<?php
require_once "header.php";

if (isset($_POST["username"]) & isset($_POST["password"])) {
    $username_html_special_chars = htmlspecialchars($_POST["username"]);
}
?>

<form action="login.php" method="post">
    <label for="username" >
        <input type="text" id="username" name="username">
    <label for="password">
        <input type="password" id="password" name="password">

</form>

<?php 
require_once "footer.php";