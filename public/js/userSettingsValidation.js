const fName = document.querySelector(`main form input[name="fName"]`)
const lName = document.querySelector(`main form input[name="lName"]`)
const login = document.querySelector(`input[name="login"]`)
let passwordCur = document.querySelectorAll(`input[type="password"]`)[0]
const password = document.querySelectorAll(`input[type="password"]`)[1]
const password2 = document.querySelectorAll(`input[type="password"]`)[2]
const form = document.querySelector("main form")

fName.addEventListener("input", removeValidationStyle)
lName.addEventListener("input", removeValidationStyle)
passwordCur.addEventListener("input", removeValidationStyle)
login.addEventListener("input", validateLogin)
password.addEventListener("input", validatePassword)
password2.addEventListener("input", validatePassword2)

form.addEventListener("submit", validateSettings);

function validateSettings(e) {
    if (fName.value) {
        validationStyle("", false, fName.nextElementSibling, fName)
    } else {
        validationStyle("This field is obligatory.", false, fName.nextElementSibling, fName)
        e.preventDefault();
        window.scrollTo(0, 0);
    }
    if (lName.value) {
        validationStyle("", false, lName.nextElementSibling, lName)
    } else {
        validationStyle("This field is obligatory.", false, lName.nextElementSibling, lName)
        e.preventDefault();
        window.scrollTo(0, 0);
    }
    if (login.value) {
        if (login.classList.contains("incorrectInput")) {
            e.preventDefault();
            window.scrollTo(0, 0);
        }
    } else {
        validationStyle("This field is obligatory.", false, login.nextElementSibling, login)
        e.preventDefault();
        window.scrollTo(0, 0);
    }
    // only if in any of the password inputs is value, validate those

    if (passwordCur.value || password.value || password2.value) {
        if (passwordCur.value) {
            validationStyle("", false, passwordCur.nextElementSibling, passwordCur)
        } else {
            validationStyle("This field is obligatory.", false, passwordCur.nextElementSibling, passwordCur)
            e.preventDefault();
        }
        if (password.value) {
            if (!password.classList.contains("correctInput")) {
                e.preventDefault();
            }
        } else {
            validationStyle("This field is obligatory.", false, password.nextElementSibling, password)
            e.preventDefault();
        }
        if (password2.value) {
            if (!password2.classList.contains("correctInput")) {
                e.preventDefault();
            }
        } else {
            validationStyle("This field is obligatory.", false, password2.nextElementSibling, password2)
            e.preventDefault();
        }
    }
}


async function validateLogin(e) {
    const name = document.querySelector(`label[for=${e.target.name}]`).innerHTML;
    let notice, isValid;
    const currentLogin = document.querySelector("form>h1").innerHTML;
    if (e.target.value && e.target.value != currentLogin) {
        [notice, isValid] = validate_input(e.target.value, 4, 30, name);
        if (isValid) {
            const response = await fetch(`https://zwa.toad.cz/~husarma1/check/loginExists/?login=${encodeURIComponent(e.target.value)}`, { mode: 'no-cors' });
            const exists = await response.json();
            if (exists) {
                isValid = false;
                notice = "Username already in use."
            } else {
                notice = "Username available.";
            }
        }
    }
    validationStyle(notice, isValid, e.target.nextElementSibling, e.target);
}
function validatePassword(e) {
    let notice, isValid;
    const name = document.querySelector(`label[for=${e.target.name}]`).innerHTML;
    if (e.target.value) {
        [notice, isValid] = validate_input(e.target.value, 8, 30, name);
    }
    const password_field = document.querySelector(`input[name="password2"]`);
    password_field.dispatchEvent(new Event("input"));
    validationStyle(notice, isValid, e.target.nextElementSibling, e.target);
}

async function validatePassword2(e) {
    const noticeElem = e.target.nextElementSibling;
    let notice, isValid;

    const password_field = document.querySelector(`input[name="password"]`);
    if (password_field.value == e.target.value) {
        isValid = true;
        notice = "Passwords match";
    } else {
        isValid = false;
        notice = "Passwords do not match";
    }
    validationStyle(notice, isValid, e.target.nextElementSibling, e.target);
}



function validate_input(input, minLength, maxLength, name = "") {
    // Check length
    length = input.length;
    if (length < minLength) {
        return [name + " must be longer than " + minLength + " characters.", false];
    }
    if (length > maxLength) {
        return [name + " must be shorter than " + maxLength + " characters.", false];
    }

    // Check for at least one letter and one digit using regular expressions
    if (!input.match(/[A-Za-z]/)) {
        return [name + " must contain at least one letter.", false];
    }

    if (!input.match(/[0-9]/)) {
        return [name + " must contain at least one digit.", false];
    }

    // If all conditions are met, the password is valid
    return [name + " is valid.", true];
}