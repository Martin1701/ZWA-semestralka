<?php

require_once 'mustache/src/Mustache/Autoloader.php';
Mustache_Autoloader::register();
require_once "models/users.php";
require_once "models/categories.php";
require_once "libs/form_libs.php";


function basic()
{
    $data = [];
    $data["title"] = "Register";

    $fName = new formInput("fName");
    $lName = new formInput("lName");
    $login = new formInput("login");
    $password = new formInput("password");
    $password2 = new formInput("password2");

    if (isset($_POST["submit"])) {
        if ($fName->getInput()) {
            $fName->valid = true;
        }
        if ($lName->getInput()) {
            $lName->valid = true;
        }
        if ($login->getInput()) {
            $login->notice = validate_input($login->value, 4, 30, "Login");
            if ($login->notice) {
                $login->incorrect = true;
            } else if (getUserByLogin($login->value)) {
                $login->incorrect = true;
                $login->notice = "Username already in use.";
            } else {
                $login->notice = "Username available.";
                $login->valid = true;
            }
        }
        if ($password->getInput()) {
            $password->notice = validate_input($password->value, 8, 30, "Password");
            if ($password->notice) {
                $password->incorrect = true;
            } else {
                $password->valid = true;
            }
        }
        if ($password2->getInput()) {
            if ($password->value == $password2->value) {
                $password2->valid = true;
            } else {
                $password2->notice = "Passwords do not match.";
                $password2->incorrect = true;
            }
        }
    }

    if (
        $fName->valid &&
        $lName->valid &&
        $login->valid &&
        $password->valid &&
        $password2->valid
    ) {
        $h = password_hash($password->value, PASSWORD_DEFAULT);
        $_SESSION["id"] = addUser($fName->value, $lName->value, $login->value, $h);
        // redirect user to main page
        header("location: ../");
        die();
    }


    $password->value = "";
    $password2->value = "";
    $data["fName"] = $fName;
    $data["lName"] = $lName;
    $data["login"] = $login;
    $data["password"] = $password;
    $data["password2"] = $password2;


    // if user tries to register while logged-in (normally not visible)
    // log him out and redirect back to register page
    if (isset($_SESSION["id"])) {
        setcookie("afterLogout", "/~husarma1/" . basename(__FILE__, '.php') . "/", time() + 60 * 5, "/"); // 5 minute expiration
        header("location: /~husarma1/logout/");
        die();
    }



    $template = file_get_contents('views/header.mustache') . file_get_contents('views/register.mustache');
    $mustache = new \Mustache_Engine(array('entity_flags' => ENT_QUOTES));
    echo $mustache->render($template, $data);
}
