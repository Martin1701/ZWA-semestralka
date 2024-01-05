<?php

/**
 * @author Martin Husár
 * @author Martin Husár <husarma1@fel.cvut.cz>
 */


/**
 * Retrieves a list of attributes from the JSON file.
 *
 * @return array - An array containing attributes.
 */
function getAttributes()
{
    $str = file_get_contents("private/attributes.json");
    return json_decode($str, true);
}
