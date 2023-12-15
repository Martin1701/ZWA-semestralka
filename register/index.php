<?php
include_once "/home/husarma1/www/users.php";
// get form data from request
$fName = "";
$lName = "";
$login = "";
$password = "";
$password2 = "";

$fNameNotice = "";
$lNameNotice = "";
$loginNotice = "";
$passwordNotice = "";
$password2Notice = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Only way to register is through POST !

    // TLDR: go through every register field, check if it is set, then if it has value
    // in case of login/password verify length and letter/number count
    // set user notice message if mistake found

    //* first name
    if (isset($_POST["fName"]) && $_POST["fName"]) {
        $fName = $_POST["fName"];
    } else {
        $fNameNotice = "This field is obligatory.";
    }
    //* last name
    if (isset($_POST["lName"]) && $_POST["lName"]) {
        $lName = $_POST["lName"];
    } else {
        $lNameNotice = "This field is obligatory.";
    }
    //* login
    if (isset($_POST["login"]) && $_POST["login"]) {
        $login = $_POST["login"];
        $loginNotice = validate_input($login, 4, 30, "Login");
        // login is valid, check if the user already exists
        if (!$loginNotice) {
            if (getUserByLogin($login)) {
                $loginNotice = "Username already in use.";
            }
        }
    } else {
        $loginNotice = "This field is obligatory.";
    }
    //* password
    if (isset($_POST["password"]) && $_POST["password"]) {
        $password = $_POST["password"];
        $passwordNotice = validate_input($password, 8, 30);
    } else {
        $passwordNotice = "This field is obligatory.";
    }
    //* retype password
    if (isset($_POST["password2"]) && $_POST["password2"]) {
        $password2 = $_POST["password2"];
        if ($password != $password2) {
            $password2Notice = "Passwords do not match.";
        }
    } else {
        $password2Notice = "This field is obligatory.";
    }

    if (
        !$fNameNotice &&
        !$lNameNotice &&
        !$loginNotice &&
        !$passwordNotice &&
        !$password2Notice
    ) {
        // no notices so everything is valid
        session_start();
        $h = password_hash($password, PASSWORD_DEFAULT);
        $_SESSION["id"] = addUser($fName, $lName, $login, $h);

        // redirect user to main page
        header("location: ../");
    }
}





// returns 0 when valid !
// else returns error message
function validate_input($input, $minLength = 0, $maxLength = 30, $name = "")
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
</head>

<body>
    <?php
    include "../header.php";
    ?>
    <main class="main-page-container">
        <br>
        <section class="main-page-section">
            <h1>Register</h1>
            <form action=<?php echo $_SERVER['PHP_SELF']; ?> method="post" class="form-register">
                <p>Please enter your login details</p>
                <input type="text" class="form-text-input" name="fName" id="input-fname" value="<?php echo htmlspecialchars($fName, ENT_QUOTES) ?>" placeholder="First name*">
                <?php if ($fNameNotice) {
                    echo "<p class='notice'>" . $fNameNotice . "<p>";
                }
                ?>
                <br>
                <input type="text" class="form-text-input" name="lName" id="input-lname" value="<?php echo htmlspecialchars($lName, ENT_QUOTES) ?>" placeholder="Last name*">
                <?php if ($lNameNotice) {
                    echo "<p class='notice'>" . $lNameNotice . "<p>";
                }
                ?>
                <br>
                <input type="text" class="form-text-input" name="login" id="input-login" value="<?php echo htmlspecialchars($login, ENT_QUOTES) ?>" placeholder="Login*">
                <?php if ($loginNotice) {
                    echo "<p class='notice'>" . $loginNotice . "<p>";
                }
                ?>
                <aside><strong>Login must contain:</strong>
                    <ul>
                        <li>from 4 to 30 characters</li>
                        <li>at least one letter</li>
                        <li>at least one digit</li>
                    </ul>
                </aside>
                <input type="password" name="password" id="input-password" value="<?php echo htmlspecialchars($password, ENT_QUOTES) ?>" placeholder="Password*">
                <?php if ($passwordNotice) {
                    echo "<p class='notice'>" . $passwordNotice . "<p>";
                }
                ?>
                <aside><strong>Password must contain:</strong>
                    <ul>
                        <li>from 8 to 30 characters</li>
                        <li>at least one letter</li>
                        <li>at least one digit</li>
                    </ul>
                </aside>
                <input type="password" name="password2" id="input-password2" value="<?php echo htmlspecialchars($password2, ENT_QUOTES) ?>" placeholder="Confirm password*">
                <?php if ($password2Notice) {
                    echo "<p class='notice'>" . $password2Notice . "<p>";
                }
                ?>
                <br>
                <input type="submit" name="submit">
            </form>
        </section>
    </main>
</body>


</html>
<!-- <?php
        echo '
<pre>';
        print_r($_POST);
        echo '</pre>';

        ?> -->