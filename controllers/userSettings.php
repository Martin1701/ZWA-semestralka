<?php

/**
 * @author Martin Husár
 * @author Martin Husár <husarma1@fel.cvut.cz>
 */
require_once 'mustache/src/Mustache/Autoloader.php';
Mustache_Autoloader::register();
require_once "models/users.php";
require_once "models/categories.php";
require_once "libs/form_libs.php";

/**
 * Handles user account edit.
 * If values user wishes to change are correct, edits the account.
 * 
 * @return void
 */
function basic()
{
    if (!isset($_SESSION["id"])) {
        setcookie("afterLogin", "/~husarma1/" . basename(__FILE__, '.php') . "/", time() + 60 * 5, "/"); // 5 minute expiration
        header("location: /~husarma1/login/");
        die();
    }


    $data = [];
    $data["title"] = "User settings";
    $user = getUser($_SESSION["id"]);
    $data["user"] = $user;

    $fName = new formInput("fName");
    $lName = new formInput("lName");
    $login = new formInput("login");
    $passwordCur = new formInput("passwordCur");
    $password = new formInput("password");
    $password2 = new formInput("password2");




    if (isset($_POST["submit"])) {
        $changesMade = false;
        if ($fName->getInput() && $fName->value != $user["fName"]) {
            $changesMade = true;
            $user["fName"] = $fName->value;
        }
        if ($lName->getInput() && $lName->value != $user["lName"]) {
            $changesMade = true;
            $user["lName"] = $lName->value;
        }
        if ($login->getInput() && $login->value != $user["login"]) {
            $login->notice = validate_input($login->value, 4, 30, "Login");
            // login is valid, check if the user already exists
            if (!$login->notice) {
                if (getUserByLogin($login->value)) {
                    $login->incorrect = true;
                    $login->notice = "Username already in use.";
                } else {
                    $changesMade = true;
                    $user["login"] = $login->value;
                }
            }
        }
        // new password is only validated when user gives the current one
        $passwordCur->getInput();
        $password->getInput();
        $password2->getInput();
        if ($passwordCur->value || $password->value || $password2->value) {
            if (!password_verify($passwordCur->value, $user["password"])) {
                $passwordCur->notice = "Incorrect password.";
                $passwordCur->incorrect = true;
            }
            if ($password->value) {
                $password->notice = validate_input($password->value, 8, 30, "Password");
                if ($password->notice) {
                    $password->incorrect = true;
                } else if (password_verify($password->value, $user["password"])) {
                    $password->notice = "New password can't be same as old one.";
                    $password->incorrect = true;
                }
            }
            if ($password2->value) {
                if ($password->value == $password2->value) {
                    if (!$passwordCur->incorrect && !$password->incorrect) {
                        $changesMade = true;
                        $user["password"] = password_hash($password->value, PASSWORD_DEFAULT);
                    }
                } else {
                    $password2->notice = "Passwords do not match.";
                    $password2->incorrect = true;
                }
            }
        } else {
            // if neither of the password inputs got any data, dont show anything ->
            // the user does not want to edit his password (probably)
            $passwordCur->notice = "";
            unset($passwordCur->incorrect);
            $password->notice = "";
            unset($password->incorrect);
            $password2->notice = "";
            unset($password2->incorrect);
        }

        // checking for incorrect because original value is always valid and I don't want to check it each time
        if (
            !isset($fName->incorrect)
            && !isset($lName->incorrect)
            && !isset($login->incorrect)
            && !isset($passwordCur->incorrect)
            && !isset($password->incorrect)
            && !isset($password2->incorrect)
        ) {
            if ($changesMade) {
                editUser($user);
            }
            header("location: /~husarma1/");
            die();
        }
    } else {
        $fName->value = $user["fName"];
        $lName->value = $user["lName"];
        $login->value = $user["login"];
    }

    $passwordCur->value = "";
    $password->value = "";
    $password2->value = "";
    $data["fName"] = $fName;
    $data["lName"] = $lName;
    $data["login"] = $login;
    $data["passwordCur"] = $passwordCur;
    $data["password"] = $password;
    $data["password2"] = $password2;


    $template = file_get_contents('views/header.mustache') . file_get_contents('views/userSettings.mustache') . file_get_contents('views/footer.mustache');
    $mustache = new \Mustache_Engine(array('entity_flags' => ENT_QUOTES));
    echo $mustache->render($template, $data);
}
