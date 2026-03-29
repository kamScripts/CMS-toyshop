<?php
//enables type declarations
declare(strict_types=1);
//autoloader automatic class import.
//Directory separator for path compatibility on any OS.
spl_autoload_register(function ($className){
    require_once __DIR__ . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . $className . ".php";
});
//Enable CORS (In production change to specific origin)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header("content-type: application/json; charset=UTF-8");

$parts = explode("/", $_SERVER["REQUEST_URI"]);
$requested = $parts[3];
print_r($parts);
$itemId = $parts[4] ?? null;
$configPath = __DIR__ . DIRECTORY_SEPARATOR . "config.ini" ;

try {
    $config = Utilities::loadConfig($configPath);
    $db = new Database(
        $config['host'],
        $config['db'],
        $config['user'],
        $config['password'],
        $config['charset']
    );

    switch ($requested) {
        case "carModels":
            $brandGateway = new BrandGateway($db);
            $productController = new ProductController($brandGateway);
            $productController->processRequest($_SERVER["REQUEST_METHOD"], $itemId);
            break;
        default:
            http_response_code(404);
            exit;
    }


} catch (RuntimeException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]); // In production log
}






