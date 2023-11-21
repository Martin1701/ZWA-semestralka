window.onload = () => {
    const searchbar = document.getElementById("searchbar");

    searchbar.addEventListener("input", showX);
}
function showX(e) {

    if (e.target.value.length > 0) {
        console.log("show")
    } else {
        console.log("hide")
    }

}