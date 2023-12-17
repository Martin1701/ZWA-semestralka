document.addEventListener("DOMContentLoaded", function () {
    /* login */
    // TODO move to its own JS file
    const username_field = document.querySelector(`
    #form-register input[name="login"]`);
    if (username_field) {
        username_field.addEventListener("input", validateField)
    }
    const password_field = document.querySelectorAll(`input[type="password"]`);
    if (password_field) {
        password_field[0].addEventListener("input", validateField)
        password_field[1].addEventListener("input", validateField)
    }
});

async function validateField(e) {
    const noticeElem = e.target.nextElementSibling;
    let notice, isValid;
    const name = e.target.placeholder.slice(0, -1);
    if (e.target.value) {
        if (e.target.name != "password2") {
            [notice, isValid] = validate_input(e.target.value, e.target.minLength, e.target.maxLength, name);
            if (isValid) {
                if (e.target.name == "login") {
                    const response = await fetch(`https://zwa.toad.cz/~husarma1/users.php?login=${e.target.value}`);
                    const exists = await response.json();
                    if (exists) {
                        isValid = false;
                        notice = "Username already in use."
                    } else {
                        notice = "Username available.";
                    }
                } else {
                    const password_field = document.querySelector(`input[name="password2"]`);
                    password_field.dispatchEvent(new Event("input"));
                }
            }
        } else {
            const password_field = document.querySelector(`input[type="password"]`);
            if (password_field.value == e.target.value) {
                isValid = true;
                notice = "Passwords match";
            } else {
                isValid = false;
                notice = "Passwords do not match";
            }
        }



        if (isValid) {
            e.target.classList.remove("incorrectInput");
            e.target.classList.add("correctInput");
            // e.target.classList.remove("incorrectText");
            // e.target.classList.add("correctText");
            noticeElem.classList.remove("incorrectText");
            noticeElem.classList.add("correctText");
            noticeElem.innerHTML = notice;
        } else {
            e.target.classList.add("incorrectInput");
            e.target.classList.remove("correctInput");
            // e.target.classList.add("incorrectText");
            // e.target.classList.remove("correctText");
            noticeElem.classList.add("incorrectText");
            noticeElem.classList.remove("correctText");
            noticeElem.innerHTML = notice;
        }
    } else {
        e.target.classList.remove("incorrectInput");
        e.target.classList.remove("correctInput");
        noticeElem.innerHTML = "";
    }
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