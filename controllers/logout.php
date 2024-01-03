<?php

function basic()
{
    unset($_SESSION["id"]);
    // redirect user to main page
    if (isset($_COOKIE["afterLogout"])) {
        // if user comes from page that redirected him to logout, return to that page
        header("location: " . $_COOKIE["afterLogout"]);
        setcookie("afterLogout", "", -1, "/");
    } else {
        header("location: ../");
    }
    die();
}
