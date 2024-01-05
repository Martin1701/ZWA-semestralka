<?php

/**
 * @author Martin Husár
 * @author Martin Husár <husarma1@fel.cvut.cz>
 */


/**
 * Retrieves a list of categories from the JSON file.
 *
 * @return array - An array containing categories.
 */
function getCategories()
{
    $str = file_get_contents("private/categories.json");
    return json_decode($str, true);
}
