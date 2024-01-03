<?php
// Set session lifetime to 1 hour (3600 seconds)
ini_set('session.gc_maxlifetime', 3600);
session_start();
// front controller bude vzdy zavolan, protoze Apache prepise veskera url
// ve tvaru /aaa/bbb/ccc
// na tvar  index.php?params=aaa/bbb/ccc`
// takze staci se podivat, co chceme

// rekneme, ze prvni param je jmeno action controlleru
const PARAMS = 'params';
const ACTION_CONTROLLERS_DIR = "controllers";

$params = $_GET[PARAMS]; //vime, ze cela cesta se prevedla na parametry`
include("libs/uri_libs.php"); // vlozime funkce pro parsonavi parametru

$todo = (parseUri($params)); // prevede parametry na pole 
// ted vime, ktery action controller se ma zavolat
// je to v $todo[0];
// a vime, jaka akce se ma zavolat
// je to $todo[1]
// a zname vsechny dalsi parametry, jsou to $todo s indexem > 1 
// pro zavolani vypisu kluku budeme tedy volat /people/boys
// a pro zavolani vypisu devcat budeme volat /people/girls


$action_controller = ACTION_CONTROLLERS_DIR . "/" . $todo[0] . ".php";
// kontrola, zda pozadovany action controller opravdu existuje
if (file_exists($action_controller)) {
    include($action_controller);
} else {
    echo ("Non existing action controller {$todo[0]} called.");
    exit();
}

// kontrola, zda pozadovana akce v danem action controlleru existuje
if (function_exists($todo[1])) {
    $todo[1](); // pokud ano, zavolame ji
} else {
    //TODO redirect user to basic() ?
    echo ("Non existing method <b>" . htmlspecialchars($todo[1]) . "()</b> called.");
    exit();
}
