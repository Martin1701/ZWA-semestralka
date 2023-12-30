<?php
class optionsInput
{
    public $value;
    public $name;
    public $label;
    public $valid;
    public $notice;
    private $plural;
    private $options; // json object(array of options) with key and ["label"] value
    function __construct($name, $label, $plural, $json)
    {
        $this->name = $name;
        $this->label = $label;
        $this->plural = $plural;
        $this->options = json_decode($json, true);
        $this->valid = false;
    }
    function get_html()
    {
        $element = "<label for=\"" . $this->name . "\">" . $this->label . "</label>";
        $element .= "<select name=\"" . $this->name . "\" id=\"" . $this->name . "\"";
        $element .= (($this->notice) ? "class=\"incorrectInput\"" : "");
        $element .= "><option value=\"0\" label=\"Select a " . $this->name . " ... \"" . (($this->value) ? "" : " selected=\"selected\"") . ">Select a " . $this->name . " ... </option>";


        // Loop through each optgroup in the JSON array
        foreach ($this->options as $group => $value) {
            $element .= "<optgroup label=\"" . $group . "\">";
            // make option for each category 
            foreach ($value as $option => $label) {
                $element .= "<option value=" . $option . (($this->value == $option) ? " selected=\"selected\"" : "") . ">" . $label . "</option>";
            }
            $element .= "</optgroup>";
        }
        $element .= "</select>";
        // $element .= " placeholder=\"" . $this->placeholder . "\">";
        $element .= "<p class=\"" . (($this->valid) ? "correctText" : "incorrectText") . "\">" . $this->notice . "</p>";
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
