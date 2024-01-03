<?php

function getAttributes()
{
    $str = file_get_contents("private/attributes.json");
    return json_decode($str, true);
}
