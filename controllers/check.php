<?php
require_once('models/users.php');

function basic()
{
    header("location: /~husarma1/");
    die();
}

function loginExists()
{
    if (isset($_GET["login"])) {
        if (existsUser($_GET["login"])) {
            echo json_encode(true);
        } else {
            echo json_encode(false);
        }
    } else {
        header("location: /~husarma1/");
    }
}
