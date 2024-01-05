<?php

/**
 * @author Martin Husár
 * @author Martin Husár <husarma1@fel.cvut.cz>
 */

/**
 * Represents a form input element with validation capabilities.
 */
class formInput
{
    /**
     * @var mixed $value Pre-filled value (or user submitted).
     */
    public $value;

    /**
     * @var string $notice Notice to display when input is incorrect.
     */
    public $notice;

    /**
     * @var string $name Name of the element.
     */
    public $name;

    /**
     * @var bool $valid Flag indicating if input is valid.
     */
    public $valid;

    /**
     * @var bool $incorrect Flag indicating if input is incorrect.
     */
    public $incorrect;

    /**
     * Initializes a new instance of the formInput class.
     *
     * @param string $name Name of the element.
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Gets the input value from the POST data and performs basic validation.
     *
     * @return bool True if input is valid, false otherwise.
     */
    public function getInput()
    {
        // First, get value from POST
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

/**
 * Validates input based on length, character types, and other criteria.
 *
 * @param string $input The input to be validated.
 * @param int $minLength The minimum allowed length.
 * @param int $maxLength The maximum allowed length.
 * @param string $name Optional name for better error messages.
 *
 * @return string An error message if validation fails, otherwise an empty string.
 */
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

    // If all conditions are met, the input is valid
    return "";
}
