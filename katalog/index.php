<?php
session_start();
include_once "./items.php";
include_once "../users.php";


// Function to stringify item
function stringifyItem($object)
{
    $excluded = ["id", "owner", "imageFormat", "category", "quantity"];
    $values = [];
    foreach ($object as $key => $value) {
        if (!in_array($key, $excluded)) {
            if (is_array($value)) {
                $values[] = stringifyItem($value); // Recursively handle nested arrays
            } else {
                $values[] = $value;
            }
        }
    }
    return strtolower(implode(" ", $values));
}




if (isset($_GET["q"])) {
    $items = [];
    $q = strtolower($_GET["q"]);
    foreach (listItems() as &$item) {
        if (str_contains(stringifyItem($item), $q)) {
            $items[] = $item; // item matches the search, add
        }
    }
    if (sizeof($items) == 1) {
        // we found exactly 1 match, so jump right to it
        header("location: ../katalog/item/?id=" . $items[0]["id"]);
        die();
    }
} else {
    $items = listItems();
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <title>Katalog</title>
    <script src="../script.js"></script>
</head>

<body>
    <?php
    include "../header.php";
    ?>
    <main>
        <ul class="searchResult">
            <?php
            foreach ($items as &$item) {
                echo "<li>";
                if ($item["imageFormat"]) {
                    echo "<img src=\"./images/" . $item["id"] . "." . $item["imageFormat"] . "\" alt=\"" . htmlspecialchars($item["name"]) . " image\">";
                } else {
                    echo "<img src=\"./images/noImage.png\" alt=\"No image\">";
                }
                echo "<a href=\"/~husarma1/katalog/item/?id=" . $item["id"] . "\">" .  htmlspecialchars($item["name"], ENT_QUOTES) . "</a>";
                echo "<p>Quantity: " . $item["quantity"] . " pcs</p>";
                echo "</li>";
            }
            ?>
        </ul>
    </main>
</body>

</html>