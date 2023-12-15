<?php
session_start();
include_once "/home/husarma1/www/users.php";
$login = "";
$password = "";

if (isset($_SESSION["id"])) {
    if (getUser($_SESSION["id"])) {
        header("location: ../");
        die();
    }
}

if (isset($_POST["login"])) {
    $login = $_POST["login"];
}
if (isset($_POST["password"])) {
    $password = $_POST["password"];
}


if (!isset($_SESSION["id"])) {
    if (existsUser($login)) {
        $user = getUserByLogin($login);
        if (password_verify($password, $user["password"])) {
            $_SESSION["id"] = $user["id"];
            header("location: ../");
            die();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <title>Login</title>
    <script src="../script.js"></script>
</head>

<body>
    <?php
    include "../header.php";
    ?>
    <h1>Log in</h1>
    <form action=<?php echo $_SERVER['PHP_SELF']; ?> method="post">
        <input type="text" name="login" id="login" required value="<?php echo $login ?>">
        <input type="password" name="password" required value="<?php echo $password ?>">
        <input type="submit" value="Log in">
    </form>
</body>

</html>