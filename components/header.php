<?php
    session_start();
    require_once 'components/pdo_functions.php';

    $username = '';
    $mainLogoPath='images/ModelCarsLogo.png';

    if (isset($_SESSION['user'])) {
        $username_htmlentities = htmlentities($_SESSION['user']);
        $loggedIn = TRUE;
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
    <title>BestCarToys</title>
</head>
<body>
    <header>        
        <div id='logoContainer'>
            <img src=<?=$mainLogoPath;?> alt='Company Logo picture' class='mainLogo'>
            <h1>BestCarToys</h1>
        </div> 
        <!--TODO: make navbar a component, a class for instances in header and footer-->
        <nav class='navbar' aria-label="Main site navigation">
            <ul class='linksList'>
                <li><a href="products.php">Products</a></li>
                <li><a href="aboutus.php">About us</a></li>
                <li><a href="findus.php">Find us</a></li>
<?php
    if (isset($_SESSION['user']) && $loggedIn) {
        echo "<li><a href='dashboard.php>Dashboard</a></li>";
    }
?>
            </ul>
        </nav>       

        <form
            id='searchBar' 
            method='post' 
            action='' 
            role='search' 
            aria-label='Products search'
        >
          <label for="search" class="hiddenLabel">Search products</label>  
          <input type="text" id="search" name="search-product" placeholder="Search Products...">
        </form>
    </header>
    <main>
