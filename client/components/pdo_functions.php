<?php
$dbhost = 'localhost';
$db = 'modelCarsOnline';
$dbuser = 'admin01';
$dbpassword  = 'password';
$chrset = 'utf8mb4';
$dbattr = "mysql:host=$dbhost;dbname=$db;charset=$chrset";
$opts = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];
try {
    $pdo = new PDO($dbattr, $dbuser, $dbpassword, $opts);    
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

function destroySession() {
    $_SESSION=array();
    if (session_id() !== "" || isset(($_COOKIE[session_name()]))) {
    setcookie(session_name(), '', time()-2592000, '/');
    }
    session_destroy();
}

function showUserProfile($user, $pdo) {
    if (file_exists("$user.jpg")) {
        echo "<img src='./images/$user.jpg' alt='User profile picture class='profilePic'>";
    }
    $stmt = $pdo->prepare("SELECT username FROM users WHERE username=?");
    $stmt->execute([$user]);

    $record-> $stmt->fetch();
    if ($record) {
        echo htmlentities($record['username']);
    }
}
?>