<?php
require_once 'mustache/src/Mustache/Autoloader.php';
Mustache_Autoloader::register();
require_once('libs/form_libs.php');
require_once('models/users.php');
require_once('models/categories.php');
require_once('models/attributes.php');
require_once('models/items.php');


function basic()
{
    header("location: ./add/");
}

// common function for add and edit item
// handles input fields
$common = function () {
    $user = getUser($_SESSION["id"]);

    $data = [];
    $data["user"] = $user;
    $data["title"] = "Add item";
    $data["categoryList"] = getCategories();

    $data["category"] = new formInput("category");
    $data["name"] = new formInput("name");
    $data["description"] = new formInput("description");
    $data["quantity"] = new formInput("quantity");

    if (isset($_POST["attrCount"])) {
        $data["attrCount"] = $_POST["attrCount"];
    } else {
        $data["attrCount"] = 1;
    }

    $remainingAttributes = [];
    foreach (getAttributes() as $group) {
        foreach ($group as $attribute => $label) {
            array_push($remainingAttributes, $attribute);
        }
    }
    // go through all attributes and make them views compatible format
    // TODO modify json to be like this
    foreach (getAttributes() as $key => $group) {
        $al["label"] = $key;
        $al["value"] = [];
        foreach ($group as $attribute => $label) {
            $al["value"][]  = ["attribute" => $attribute, "label" => $label];
        }
        $attributeList[] = $al;
    }
    $data["attributes"] = [];
    // go through number of attributes we have
    for ($i = 0; $i < $data["attrCount"]; $i++) {
        if (isset($_POST["attr" . $i]) && isset($_POST["val" . $i])) {
            // only accept one attribute of each
            if (in_array($_POST["attr" . $i], $remainingAttributes)) {
                // take attribute list and look for the selected one, then assign it selected attribute
                $al = $attributeList;
                foreach ($al as &$group) {
                    foreach ($group["value"] as &$attr) {
                        if ($attr["attribute"] == $_POST["attr" . $i]) {
                            $attr["selected"] = true;
                        }
                    }
                }
                $data["attributes"][] = ["attr" => ["name" => "attr" . $i, "value" => $_POST["attr" . $i]], "val" => ["name" => "val" . $i, "value" => ($_POST["val" . $i]) ? $_POST["val" . $i] : ""], "attributeList" => $al];
                // $data["attributes"][] = ["attribute" => $_POST["attr" . $i], "value" => $_POST["val" . $i]];
                unset($remainingAttributes[array_search($_POST["attr" . $i], $remainingAttributes)]);
            }
        }
    }

    // mark the last selected value as selected
    if ($data["category"]->value) {
        foreach ($data["categoryList"] as &$li) {
            if ($data["category"]->value == $li["category"]) {
                $li["selected"] = "selected";
            }
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST["addAttr"])) {
            // add attribute
            $data["attrCount"]++;
        } else if (isset($_POST["remAttr"])) {
            $index = (int) filter_var($_POST["remAttr"], FILTER_SANITIZE_NUMBER_INT);
            array_splice($data["attributes"], $index, 1);
            // array lost one index so all items after need to be shifted down
            for ($i = $index; $i < sizeof($data["attributes"]); $i++) {
                $data["attributes"][$i]["attr"]["name"] = "attr" . $i;
                $data["attributes"][$i]["attr"]["name"] = "val" . $i;
            }
            $data["attrCount"]--;
        }


        // validating even when user adds attribute (just to let them know)
        $data["category"]->getInput();
        $data["name"]->getInput();
        $data["description"]->getInput();
        if ($data["quantity"]->getInput()) {
            if (is_numeric($data["quantity"]->value)) {
                if ($data["quantity"]->value < 0) {
                    $data["quantity"]->notice = "Quantity can't be negative.";
                    $data["quantity"]->incorrect = true;
                }
            } else {
                $data["quantity"]->value == "";
                $data["quantity"]->notice = "Quantity must be a number.";
                $data["quantity"]->incorrect = true;
            }
        }
    }

    // always at least one attribute field
    if ($data["attrCount"] < 1) {
        $data["attrCount"] = 1;
    }
    // fill in attributes to match the amount
    while (sizeof($data["attributes"]) < $data["attrCount"]) {
        $data["attributes"][] = ["attr" => ["name" => "attr" . sizeof($data["attributes"])], "val" => ["name" => "val" . sizeof($data["attributes"])], "attributeList" => $attributeList];
    }
    return $data;
};


function add()
{
    if (!isset($_SESSION["id"])) {
        setcookie("afterLogin", "/~husarma1/" . basename(__FILE__, '.php') . "/", time() + 60 * 5, "/"); // 5 minute expiration
        header("location: /~husarma1/login/");
        die();
    }

    global $common;
    $data = $common();

    if (
        isset($_POST["submit"])
        && !isset($data["category"]->incorrect)
        && !isset($data["name"]->incorrect)
        && !isset($data["description"]->incorrect)
        && !isset($data["quantity"]->incorrect)
    ) {
        // assemble attributes to copatible pack
        foreach ($data["attributes"] as $attribute) {
            // only accept attributes with valid value
            if ($attribute["attr"]["value"] && $attribute["val"]["value"]) {
                $itemAttrs[] = ["attribute" => $attribute["attr"]["value"], "value" => $attribute["val"]["value"]];
            }
        }

        if (isset($_FILES["image"]) && $_FILES["image"]["error"] == UPLOAD_ERR_OK) {
            // TODO check file type
            //  $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);4
            $fmt = strtolower(pathinfo($_FILES["image"]['name'], PATHINFO_EXTENSION));
            $id = addItem($data["name"]->value, $_SESSION["id"], $data["category"]->value, $data["quantity"]->value, $data["description"]->value,  $itemAttrs, $fmt);
            // TODO resize image (250x250)?
            move_uploaded_file($_FILES["image"]["tmp_name"], "images/" . $id . "." . $fmt);
        } else {
            // add item without image
            $id = addItem($data["name"]->value, $_SESSION["id"], $data["category"]->value, $data["quantity"]->value, $data["description"]->value,  $itemAttrs);
        }

        header("location: /~husarma1/item/details/?id=" . $id); // redirect user to page with newly created item
        die();
    }


    $template = file_get_contents('views/header.mustache') . file_get_contents('views/itemAdd.mustache');
    $mustache = new \Mustache_Engine(array('entity_flags' => ENT_QUOTES));
    echo $mustache->render($template, $data);
}


function edit()
{
    // get item, redirect user to main page if no id is set or if the item does not exist
    if (isset($_GET["id"])) {
        $item = getItem($_GET["id"]);
        if (!isset($item)) {
            header("location: /~husarma1/");
            die();
        }
    } else {
        header("location: /~husarma1/");
        die();
    }
    // get logged user
    if (!isset($_SESSION["id"])) {
        setcookie("afterLogin", "/~husarma1/" . basename(__FILE__, '.php') . "/", time() + 60 * 5, "/"); // 5 minute expiration
        header("location: /~husarma1/login/");
        die();
    }
    $user = getUser($_SESSION["id"]);

    // check ownership
    if ($user["id"] != $item["owner"]) {
        // the user is not an owner
        header('HTTP/1.0 403 Forbidden');
        include "views/forbidden.html";
        die();
    }

    global $common;
    global $returnFileSize;
    $data = $common();
    $data["item"] = $item;


    if (isset($_POST["delete"])) {
        // delete item
        if ($item["imageFormat"]) {
            unlink(("../katalog/images/" . $item["id"] . "." . $item["imageFormat"]));
        }
        deleteItem($item["id"]);
        header("location: /~husarma1/");
        die();
    }


    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // GET request, pre-fill the form with item data
        $data["name"]->value = $item["name"];
        $data["category"]->value = $item["category"];
        $data["description"]->value = $item["description"];
        $data["quantity"]->value = $item["quantity"];

        if ($item["imageFormat"]) {
            $data["fileSize"] = $returnFileSize(filesize("images/" . $item["id"] . "." . $item["imageFormat"]));
        }

        // pre-select category
        if ($data["category"]->value) {
            foreach ($data["categoryList"] as &$li) {
                if ($data["category"]->value == $li["category"]) {
                    $li["selected"] = "selected";
                }
            }
        }
        foreach (getAttributes() as $key => $group) {
            $al["label"] = $key;
            $al["value"] = [];
            foreach ($group as $attribute => $label) {
                $al["value"][]  = ["attribute" => $attribute, "label" => $label];
            }
            $attributeList[] = $al;
        }
        // add attributes
        $i = 0;
        $data["attributes"] = [];
        foreach ($item["attributes"] as $itemAttr) {
            $al = $attributeList;
            foreach ($al as &$group) {
                foreach ($group["value"] as &$attr) {
                    if ($attr["attribute"] == $itemAttr["attribute"]) {
                        $attr["selected"] = true;
                    }
                }
            }
            $data["attributes"][] = ["attr" => ["name" => "attr" . $i, "value" => $itemAttr["attribute"]], "val" => ["name" => "val" . $i, "value" => $itemAttr["value"]], "attributeList" => $al];
            $i++;
        }
        $data["attrCount"] = $i;
    }


    if (
        isset($_POST["submit"])
        && !isset($data["category"]->incorrect)
        && !isset($data["name"]->incorrect)
        && !isset($data["description"]->incorrect)
        && !isset($data["quantity"]->incorrect)
    ) {
        // assemble attributes to copatible pack
        foreach ($data["attributes"] as $attribute) {
            // only accept attributes with valid value
            if ($attribute["attr"]["value"] && $attribute["val"]["value"]) {
                $itemAttrs[] = ["attribute" => $attribute["attr"]["value"], "value" => $attribute["val"]["value"]];
            }
        }

        if (isset($_FILES["image"]) && $_FILES["image"]["error"] == UPLOAD_ERR_OK) {
            // TODO check file type
            //  $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);4

            unlink(("images/" . $item["id"] . "." . $item["imageFormat"])); // remove old file
            $fmt = strtolower(pathinfo($_FILES["image"]['name'], PATHINFO_EXTENSION));
            $item["name"] = $data["name"]->value;
            $item["category"] = $data["category"]->value;
            $item["quantity"] = $data["quantity"]->value;
            $item["description"] = $data["description"]->value;
            $item["attributes"] = $itemAttrs;
            $item["imageFormat"] = $fmt;


            editItem($item);
            // TODO resize image (250x250)?
            move_uploaded_file($_FILES["image"]["tmp_name"], "images/" . $item["id"] . "." . $fmt);
        } else {
            // edit item without changing image
            $item["name"] = $data["name"]->value;
            $item["category"] = $data["category"]->value;
            $item["quantity"] = $data["quantity"]->value;
            $item["description"] = $data["description"]->value;
            $item["attributes"] = $itemAttrs;

            editItem($item);
        }

        header("location: /~husarma1/item/details/?id=" . $item["id"]); // redirect user to page with newly created item
        die();
    }


    $template = file_get_contents('views/header.mustache') . file_get_contents('views/itemEdit.mustache');
    $mustache = new \Mustache_Engine(array('entity_flags' => ENT_QUOTES));
    echo $mustache->render($template, $data);
}


function details()
{
    // if no ID is set, return user to main page
    if (!isset($_REQUEST["id"])) {
        header("location: /~husarma1/");
        die();
    }
    $item = getItem($_REQUEST["id"]);
    // if item does not exist, return user to main page
    if (!isset($item)) {
        header("location: /~husarma1/");
        die();
    }

    // replace attribute names with attribute labels
    // and make them correct order (like in .json file)
    foreach (json_decode(file_get_contents('private/attributes.json'), true) as $group) {
        foreach ($group as $attribute => $label) {
            foreach ($item["attributes"] as $index => $attr) {
                if ($attribute == $attr["attribute"]) {
                    $attrs[] = ["attribute" => $label, "value" => $attr["value"]];
                }
            }
        }
    }
    if (isset($attrs)) {
        $item["attributes"] = $attrs;
    }


    $data = [];
    $data["item"] = $item;
    $data["title"] = $item["name"];

    if (isset($_SESSION["id"])) {
        $data["user"] = getUser($_SESSION["id"]);
    }

    $owner = getUser($item["owner"]);
    $data["owner"] = $owner;


    $template = file_get_contents('views/header.mustache') . file_get_contents('views/itemDetails.mustache');
    $mustache = new \Mustache_Engine(array('entity_flags' => ENT_QUOTES));
    echo $mustache->render($template, $data);
}


$returnFileSize = function ($number) {
    if ($number < 1024) {
        return $number .  "bytes";
    } else if ($number >= 1024 && $number < 1048576) {
        return round($number / 1024, 1) . "KB";
    } else if ($number >= 1048576) {
        return round($number / 1048576) . "MB";
    }
};
