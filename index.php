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
        <form method="get" action="./katalog/">
            <nav class="categories">
                <ul class="categories-list">
                    <?php
                    // Read the JSON file
                    $jsonData = file_get_contents('tree.json');

                    // Parse JSON data
                    $data = json_decode($jsonData, true);

                    // Check if decoding was successful
                    if ($data !== null) {
                        // Loop through each object in the JSON array
                        foreach ($data as $key => $value) {
                            // Print HTML element for each object
                            echo '<li class="categories-list-item"><button type="submit" value=' . $key . ' name="category"><svg style="width: 0"></svg><span>' . $data[$key]['label'];
                            echo '</span></button></li>';
                        }
                    }
                    ?>

                </ul>
            </nav>
        </form>
    </main>
</body>

</html>