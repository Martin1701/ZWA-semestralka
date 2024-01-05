// common function to change styling of input elements
function validationStyle(notice, isValid, noticeElem, inputField) {
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

function removeValidationStyle(e) {
    validationStyle("", false, e.target.nextElementSibling, e.target)
}