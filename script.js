document.addEventListener("DOMContentLoaded", function () {
    /* header-specific event listeners */
    document.querySelector(`.search-container>input[type="text"]`).addEventListener("input", showClear); // show clear button when text is inputted

    const searchClear = document.querySelector(`.search-container>button[type="reset"]`);
    searchClear.addEventListener("click", clearSearch); // hide button when clicked
    searchClear.children[0].classList.add("hidden"); // hide it on page load (it stays visible with JS disabled)
});
function showClear(e) {
    const searchClear = document.querySelector(`.search-container>button[type="reset"]`);
    if (e.target.value.length > 0) {
        searchClear.children[0].classList.remove("hidden");
    } else {
        searchClear.children[0].classList.add("hidden");
    }
}
function clearSearch(e) {
    e.currentTarget.children[0].classList.add("hidden");
}