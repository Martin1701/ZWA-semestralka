<?php
/* set the cache expire to 120 minutes */
// Set session lifetime to 1 hour (3600 seconds)
ini_set('session.gc_maxlifetime', 3600);
session_start();
include_once "/home/husarma1/www/users.php";

class loginInput
{
    private $isPassword;        // if true use type="password"
    public $value;              // pre-filled value (or user submitted)
    public $notice;             // notice to display
    public $name;               // name of the element
    private $placeholder;       // input placeholder
    public $valid;              // if input is valid or not
    function __construct($name, $placeholder,  $isPassword = false)
    {
        $this->name = $name;
        $this->placeholder = $placeholder;
        $this->isPassword = $isPassword;
        $this->valid = false;
    }
    // returns string that can be inserted directly into HTML
    function get_html()
    {
        $element = "<label for=\"" . $this->name . "\">" . $this->placeholder . "</label>";
        $element .= "<input type=\"" . (($this->isPassword) ? "password" : "text") . "\" name=\"" . $this->name . "\"" . "\" id=\"" . $this->name . "\"";
        if ($this->name == "login" && $this->valid) {
            $element .= "class=\"correctInput\"";
        } else {
            $element .= (($this->notice) ? "class=\"incorrectInput\"" : "");
        }

        $element .= " value=\"" . htmlspecialchars($this->value, ENT_QUOTES) . "\"";
        // $element .= " placeholder=\"" . $this->placeholder . "\">";
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

$login = new loginInput("login", "Login", false);
$password = new loginInput("password", "Password", true);

if (isset($_SESSION["id"])) {
    if (getUser($_SESSION["id"])) {
        header("location: ../");
        die();
    }
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($login->getInput() && $password->getInput()) {
        if (!isset($_SESSION["id"])) {
            if (existsUser($login->value)) {
                $user = getUserByLogin($login->value);
                if (password_verify($password->value, $user["password"])) {
                    $_SESSION["id"] = $user["id"];
                    // no need to set $login->valid=true and $password->valid=true
                    if (isset($_COOKIE["afterLogin"])) {
                        // if user comes from page that redirected him to login, return to that page
                        header("location: " . $_COOKIE["afterLogin"]);
                        setcookie("afterLogin", "", -1, "/");
                    } else {
                        header("location: ../");
                    }
                    die();
                } else {
                    $login->valid = true;
                    $password->notice = "Incorrect password.";
                }
            } else {
                $login->notice = "This user does not exist.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <title>Login</title>
    <script src="../script.js"></script>
</head>

<body>
    <?php
    include "../header.php";
    ?>

    <main>
        <form class="user-form" action=<?php echo $_SERVER['PHP_SELF']; ?> method="post">
            <h1>Log in</h1>
            <h2>Please enter your login and password</h2>
            <?php
            echo $login->get_html();
            echo $password->get_html();
            ?>
            <input type="submit" value="Log in" title="Log in">
        </form>
    </main>
</body>

</html>