<?php

/**
 * @author Martin Husár
 * @author Martin Husár <husarma1@fel.cvut.cz>
 */

/**
 * Logs-out the user.
 * If user is redirected to logout from another page and afterLogout cookie is set, redirects him to that page, otherwise redirects to main.
 * 
 * @return void
 */
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
