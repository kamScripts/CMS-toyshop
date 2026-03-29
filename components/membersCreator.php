<?php
require_once "header.php";
if (isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["email"])) {
    
}

?>
<h2>Internal Tool use only</h2>
<form action="membersCreator.php" method="post">
<label for="username">
    <input type="text" id="username" name="username" maxlength="16" required>
<label for="password">
    <input type="password" id="password" name="password" minlength="8" maxlength="24" required>
<label for="email">
    <input type="email" id="email" name="email" required>
</form>

<?php
require_once "footer.php";