const image = document.querySelector(`input[name="image"]`);
image.addEventListener("change", updateImageDisplay);


function updateImageDisplay(e) {
    const image = e.target.nextElementSibling;

    if (e.target.files.length > 0) {
        const file = e.target.files[0];
        if (validTypeImage(file)) {
            image.src = URL.createObjectURL(file);
            image.alt = image.title = file.name;
            image.nextElementSibling.innerHTML = `File size: ${returnFileSize(
                file.size,
            )}.`;
            image.nextElementSibling.classList.remove("incorrectText");
        } else {
            image.nextElementSibling.innerHTML = `File name ${file.name}: Not a valid file type.`;
            image.nextElementSibling.classList.add("incorrectText");
        }
    }
}

const fileTypes = [
    "image/apng",
    "image/bmp",
    "image/gif",
    "image/jpeg",
    "image/pjpeg",
    "image/png",
    "image/svg+xml",
    "image/tiff",
    "image/webp",
    "image/x-icon",
];

function validTypeImage(file) {
    return fileTypes.includes(file.type);
}


function returnFileSize(number) {
    if (number < 1024) {
        return `${number} bytes`;
    } else if (number >= 1024 && number < 1048576) {
        return `${(number / 1024).toFixed(1)} KB`;
    } else if (number >= 1048576) {
        return `${(number / 1048576).toFixed(1)} MB`;
    }
}