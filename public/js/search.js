document.addEventListener("DOMContentLoaded", function () { // included in header.mustache need for wait until DOM is loaded
    document.querySelector(`.search-container>input[type="text"]`).addEventListener("input", showClear); // show clear button when text is inputted

    const searchClear = document.querySelector(`.search-container>button[type="reset"]`);
    searchClear.addEventListener("click", clearSearch);
    searchClear.children[0].classList.add("hidden"); // hide it on page load (it stays visible with JS disabled)
});
// show button to clear search field
function showClear(e) {
    const searchClear = document.querySelector(`.search-container>button[type="reset"]`);
    if (e.target.value.length > 0) {
        searchClear.children[0].classList.remove("hidden");
    } else {
        searchClear.children[0].classList.add("hidden");
    }
}
// hide button to clear search field after it has been clicked
function clearSearch(e) {
    e.currentTarget.children[0].classList.add("hidden");
}