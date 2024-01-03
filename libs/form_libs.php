<?php
class formInput
{
    public $value; // pre-filled value (or user submitted)
    public $notice; // notice to display
    public $name; // name of the element
    // valid/incorrect, so the input is tri-state
    public $valid; // if input is valid
    public $incorrect; // if input is incorrect
    function __construct($name)
    {
        $this->name = $name;
    }
    function getInput()
    {
        // first, get value from POST
        if (isset($_POST[$this->name]) && $_POST[$this->name]) {
            $this->value = $_POST[$this->name];
            return true;
        } else {
            $this->notice = "This field is obligatory.";
            $this->incorrect = true;
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
