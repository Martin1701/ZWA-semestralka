<?php

/**
 * @author Martin Husár
 * @author Martin Husár <husarma1@fel.cvut.cz>
 */

/**
 * Checks if a user with the given login exists.
 *
 * @param string $login - The login to check.
 * @return bool - True if the user exists, false otherwise.
 */
function existsUser($login)
{
    foreach (listUsers() as &$user) {
        if ($user["login"] == $login) {
            return true;
        }
    }
    return false;
}

/**
 * Retrieves a list of all users from the JSON database.
 *
 * @return array - An array containing user information.
 */
function listUsers()
{
    $str = file_get_contents("db/users.json");
    return json_decode($str, true);
}

/**
 * Retrieves user information based on the user's ID.
 *
 * @param string $id - The ID of the user to retrieve.
 * @return array|null - An array containing user information or null if the user is not found.
 */
function getUser($id)
{
    foreach (listUsers() as &$user) {
        if ($user["id"] == $id) {
            return $user;
        }
    }
}

/**
 * Retrieves user information based on the user's login.
 *
 * @param string $login - The login of the user to retrieve.
 * @return array|null - An array containing user information or null if the user is not found.
 */
function getUserByLogin($login)
{
    foreach (listUsers() as &$user) {
        if ($user["login"] == $login) {
            return $user;
        }
    }
}

/**
 * Saves the updated list of users to the JSON database.
 *
 * @param array $users - An array containing user information to be saved.
 */
function saveUsers($users)
{
    $str = json_encode($users);
    file_put_contents("db/users.json", $str);
}

/**
 * Adds a new user to the database.
 *
 * @param string $fName - First name of the user.
 * @param string $lName - Last name of the user.
 * @param string $login - Login of the user.
 * @param string $password - Password of the user.
 * @param string|null $id - ID of the user (auto-generated if not provided).
 * @return string - The ID of the newly added user.
 */
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

/**
 * Deletes a user from the database based on the user's ID.
 *
 * @param string $id - The ID of the user to be deleted.
 */
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

/**
 * Edits an existing user's information in the database.
 *
 * @param array $user - An array containing updated user information.
 */
function editUser($user)
{
    $all = listUsers();
    foreach ($all as &$value) {
        if ($value["id"] == $user["id"]) {
            $value = $user;
            saveUsers($all);
            return;
        }
    }
}
