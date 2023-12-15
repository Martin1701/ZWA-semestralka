<?php
$color = "white";
if (isset($_COOKIE["color"])) {
    $color = $_COOKIE["color"];
}

if (isset($_GET["color"])) {
    $color = $_GET["color"];
    setcookie("color", $color, 253402300799);
}
?>

<body>
    <form action=<?php echo $_SERVER['PHP_SELF']; ?> method="get">
        <input type="radio" name="color" value="white" id="white">
        <label for="white">white</label>
        <br>
        <input type="radio" name="color" value="red" id="red">
        <label for="red">red</label>
        <br>
        <input type="radio" name="color" value="green" id="green">
        <label for="green">green</label>
        <br>
        <input type="radio" name="color" value="blue" id="blue">
        <label for="blue">blue</label>
        <br>
        <input type="radio" name="color" value="black" id="black">
        <label for="black">black</label>
        <br>
        <input type="submit">
    </form>
    <div style="height: 100px; width: 100px; background-color: <?php echo $color ?>;">
</body>