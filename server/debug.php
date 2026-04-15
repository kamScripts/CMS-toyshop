<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Debug Page</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Current Directory: " . __DIR__ . "<br><br>";

echo "<strong>Checking server folder:</strong><br>";
if (is_dir(__DIR__ . '/server')) {
    echo "✓ server folder exists<br>";
} else {
    echo "✗ server folder NOT found!<br>";
}

echo "<strong>Checking src folder:</strong><br>";
if (is_dir(__DIR__ . '/server/src')) {
    echo "✓ server/src folder exists<br>";
} else {
    echo "✗ server/src folder NOT found!<br>";
}

echo "<br><strong>Testing autoloader:</strong><br>";
try {
    require_once __DIR__ . '/server/src/Database.php';
    echo "✓ Database.php loaded successfully<br>";
} catch (Exception $e) {
    echo "✗ Failed to load Database.php: " . $e->getMessage() . "<br>";
}

echo "<br><strong>Testing config.ini:</strong><br>";
$configPath = __DIR__ . '/server/config.ini';
if (file_exists($configPath)) {
    echo "✓ config.ini found<br>";
} else {
    echo "✗ config.ini NOT found at: " . $configPath . "<br>";
}
?>