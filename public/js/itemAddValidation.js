return;
const category = document.querySelector(`main form select[name="category"]`)
const name = document.querySelector(`main form input[name="name"]`)
const description = document.querySelector(`main form input[name="description"]`)
const quantity = document.querySelector(`main form input[name="quantity"]`)
const deleteItem = document.querySelector(`main form input[name="delete"]`)

const form = document.querySelector("main form")

category.addEventListener("input", removeValidationStyle);
name.addEventListener("input", removeValidationStyle);
description.addEventListener("input", removeValidationStyle);
quantity.addEventListener("input", removeValidationStyle);
if (deleteItem) {
    deleteItem.addEventListener("click", noValidate);
}

form.addEventListener("submit", validateItem)

let validate = true;
function noValidate() {
    // delete item, no need for validation
    validate = false;
}

function validateItem(e) {
    if (validate) {
        if (category.value != 0) {
            validationStyle("", false, category.nextElementSibling, category)
        } else {
            validationStyle("This field is obligatory.", false, category.nextElementSibling, category)
            e.preventDefault();
            window.scrollTo(0, 0);
        }
        if (name.value) {
            validationStyle("", false, name.nextElementSibling, name)
        } else {
            validationStyle("This field is obligatory.", false, name.nextElementSibling, name)
            e.preventDefault();
            window.scrollTo(0, 0);
        }
        if (description.value) {
            validationStyle("", false, description.nextElementSibling, description)
        } else {
            validationStyle("This field is obligatory.", false, description.nextElementSibling, description)
            e.preventDefault();
            window.scrollTo(0, 0);
        }
        if (quantity.value) {
            validationStyle("", false, quantity.nextElementSibling, quantity)
        } else {
            validationStyle("This field is obligatory.", false, quantity.nextElementSibling, quantity)
            e.preventDefault();
        }
        const fileInfo = document.getElementsByClassName("info")[0]
        if (fileInfo.classList.contains("incorrectText")) {
            // invalid file type
            e.preventDefault();
        }
        // we don't check the attributes here
    }
}