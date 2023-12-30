<?php
session_start();
include_once "/home/husarma1/www/users.php";
include_once "/home/husarma1/www/inputClasses.php";
include_once "../katalog/items.php";

if (!isset($_SESSION["id"])) {
    setcookie("afterLogin", $_SERVER['REQUEST_URI'], time() + 60 * 60 * 1, "/"); // 1 hour expiration
    header("location: ../login/");
    die();
}

// requested edit of item with this id
if (isset($_REQUEST["id"])) {

    $item = getItem($_REQUEST["id"]);
    // if item does not exist, continue like adding new part
    if ($item) {
        if ($_SESSION["id"] != $item["owner"]) {
            // the user is not an owner of this item (only owner can edit)
            header("HTTP/1.1 403 Forbidden");
            echo "<!DOCTYPE HTML PUBLIC -//IETF//DTD HTML 2.0//EN\"><html lang=\"en\"><head><title>403 Forbidden</title></head><body><h1>Forbidden</h1><p>You don't have permission to access this resource.</p><hr></body></html>";
            die('Forbidden');
        } else if (isset($_REQUEST["remove"])) {
            unlink(("../katalog/images/" . $item["id"] . "." . $item["imageFormat"]));
            deleteItem($item["id"]);
            header("location: ../");
            die();
        }
    }
}


$user = getUser($_SESSION["id"]);

class itemInput
{
    public $value;              // pre-filled value (or user submitted)
    public $notice;             // notice to display
    public $name;               // name of the element
    private $label;             // input label
    public $valid;              // if input is valid or not
    public $isNumber;           // if the input is number =true
    function __construct($name, $label, $isNumber = false)
    {
        $this->name = $name;
        $this->label = $label;
        $this->valid = false;
        $this->isNumber = $isNumber;
    }
    // returns string that can be inserted directly into HTML
    function get_html()
    {
        $element = "<label for=\"" . $this->name . "\">" . $this->label . "</label>";
        $element .= "<input type=\"" . (($this->isNumber) ? "number" : "text") . "\" name=\"" . $this->name . "\" id=\"" . $this->name . "\"";
        if ($this->name == "login" && $this->valid) {
            $element .= "class=\"correctInput\"";
        } else {
            $element .= (($this->notice) ? "class=\"incorrectInput\"" : "");
        }

        $element .= " value=\"" . htmlspecialchars($this->value, ENT_QUOTES) . "\"";
        $element .= "><p class=\"" . (($this->valid) ? "correctText" : "incorrectText") . "\">" . $this->notice . "</p>";
        return $element;
    }
    function getInput()
    {
        // first, get value from POST
        if (isset($_POST[$this->name]) && $_POST[$this->name]) {
            $this->value = $_POST[$this->name];
            return true;
        } else {
            $this->notice = "This field is obligatory.";
            return false;
        }
    }
}


$category = new optionsInput("category", "Category of the item", "Categories", file_get_contents('../categories.json'));
$name = new itemInput("name", "Name");
$description = new itemInput("description", "Description");
$quantity = new itemInput("quantity", "Quantity", true);


$attributes = [];
$attrCount = 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST["attrCount"])) {
        $attrCount = $_POST["attrCount"];
    }
    $remainingAttributes = [];
    foreach (json_decode(file_get_contents('../attributes.json'), true) as $group) {
        foreach ($group as $attribute => $label) {
            array_push($remainingAttributes, $attribute);
        }
    }
    // go through number of attributes we have
    for ($i = 0; $i < $attrCount; $i++) {
        if (isset($_POST["attr" . $i]) && isset($_POST["val" . $i])) {
            // if no value is set the attribute field will be ignored
            if ($_POST["attr" . $i] && $_POST["val" . $i]) {
                // only accept one attribute of each
                if (in_array($_POST["attr" . $i], $remainingAttributes)) {
                    array_push($attributes, [$_POST["attr" . $i], $_POST["val" . $i]]);
                    unset($remainingAttributes[array_search($_POST["attr" . $i], $remainingAttributes)]);
                }
            }
        }
    }

    if ($category->getInput()) {
        $category->valid = true;
    }
    if ($name->getInput()) {
        $name->valid = true;
    }
    if ($description->getInput()) {
        $description->valid = true;
    }
    if ($quantity->getInput()) {
        if (is_numeric($quantity->value)) {
            if ($quantity->value > 0) {
                $quantity->valid = true;
            } else {
                $quantity->notice = "Quantity can't be negative.";
            }
        } else {
            $quantity->value = "";
            $quantity->notice = "Quantity must be a number.";
        }
    }
    if (isset($_POST["addAttr"])) {
        // add attribute
        $attrCount++;
    } else if (isset($_POST["remAttr"])) {
        array_splice($attributes, $_POST["remAttr"], 1);
        $attrCount--;
    }




    if (!isset($_SESSION["imagePath"])) {
        if (isset($_FILES["image"]) && $_FILES["image"]["error"] == UPLOAD_ERR_OK) {
            // TODO check file type
            //  $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            $imagePath = "temp/" . $user["id"] . "." . strtolower(pathinfo($_FILES["image"]['name'], PATHINFO_EXTENSION));
            move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath);
            $_SESSION["imagePath"] = $imagePath;
        }
    }
}

// when user comes for first time, pre-fill the fields (or reset them)
if (isset($_GET["edit"]) && isset($item)) {
    $category->value = $item["category"];
    $name->value = $item["name"];
    $description->value = $item["description"];
    $quantity->value = $item["quantity"];
    $attributes = $item["attributes"];
    $attrCount = sizeof($attributes);
}

if ($name->valid && $category->valid && $description->valid && $quantity->valid && (isset($_POST["change"]) || isset($_POST["submit"]))) {
    if (isset($item)) {
        // we are just editing
        $item["name"] = $name->value;
        $item["category"] = $category->value;
        $item["description"] = $description->value;
        $item["quantity"] = $quantity->value;
        $item["attributes"] = $attributes;
        if (isset($_SESSION["imagePath"])) {
            // remove old file
            unlink(("../katalog/images/" . $item["id"] . "." . $item["imageFormat"]));
            $fmt = strtolower(pathinfo($_SESSION["imagePath"], PATHINFO_EXTENSION));
            $item["imageFormat"] = $fmt;
        }
        editItem($item);
        $id = $item["id"];
    } else {
        // move file from temporary location to its permanent home

        if (isset($_SESSION["imagePath"])) {
            $fmt = strtolower(pathinfo($_SESSION["imagePath"], PATHINFO_EXTENSION));

            $id = addItem($name->value, $_SESSION["id"], $category->value, $quantity->value, $description->value,  $attributes, $fmt);
        } else {
            $id = addItem($name->value, $_SESSION["id"], $category->value, $quantity->value, $description->value,  $attributes);
        }
    }
    if (isset($_SESSION["imagePath"])) {
        rename($_SESSION["imagePath"], "../katalog/images/" . $id . "." . $fmt);
        unset($_SESSION["imagePath"]);
    }
    header("location: ../katalog/item/?id=" . $id);
    die();
}

function returnFileSize($number)
{
    if ($number < 1024) {
        return $number .  "bytes";
    } else if ($number >= 1024 && $number < 1048576) {
        return round($number / 1024, 1) . "KB";
    } else if ($number >= 1048576) {
        return round($number / 1048576) . "MB";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <title>Register</title>
    <script src="../script.js"></script>
    <script src="./filePreview.js"></script>
</head>

<body>
    <?php
    include "../header.php"; ?>
    <main>
        <form action=<?php echo $_SERVER['PHP_SELF'] . ((isset($item["id"])) ? ("?id=" . $item["id"]) : ""); ?> method="post" class="user-form" enctype="multipart/form-data">
            <?php
            if (!isset($item)) {
                echo "<h1>Add item to Catalog</h1>";
                echo "<h2>Please fill in necessary information.</h2>";
            } else {
                echo "<h1>Edit item</h1>";
                echo "<h2>Don't forget to save changes.</h2>";
            }   ?>



            <?php
            echo $category->get_html();
            echo $name->get_html();
            echo $description->get_html();
            echo $quantity->get_html();
            ?>
            <label for="image">Upload image (Optional)</label>
            <input type="file" name="image" id="image" accept="image/*" hidden>

            <?php
            if (isset($_SESSION["imagePath"])) {
                echo "<img src=\"" . $_SESSION["imagePath"] . "\"class=\"preview\" alt=\"preview image\">";
                echo "<p>File size:" . returnFileSize(filesize($_SESSION["imagePath"])) . "</p>";
            } else if (isset($item)) {
                echo "<img src=\"" . ("../katalog/images/" . $item["id"] . "." . $item["imageFormat"]) . "\"class=\"preview\" alt=\"preview image\">";
                echo "<p>File size:" . returnFileSize(filesize(("../katalog/images/" . $item["id"] . "." . $item["imageFormat"]))) . "</p>";
            } else {
                echo "<img src=\"./noPreview.png\" class=\"preview\" alt=\"no preview image\">";
                echo "<p>No file currently selected for upload.</p>";
            } ?>
            <section class="attributes">
                <?php
                if (!isset($item)) {
                    echo "<h2>Add attributes for the item.</h2>";
                } else {
                    echo "<h2>Edit attributes</h2>";
                }   ?>

                <?php
                for ($i = 0; $i < $attrCount; $i++) {
                    $attribute = new optionsInput("attr" . $i, $i + 1, "Attributes", file_get_contents('../attributes.json'));
                    if (isset($attributes[$i][0])) {
                        $attribute->value = $attributes[$i][0];
                    }
                    echo $attribute->get_html();
                    echo "<input type=\"text\" placeholder=\"value\" name=\"val" . $i . "\" value=\"" . (isset($attributes[$i][0]) ? htmlspecialchars($attributes[$i][1], ENT_QUOTES) : "") . "\">";
                    echo "<button type=\"submit\" name=\"remAttr\" value=\"" . $i . "\" title=\"Remove attribute\">-</button>";
                }
                ?>
                <button type="submit" name="addAttr" value="add attribute" title="Add attribute">+</button>
                <input type="hidden" value="<?php echo $attrCount ?>" name="attrCount">
            </section>
            <?php
            if (!isset($item)) {
                echo '<input type="submit" name="submit" value="Add item" title="Add item">';
            } else {
                echo '<input type="submit" name="change" value="Save changes" title="Save changes">';
                echo '<input type="submit" name="remove" value="Remove item" title="Remove item">';
            }
            ?>
        </form>
    </main>
</body>

</html>