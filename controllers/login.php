<?php

require_once 'mustache/src/Mustache/Autoloader.php';
Mustache_Autoloader::register();
require_once "models/users.php";
require_once "libs/form_libs.php";


function basic()
{

    if (isset($_SESSION["id"])) {
        if (getUser($_SESSION["id"])) {
            header("location: ../"); // main page
            die();
        }
    }
    $data = [];

    $login = new formInput("login");
    $password = new formInput("password");

    if (isset($_POST["submit"])) {
        if ($login->getInput()) {
            // check if the user exists
            $user = getUserByLogin($login->value);
            if (!isset($user)) {
                $login->incorrect = true;
                $login->notice = "This user does not exist.";
            }
        }

        if ($password->getInput() && isset($user)) {
            // if user extist, verify password
            if (password_verify($password->value, $user["password"])) {
                $_SESSION["id"] = $user["id"];
                if (isset($_COOKIE["afterLogin"])) {
                    // if user comes from page that redirected him to login, return to that page
                    header("location: " . $_COOKIE["afterLogin"]);
                    setcookie("afterLogin", "", -1, "/");
                } else {
                    header("location: ../");
                }
                die();
            } else {
                $password->incorrect = true;
                $password->notice = "Incorrect password.";
            }
        }
    }
    $password->value = "";
    $data["login"] = $login;
    $data["password"] = $password;

    $data["title"] = "Log in";



    $template = file_get_contents('views/header.mustache') . file_get_contents('views/login.mustache');
    $mustache = new \Mustache_Engine(array('entity_flags' => ENT_QUOTES));
    echo $mustache->render($template, $data);
}
