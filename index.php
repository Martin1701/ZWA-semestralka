<?php

/**
 * @author Martin Husár
 * @author Martin Husár <husarma1@fel.cvut.cz>
 */

include("libs/uri_libs.php"); // function to parse uri
// Set session lifetime to 1 hour (3600 seconds)
ini_set('session.gc_maxlifetime', 3600);
session_start();

$params = $_GET["params"];

$todo = (parseUri($params)); // convert parameters to array

$action_controller = "controllers/" . $todo[0] . ".php";
// does the controller exist ?
if (file_exists($action_controller)) {
    include($action_controller);
} else {
    echo ("Non existing action controller <b>" . htmlspecialchars($todo[0]) . "</b> called.");
    exit();
}

// does the action (function) exist ?
if (function_exists($todo[1])) {
    $todo[1](); // call it
} else {
    echo ("Non existing method <b>" . htmlspecialchars($todo[1]) . "()</b> called.");
    exit();
}
