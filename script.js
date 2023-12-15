window.onload = () => {
    // dom loaded ? ......
    const searchBar = document.getElementById("searchBar");

    searchBar.addEventListener("input", showClear);

    const searchBarClear = document.getElementById("searchBarClear");
    searchBarClear.addEventListener("click", clearSearchBar);
    searchBarClear.children[0].classList.add("hidden");

    const username_field = document.getElementById("input-login");
    if (username_field) {
        username_field.addEventListener("input", usernameExists)
    }
}
function showClear(e) {
    const searchBarClear = document.getElementById("searchBarClear");
    if (e.target.value.length > 0) {
        searchBarClear.children[0].classList.remove("hidden");
    } else {
        searchBarClear.children[0].classList.add("hidden");
    }
}
function clearSearchBar(e) {
    const searchBarClear = document.getElementById("searchBarClear");
    searchBarClear.children[0].classList.add("hidden");
    const searchBar = document.getElementById("searchBar");
    searchBar.value = "";
}

async function usernameExists(e) {
    const response = await fetch(`https://zwa.toad.cz/~husarma1/users.php?login=${e.target.value}`);
    const exists = await response.json();

    if (exists) {
        e.target.style.backgroundColor = "red";
    } else {
        e.target.style.backgroundColor = "green";
    }
}