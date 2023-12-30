<?php
session_start();
include_once "/home/husarma1/www/users.php";

if (!isset($_SESSION["id"])) {
    setcookie("afterLogin", $_SERVER['REQUEST_URI'], time() + 60 * 60 * 1, "/"); // 1 hour expiration
    header("location: ../login/");
    die();
}

$user = getUser($_SESSION["id"]);

class settingsInput
{
    private $isPassword;        // if true use type="password"
    public $value;              // pre-filled value (or user submitted)
    public $notice;             // notice to display
    public $name;               // name of the element
    private $label;             // input label
    public $valid;              // if input is valid or not
    public $minLength;
    public $maxLength;
    function __construct($name, $label,  $isPassword = false, $minLength = 0, $maxLength = 0)
    {
        $this->name = $name;
        $this->label = $label;
        $this->isPassword = $isPassword;
        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
        $this->valid = false;
    }
    // returns string that can be inserted directly into HTML
    function get_html()
    {
        $element = "<label for=\"" . $this->name . "\">" . $this->label . "</label>";
        $element .= "<input type=\"" . (($this->isPassword) ? "password" : "text") . "\" name=\"" . $this->name . "\" id=\"" . $this->name . "\"";
        $element .= ($this->minLength) ? " minlength=\"" . $this->minLength . "\"" : "";
        $element .= ($this->maxLength) ? " maxlength=\"" . $this->maxLength . "\"" : "";
        if ($this->name == "login" && $this->valid) {
            $element .= "class=\"correctInput\"";
        } else {
            $element .= (($this->notice) ? "class=\"incorrectInput\"" : "");
        }
        if (!$this->isPassword) {
            $element .= " value=\"" . htmlspecialchars($this->value, ENT_QUOTES) . "\"";
        }
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
            // $this->notice = "This field is obligatory.";
            return false;
        }
    }
}
// returns "" when valid !
// else returns error message
function validate_input($input, $minLength, $maxLength, $name = "")
{
    // Check length
    $length = strlen($input);
    if ($length < $minLength) {
        return $name . " must be longer than " . $minLength . " characters.";
    }
    if ($length > $maxLength) {
        return $name . " must be shorter than " . $maxLength . " characters.";
    }

    // Check for at least one letter and one digit using regular expressions
    if (!preg_match('/[a-zA-Z]/', $input)) {
        return $name . " must contain at least one letter.";
    }

    if (!preg_match('/[0-9]/', $input)) {
        return $name . " must contain at least one digit.";
    }

    // If all conditions are met, the password is valid
    return "";
}

$fName = new settingsInput("fName", "First name");
$lName = new settingsInput("lName", "Last name");
$login = new settingsInput("login", "Login", false, 4, 30);
$passwordCur = new settingsInput("passwordCur", "Current password", true, 8, 30);
$password = new settingsInput("password", "New password", true, 8, 30);
$password2 = new settingsInput("password2", "Confirm new password", true); // only $password's length is validated

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $changesMade = false;
    //* first name
    if ($fName->getInput() && $fName->value != $user["fName"]) {
        $changesMade = true;
        $user["fName"] = $fName->value;
    }
    if ($lName->getInput() && $lName->value != $user["lName"]) {
        $changesMade = true;
        $user["lName"] = $lName->value;
    }
    if ($login->getInput() && $login->value != $user["login"]) {
        $login->notice = validate_input($login->value, $login->minLength, $login->maxLength, "Login");
        // login is valid, check if the user already exists
        if (!$login->notice) {
            if (getUserByLogin($login->value)) {
                $login->notice = "Username already in use.";
            } else {
                $changesMade = true;
                $user["login"] = $login->value;
            }
        }
    }
    if ($passwordCur->getInput()) {
        if (password_verify($passwordCur->value, $user["password"])) {
            $passwordCur->valid = true;
        } else {
            $passwordCur->notice = "Incorrect password.";
        }
    }
    if ($password->getInput()) {
        $password->notice = validate_input($password->value, $password->minLength, $password->maxLength, "Password");
        if (!$password->notice) {
            if (password_verify($password->value, $user["password"])) {
                $password->notice = "New password can't be same as old one.";
            } else {
                $password->valid = true;
            }
        }
        if (!$passwordCur->value) {
            $passwordCur->notice = "Please type in current password.";
        }
    } else if ($passwordCur->value || $password2->value) {
        $password->notice = "Please type in new password.";
    }
    if ($password2->getInput()) {
        if ($password->value != $password2->value) {
            $password2->notice = "Passwords do not match.";
            $password->value = "";
            $password2->value = "";
        } else {
            $password2->valid = true;
        }
    } else if ($passwordCur->value || $password->value) {
        $password2->notice = "Please confirm new password.";
    }
    if ($passwordCur->valid && $password->valid && $password2->valid) {
        $changesMade = true;
        $user["password"] = password_hash($password->value, PASSWORD_DEFAULT);
    }
    if ($changesMade) {
        editUser($user);
    }
}

$fName->value = $user["fName"];
$lName->value = $user["lName"];
$login->value = $user["login"];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <title>Register</title>
    <script src="../script.js"></script>
    <script src="./settingsValidation.js"></script>
</head>

<body>
    <?php
    include "../header.php"; ?>
</body>
<main>
    <form action=<?php echo $_SERVER['PHP_SELF']; ?> method="post" class="user-form">
        <h1><?php echo htmlspecialchars($user["login"], ENT_QUOTES);
            ?></h1>
        <h2>Here you can change your information.</h2>
        <?php
        echo $fName->get_html();
        echo $lName->get_html();
        echo $login->get_html();
        ?>
        <aside><strong>Login must contain:</strong>
            <ul>
                <li>from 4 to 30 characters</li>
                <li>at least one letter</li>
                <li>at least one digit</li>
            </ul>
        </aside>
        <input type="submit" name="submit" value="Save changes" title="Register">
        <h2>Change password:</h2>
        <?php
        echo $passwordCur->get_html();
        echo $password->get_html();
        ?>
        <aside><strong>Password must contain:</strong>
            <ul>
                <li>from 8 to 30 characters</li>
                <li>at least one letter</li>
                <li>at least one digit</li>
            </ul>
        </aside>
        <?php
        echo $password2->get_html();
        ?>
        <input type="submit" name="submit" value="Save changes" title="Save changes">
    </form>
</main>

</html>