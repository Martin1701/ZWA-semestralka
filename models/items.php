<?php

/**
 * @author Martin Husár
 * @author Martin Husár <husarma1@fel.cvut.cz>
 */

/**
 * Retrieves a list of all items from the JSON database.
 *
 * @return array - An array containing item information.
 */
function listItems()
{
    $str = file_get_contents("/home/husarma1/www/db/items.json");
    return json_decode($str, true);
}

/**
 * Retrieves item information based on the item's ID.
 *
 * @param string $id - The ID of the item to retrieve.
 * @return array|null - An array containing item information or null if the item is not found.
 */
function getItem($id)
{
    foreach (listItems() as &$item) {
        if ($item["id"] == $id) {
            return $item;
        }
    }
}

/**
 * Saves the updated list of items to the JSON database.
 *
 * @param array $items - An array containing item information to be saved.
 */
function saveItems($items)
{
    $str = json_encode($items);
    file_put_contents("/home/husarma1/www/db/items.json", $str);
}

/**
 * Adds a new item to the database.
 *
 * @param string $name - Name of the item.
 * @param string $owner - Owner of the item.
 * @param string $category - Category of the item.
 * @param int $quantity - Quantity of the item.
 * @param string $description - Description of the item.
 * @param array $attributes - Array of arrays containing attributes (e.g., [["attribute name", "attribute value"]]).
 * @param string $imageFormat - Format of the item's image.
 * @return string - The ID of the newly added item.
 */
function addItem($name, $owner, $category, $quantity, $description, $attributes = [], $imageFormat = "")
{
    $all = listItems();
    $id = uniqid();
    $item = [
        "id" => $id,
        "owner" => $owner,
        "name" => $name,
        "category" => $category,
        "quantity" => $quantity,
        "description" => $description,
        "attributes" => $attributes,
        "imageFormat" => $imageFormat
    ];
    $all[] = $item;
    saveItems($all);
    return $id;
}

/**
 * Deletes an item from the database based on the item's ID.
 *
 * @param string $id - The ID of the item to be deleted.
 */
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

/**
 * Edits an existing item's information in the database.
 *
 * @param array $item - An array containing updated item information.
 */
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
