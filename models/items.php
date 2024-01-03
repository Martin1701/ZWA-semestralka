<?php

// *** database manipulation functions ***

function listItems()
{
    $str = file_get_contents("/home/husarma1/www/db/items.json");
    return json_decode($str, true);
}
function getItem($id)
{
    foreach (listItems() as &$item) {
        if ($item["id"] == $id) {
            return $item;
        }
    }
}

function saveItems($items)
{
    $str = json_encode($items);
    file_put_contents("/home/husarma1/www/db/items.json", $str);
}
// $attributes -> array of arrays containing: ["attribute name", "attribute value"]
// returns items id
function addItem($name, $owner, $category, $quantity, $description,  $attributes, $imageFormat = "")
{
    $all = listItems();
    $id = uniqid();
    $item = ["id" => $id, "owner" => $owner, "name" => $name, "category" => $category, "quantity" => $quantity, "description" => $description, "attributes" => $attributes, "imageFormat" => $imageFormat];
    $all[] = $item;
    saveItems($all);
    return $id;
}

function deleteItem($id)
{
    $all = listItems();
    foreach ($all as $key => &$item) {
        if ($item["id"] == $id) {
            unset($all[$key]);
            saveItems($all);
            return;
        }
    }
}

function editItem($item)
{
    $all = listItems();
    foreach ($all as $key => &$value) {
        if ($value["id"] == $item["id"]) {
            $all[$key] = $item;
            saveItems($all);
            return;
        }
    }
}
