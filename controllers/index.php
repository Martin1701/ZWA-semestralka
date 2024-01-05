<?php

/**
 * @author Martin Husár
 * @author Martin Husár <husarma1@fel.cvut.cz>
 */
require_once 'mustache/src/Mustache/Autoloader.php';
Mustache_Autoloader::register();
require_once('models/users.php');
require_once('models/categories.php');

/**
 * Retrieves list of categories and shows them on main page.
 * 
 * @return void
 */
function basic()
{
    $data = [];
    $data["title"] = "Main page";

    if (isset($_SESSION["id"])) {
        $data["user"] = getUser($_SESSION["id"]);
    }
    $data["categoryList"] = getCategories();


    $template = file_get_contents('views/header.mustache') . file_get_contents('views/index.mustache') . file_get_contents('views/footer.mustache');
    $mustache = new \Mustache_Engine(array('entity_flags' => ENT_QUOTES));
    echo $mustache->render($template, $data);
}
