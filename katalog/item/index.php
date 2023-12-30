<?php
session_start();
include_once "../items.php";
include_once "../../users.php";


// if no ID is set, return user to main page
if (!isset($_REQUEST["id"])) {
    header("location: ../../");
    die();
}
$item = getItem($_REQUEST["id"]);
// if item does not exist, return user to main page
if (!$item) {
    header("location: ../../");
    die();
}

$owner = getUser($item["owner"]);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../styles.css">
    <title><?php echo htmlspecialchars($item["name"], ENT_QUOTES); ?></title>
    <script src="../../script.js"></script>
</head>

<body>
    <?php
    include "../../header.php";

    ?>
    <main>
        <section class="item-info">
            <h1><?php echo htmlspecialchars($item["name"], ENT_QUOTES) ?></h1>
            <p><?php echo htmlspecialchars($item["description"], ENT_QUOTES) ?></p>
            <?php
            if ($item["imageFormat"]) {
                echo "<img src=\"../images/" . $item["id"] . "." . $item["imageFormat"] . "\" alt=\"" . htmlspecialchars($item["name"]) . " image\">";
            } else {
                echo "<img src=\"../images/noImage.png\" alt=\"No image\">";
            }
            ?>

            <table>
                <tbody>
                    <tr>
                        <td>Owned by: </td>
                        <td><?php echo htmlspecialchars($owner["fName"] . " " .  $owner["lName"], ENT_QUOTES) ?></td>
                    </tr>
                    <tr>
                        <td>Quantity: </td>
                        <td><?php echo htmlspecialchars($item["quantity"], ENT_QUOTES) ?>pcs</td>
                    </tr>
                    <?php
                    foreach (json_decode(file_get_contents('../../attributes.json'), true) as $group) {
                        foreach ($group as $attribute => $label) {
                            foreach ($item["attributes"] as $index => $attr) {
                                if ($attribute == $attr[0]) {
                                    echo "<tr><td>" . htmlspecialchars($label . ":", ENT_QUOTES)  . "</td><td>" . htmlspecialchars($attr[1], ENT_QUOTES) . "</td></tr>";
                                }
                            }
                        }
                    }
                    ?>
                    <tr>
                        <td><a href=<?php echo "\"../../add/?id=" . urlencode($item["id"]) . "&edit\""; ?>>Edit</a></td>
                        <td>(requires ownership)</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </main>
</body>

</html>