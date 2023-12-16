<nav class="main-page-navigation">
    <a href="/~husarma1">
        <svg class="svg-icon svg-icon-small" viewBox="0 0 24 24">
            <use xlink:href="/~husarma1/svg/home.svg#home-svg"></use>
        </svg>
        Home</a>
    <?php
    if (isset($_SESSION["id"])) {
        include_once "/home/husarma1/www/users.php";
        $user = getUser($_SESSION["id"]);

        echo "<p>Welcome, " .  $user["login"] . " (" . $user["id"] .   ")</p>";
        echo "<a href=\"/~husarma1/logout\">Log out</a>";
    } else {
        echo "<a href=\"/~husarma1/login\">";
        echo "<svg class=\"svg-icon svg-icon-small\" viewBox=\"0 0 50 50\">";
        echo "<use xlink:href=\"/~husarma1/svg/login.svg#login-svg\"></use>";
        echo "</svg>";
        echo "Log in</a>";
    }

    if (!isset($_SESSION["id"])) {
        echo "<a href=\"/~husarma1/register\">";
        echo "<svg class=\"svg-icon svg-icon-small\" viewBox=\"0 0 15 15\">";
        echo "<use xlink:href=\"/~husarma1/svg/register.svg#register-svg\"></use>";
        echo "</svg>";
        echo "Register</a>";
    }
    ?>
    <a href="/~husarma1/userSettings">
        <svg class="svg-icon svg-icon-small" viewBox="0 0 50 50">
            <use xlink:href="/~husarma1/svg/user.svg#user-svg"></use>
        </svg>
        User Settings</a>
</nav>
<header>
    <a href="/~husarma1"><img width="200" alt="logo" class="logo" src=""></a>
    <form class="search-container">
        <input type="text" autocomplete="off" id="searchBar" placeholder="Enter product name (2+ characters)" maxlength="50" minlength="2" required>
        <button type="reset" title="Clear">
            <svg class="svg-icon" viewBox="0 -2 20 20" data-is-empty="false">
                <use xlink:href="/~husarma1/svg/close.svg#close-svg"></use>
            </svg>
        </button>
        <button type="submit" title="Search">
            <svg class="svg-icon" viewBox="0 0 50 50">
                <use xlink:href="/~husarma1/svg/magnifier.svg#magnifier-svg"></use>
            </svg>
        </button>
    </form>
</header>