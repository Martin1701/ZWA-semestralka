// get input fields
const login = document.querySelector(`main form input[type="text"]`)
const password = document.querySelector(`main form input[type="password"]`)
const form = document.querySelector("main form")

// add event listeners
login.addEventListener("input", removeValidationStyle);
password.addEventListener("input", removeValidationStyle);
form.addEventListener("submit", validateLogin);

function validateLogin(e) {
    if (login.value) {
        validationStyle("", false, login.nextElementSibling, login) // valid so no notice and basic style
    } else {
        validationStyle("This field is obligatory.", false, login.nextElementSibling, login)
        e.preventDefault();
    }

    if (password.value) {
        validationStyle("", false, password.nextElementSibling, password) // valid so no notice and basic style
    } else {
        validationStyle("This field is obligatory.", false, password.nextElementSibling, password)
        e.preventDefault();
    }
}