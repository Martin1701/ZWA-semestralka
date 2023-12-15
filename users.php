<?php

// AJAX request reply
if (isset($_GET["login"])) {
    if (existsUser($_GET["login"])) {
        echo json_encode(true);
    } else {
        echo json_encode(false);
    }
}

// *** database manipulation functions ***

function existsUser($login)
{
    foreach (listUsers() as &$user) {
        if ($user["login"] == $login) {
            return true;
        }
    }
    return false;
}

function listUsers()
{
    $str = file_get_contents("/home/husarma1/www/users.json");
    return json_decode($str, true);
}

function getUser($id)
{
    foreach (listUsers() as &$user) {
        if ($user["id"] == $id) {
            return $user;
        }
    }
}
function getUserByLogin($login)
{
    foreach (listUsers() as &$user) {
        if ($user["login"] == $login) {
            return $user;
        }
    }
}
function saveUsers($users)
{
    $str = json_encode($users);
    file_put_contents("/home/husarma1/www/users.json", $str);
}

function addUser($fName, $lName, $login, $password, $id = null)
{
    $all = listUsers();
    if (!$id) {
        $id = uniqid();
    }
    $user = ["id" => $id, "fName" => $fName, "lName" => $lName, "login" => $login, "password" => $password];
    $all[] = $user;
    saveUsers($all);
    return $id;
}

function deleteUser($id)
{
    $all = listUsers();
    foreach ($all as $key => &$user) {
        if ($user["id"] == $id) {
            unset($all[$key]);
            saveUsers($all);
            return;
        }
    }
}

function editUser($id, $name, $password)
{
    // TODO implement
    // deleteUser($id);
    // add new user, but with the same ID
    // addUser($name, $password, $id);
}
