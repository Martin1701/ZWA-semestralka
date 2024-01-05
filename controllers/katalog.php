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
$compareByName = function ($a, $b) {
    return strcmp($a['item']['name'], $b['item']['name']);
};

function basic()
{
    global $stringifyItem;
    $data = [];
    $data["title"] = "Katalog";

    if (isset($_SESSION["id"])) {
        $data["user"] = getUser($_SESSION["id"]);
    }

    // category or query is set
    if (!isset($_GET["category"]) && !isset($_GET["q"])) {
        header("location: /~husarma1/");
        die();
    }
    // query is set, is it long enough
    if (isset($_GET["q"]) && strlen($_GET["q"]) < 2) {
        header("location: /~husarma1/");
        die();
    }
    $allCategories = [];
    foreach (getCategories() as $category) {
        $allCategories[] = $category["category"];
    }
    // category is set, is it one of valid ones
    if (isset($_GET["category"]) &&  !in_array($_GET["category"], $allCategories)) {
        header("HTTP/1.0 404 Not Found");
        include "views/notFound.html";
        die();
    }



    // go through query if it is set
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

        // create links to categories that have these items and show their count
        $inCategorires = [];
        foreach ($items as &$item) {
            if (isset($inCategorires[$item["item"]["category"]])) {
                $inCategorires[$item["item"]["category"]]++;
            } else {
                $inCategorires[$item["item"]["category"]] = 1;
            }
        }
    }

    // go through items and only accept the ones that have the desired category
    if (isset($_GET["category"])) {
        if (isset($items)) {
            // there was already an search
            foreach ($items as $key => &$item) {
                // remove items with not matching category
                if ($_GET["category"] != $item["item"]["category"]) {
                    array_splice($items, $key, 1);
                }
            }
        } else {
            // no search, just get all of the items from category
            foreach (listItems() as &$item) {
                if ($_GET["category"] === $item["category"]) {
                    $items[] = ["item" => $item];
                }
            }
        }
    } else {
        foreach (getCategories() as &$category) {
            // if we found items in that category
            if (isset($inCategorires[$category["category"]])) {
                // add it to array to show it
                $category["count"] = $inCategorires[$category["category"]];
                $data["inCategories"][] = $category;
            }
        }
    }

    if (isset($_GET["category"])) {
        foreach (getCategories() as $category) {
            if ($_GET["category"] == $category["category"]) {
                $data["category"] = $category["label"];
                break;
            }
        }
    }
    if (isset($items)) {
        // Custom comparison function for sorting
        global $compareByName;

        // Sort the array using the custom comparison function
        usort($items, $compareByName);
        $perPage = 9;

        if (isset($_GET["page"])) {
            $data["page"] = $_GET["page"];
            if ($data["page"] < 1) {
                // handle negative pages
                $data["page"] = 1;
            }
            $index = ($data["page"] - 1) * $perPage;
            $data["listItems"] = array_slice($items, $index, $perPage);
        } else {
            $data["listItems"] = array_slice($items, 0, $perPage);
            $data["page"] = 1;
        }

        if ($data["page"] != 1) {
            $data["prevPage"] = $data["page"] - 1;
        }
        $data["maxPage"] = ceil(sizeof($items) / $perPage);
        if ($data["page"] < $data["maxPage"]) {
            $data["nextPage"] = $data["page"] + 1;
        }

        $data["itemCount"] = sizeof($items);
    } else {
        $data["itemCount"] = 0;
    }
    $data["pageRequest"] = (isset($data["q"]) ? "q=" . $data["q"] . "&" : "") . (isset($_GET["category"]) ? "category=" . $_GET["category"] . "&" : "");

    $template = file_get_contents('views/header.mustache') . file_get_contents('views/katalog.mustache') . file_get_contents('views/footer.mustache');
    $mustache = new \Mustache_Engine(array('entity_flags' => ENT_QUOTES));
    echo $mustache->render($template, $data);
}
