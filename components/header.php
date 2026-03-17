<?php
    session_start();
    require_once 'components/pdo_functions.php';

    $username = '';
    $mainLogoPath='images/BestCarToysLogo.png';

    if (isset($_SESSION['user'])) {
        $username_htmlentities = htmlentities($_SESSION['user']);
        $loggedIn = TRUE;
        $username = "<p>$username_htmlentities</p>";
    } else {
        $loggedIn = FALSE;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' href='stylesheets/styles.css'>    
    <title>ModelCarsOnline</title>
</head>
<body>
    <header>        
        <div id='logoContainer'>
            <img src=<?=$mainLogoPath;?> alt='Company Logo picture' class='mainLogo'>
        </div>
        <nav class='navbar'>
            <ul>
                <li>Nav-Link</li>
                <li>Nav-Link</li>
                <li>Nav-Link</li>
                <li>Nav-Link</li>
                <li>Nav-Link</li>
            </ul>
        </nav>
    </header>
</body>
</html>