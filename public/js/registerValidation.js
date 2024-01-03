
const username_field = document.querySelector(`input[name="login"]`);
username_field.addEventListener("input", validateLogin)
const password_field = document.querySelectorAll(`input[type="password"]`);
password_field[0].addEventListener("input", validatePassword)
password_field[1].addEventListener("input", validatePassword2)



function validateStyle(notice, isValid, noticeElem, inputField) {
    if (notice) {
        if (isValid) {
            inputField.classList.remove("incorrectInput");
            inputField.classList.add("correctInput");
            // inputField.classList.remove("incorrectText");
            // inputField.classList.add("correctText");
            noticeElem.classList.remove("incorrectText");
            noticeElem.classList.add("correctText");
            noticeElem.innerHTML = notice;
        } else {
            inputField.classList.add("incorrectInput");
            inputField.classList.remove("correctInput");
            // inputField.classList.add("incorrectText");
            // inputField.classList.remove("correctText");
            noticeElem.classList.add("incorrectText");
            noticeElem.classList.remove("correctText");
            noticeElem.innerHTML = notice;
        }
    } else {
        inputField.classList.remove("incorrectInput");
        inputField.classList.remove("correctInput");
        noticeElem.innerHTML = "";
    }
}




async function validateLogin(e) {
    const name = document.querySelector(`label[for=${e.target.name}]`).innerHTML;
    let notice, isValid;
    if (e.target.value) {
        [notice, isValid] = validate_input(e.target.value, 4, 30, name);
        if (isValid) {
            const response = await fetch(`https://zwa.toad.cz/~husarma1/users.php?login=${encodeURIComponent(e.target.value)}`, { mode: 'no-cors' });
            const exists = await response.json();
            if (exists) {
                isValid = false;
                notice = "Username already in use."
            } else {
                notice = "Username available.";
            }
        }
    }
    validateStyle(notice, isValid, e.target.nextElementSibling, e.target);
}
function validatePassword(e) {
    let notice, isValid;
    const name = document.querySelector(`label[for=${e.target.name}]`).innerHTML;
    console.log(e.target.previousSibling);
    if (e.target.value) {
        [notice, isValid] = validate_input(e.target.value, 8, 30, name);
    }
    const password_field = document.querySelector(`input[name="password2"]`);
    password_field.dispatchEvent(new Event("input"));
    validateStyle(notice, isValid, e.target.nextElementSibling, e.target);
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
    validateStyle(notice, isValid, e.target.nextElementSibling, e.target);
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