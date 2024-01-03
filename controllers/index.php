<?php
require_once 'mustache/src/Mustache/Autoloader.php';
Mustache_Autoloader::register();
require_once('models/users.php');
require_once('models/categories.php');


function basic()
{
    $data = [];
    $data["title"] = "Main page";

    if (isset($_SESSION["id"])) {
        $data["user"] = getUser($_SESSION["id"]);
    }
    $data["categoryList"] = getCategories();


    $template = file_get_contents('views/header.mustache') . file_get_contents('views/index.mustache');
    $mustache = new \Mustache_Engine(array('entity_flags' => ENT_QUOTES));
    echo $mustache->render($template, $data);
}
