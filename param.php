<?php
    if (isset($_GET["jmeno"]) && $_GET["prijmeni"]) {
    echo "ahoj " . $_GET["jmeno"] . " " . $_GET["prijmeni"];
    } else {
        echo ".";
    }
?>