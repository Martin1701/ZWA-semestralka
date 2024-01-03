<?php
require_once 'mustache/src/Mustache/Autoloader.php';
Mustache_Autoloader::register();
require_once('models/users.php');
require_once('models/categories.php');
require_once('models/items.php');


// Function to stringify item
$stringifyItem = function ($object) {
    global $stringifyItem;
    $excluded = ["id", "owner", "imageFormat", "category", "quantity"];
    $values = [];
    foreach ($object as $key => $value) {
        if (!in_array($key, $excluded)) {
            if (is_array($value)) {
                foreach ($value as $attr) {
                    // only copy values, not attribute names
                    $values[] = $attr["value"];
                }
            } else {
                $values[] = $value;
            }
        }
    }
    return strtolower(implode(" ", $values));
};


function basic()
{
    global $stringifyItem;
    $data = [];
    $data["title"] = "Katalog";

    if (isset($_SESSION["id"])) {
        $data["user"] = getUser($_SESSION["id"]);
    }






    if (isset($_GET["q"])) {
        $data["q"] = $_GET["q"];
        $items = [];
        $q = strtolower($_GET["q"]);
        foreach (listItems() as &$item) {
            if (str_contains($stringifyItem($item), $q)) {
                $items[] = ["item" => $item]; // item matches the search, add
            }
        }
        if (sizeof($items) == 1) {
            // we found exactly 1 match, so jump right to it
            header("location: ../item/details/?id=" . $items[0]["item"]["id"]);
            die();
        }
    } else {
        $items = listItems();
    }
    $data["listItems"] = $items;




    $template = file_get_contents('views/header.mustache') . file_get_contents('views/katalog.mustache');
    $mustache = new \Mustache_Engine(array('entity_flags' => ENT_QUOTES));
    echo $mustache->render($template, $data);
}
