<?php
$q = "";
if (isset($_POST["q"])) {
    $q = $_POST["q"];
    if (strlen($q) >= 3) {
        header("location: ../");
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <form action=<?php echo $_SERVER['PHP_SELF']; ?> method="post">
        <input type="text" name="q" required value="<?php echo htmlspecialchars($q, ENT_QUOTES) ?>">
        <input type="submit">
    </form>
</body>


</html>