<?php
session_start() ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles.css">
    <title>Main Page</title>
    <script src="script.js"></script>
</head>

<body>
    <?php
    include "header.php";
    ?>
    <main class="main-page-container">
        <form method="get" action="./katalog/" class="categories">
            <?php
            // Read the JSON file
            $jsonData = file_get_contents('categories.json');

            // Parse JSON data
            $data = json_decode($jsonData, true);

            // Check if decoding was successful
            if ($data !== null) {
                // Loop through each object in the JSON array
                foreach ($data as $key => $value) {
                    // Print HTML element for each object
                    echo '<button type="submit" value=' . $key . ' name="category">';
                    echo '<svg class="svg-icon" viewBox="0 0 50 50" data-is-empty="false">';
                    echo '<use xlink:href="/~husarma1/svg/categories/' . $key . '.svg#' . $key . '-svg' . '"></use></svg>';
                    echo '<span>' . $data[$key]['label'];
                    echo '</span></button>';
                }
            }
            ?>
        </form>
    </main>
</body>

</html>