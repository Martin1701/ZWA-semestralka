<?php

function getCategories()
{
    $str = file_get_contents("private/categories.json");
    return json_decode($str, true);
}
