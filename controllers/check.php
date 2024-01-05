<?php

/**
 * @author Martin Husár
 * @author Martin Husár <husarma1@fel.cvut.cz>
 */
require_once('models/users.php');

/**
 * Redirects to the home page.
 *
 * @return void
 */
function basic()
{
    header("location: /~husarma1/");
    die();
}
/**
 * Checks if a login exists and sends a JSON response.
 * Redirects to the home page if the login parameter is not set.
 *
 * @return void
 */
function loginExists()
{
    if (isset($_GET["login"])) {
        if (existsUser($_GET["login"])) {
            echo json_encode(true);
        } else {
            echo json_encode(false);
        }
    } else {
        header("location: /~husarma1/");
    }
}
