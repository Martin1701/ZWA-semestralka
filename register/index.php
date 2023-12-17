<?php
include_once "/home/husarma1/www/users.php";

class registerInput
{
    private $isPassword;        // if true use type="password"
    public $value;              // pre-filled value (or user submitted)
    public $notice;             // notice to display
    public $name;               // name of the element
    private $placeholder;       // input placeholder
    public $valid;              // if input is valid or not
    public $minLength;
    public $maxLength;
    function __construct($name, $placeholder,  $isPassword = false, $minLength = 0, $maxLength = 0)
    {
        $this->name = $name;
        $this->placeholder = $placeholder;
        $this->isPassword = $isPassword;
        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
        $this->valid = false;
    }
    // returns string that can be inserted directly into HTML
    function get_html()
    {
        $element = "<input type=\"" . (($this->isPassword) ? "password" : "text") . "\" name=\"" . $this->name . "\"";
        $element .= ($this->minLength) ? "minlength=\"" . $this->minLength . "\"" : "";
        $element .= ($this->maxLength) ? "maxlength=\"" . $this->maxLength . "\"" : "";
        if ($this->name == "login" && $this->valid) {
            $element .= "class=\"correctInput\"";
        } else {
            $element .= (($this->notice) ? "class=\"incorrectInput\"" : "");
        }

        $element .= " value=\"" . htmlspecialchars($this->value, ENT_QUOTES) . "\"";
        $element .= " placeholder=\"" . $this->placeholder . "\">";
        $element .= "<p class=\"" . (($this->valid) ? "correctText" : "incorrectText") . "\">" . $this->notice . "</p>";
        return $element;
    }
    function getInput()
    {
        // first, get value from POST
        if (isset($_POST[$this->name]) && $_POST[$this->name]) {
            $this->value = $_POST[$this->name];
        } else {
            $this->notice = "This field is obligatory.";
        }
    }
}

$fName = new registerInput("fName", "First name*");
$lName = new registerInput("lName", "Last name*");
$login = new registerInput("login", "Login*", false, 4, 30);
$password = new registerInput("password", "Password*", true, 8, 30);
$password2 = new registerInput("password2", "Confirm password*", true); // only $password's length is validated

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Only way to register is through POST !

    // TLDR: go through every register field, check if it is set, then if it has value
    // in case of login/password verify length and letter/number count
    // set user notice message if mistake found
    // login is exception, special value valid is used, notice can be "Username valid."

    //* first name
    $fName->getInput();
    if ($fName->value) {
        $fName->valid = true;
    }
    //* last name
    $lName->getInput();
    if ($lName->value) {
        $lName->valid = true;
    }
    //* login
    $login->getInput();
    if ($login->value) {
        $login->notice = validate_input($login->value, $login->minLength, $login->maxLength, "Login");
        // login is valid, check if the user already exists
        if (!$login->notice) {
            if (getUserByLogin($login->value)) {
                $login->notice = "Username already in use.";
            } else {
                $login->notice = "Username available.";
                $login->valid = true;
            }
        }
    }
    //* password
    $password->getInput();
    if ($password->value) {
        $password->notice = validate_input($password->value, $password->minLength, $password->maxLength, "Password");
        $password->valid = true;
    }
    //* retype password
    $password2->getInput();
    if ($password2->value) {
        if ($password->value != $password2->value) {
            $password2->notice = "Passwords do not match.";
        } else {
            $password2->valid = true;
        }
    }

    if (
        $fName->valid &&
        $lName->valid &&
        $login->valid &&
        $password->valid &&
        $password2->valid
    ) {
        session_start();
        $h = password_hash($password->value, PASSWORD_DEFAULT);
        $_SESSION["id"] = addUser($fName->value, $lName->value, $login->value, $h);

        // redirect user to main page
        header("location: ../");
    }
}
// returns 0 when valid !
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


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <title>Register</title>
    <script src="../script.js"></script>
    <script src="./registerValidation.js"></script>
</head>

<body>
    <?php
    include "../header.php";
    ?>
    <main class="main-page-container">
        <form action=<?php echo $_SERVER['PHP_SELF']; ?> method="post" class="user-form">
            <h1>Register</h1>
            <h2>Please enter your login details</h2>
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
            <?php
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
            <input type="submit" name="submit" value="Register" title="Register">
        </form>
    </main>
</body>


</html>