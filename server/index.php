<?php
/**
 *
 *
 *
 */
//enables type declarations
declare(strict_types=1);
// Extra de
error_reporting(E_ALL);
ini_set('display_errors', '0');
session_start();

//autoloader automatic class import.
//Directory separator for path compatibility on any OS.
spl_autoload_register(function ($className){
    require_once __DIR__ . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . $className . ".php";
});

header("Access-Control-Allow-Origin: https://kamscripts.site");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header("content-type: application/json; charset=UTF-8");

//IMPORTANT: URI PARTS ADJUSTED FOR PRODUCTION, LOCALHOST NEED CHECKING PATH
$parts = explode("/", $_SERVER["REQUEST_URI"]);
$requested = $parts[3]; // first part of URL like carModels
//print_r($parts); // VERIFY URL



$itemId = $parts[4] ?? null; //  id of  full product specs/subCategory
$detailId = $parts[5] ?? null; // detail Id - carModels/brand/1
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
    $brandGateway = new BrandGateway($db);
    $scaleGateway = new ScaleGateway($db);
    $collectionGateway = new CollectionGateway($db);
    $modelGateway = new ModelGateway($db);
    $variantGateway = new VariantGateway($db);
    switch ($requested) {
        case "carModels":
            $productController = new ProductController
            (
                $brandGateway,
                $scaleGateway,
                $collectionGateway,
                $modelGateway,
                $variantGateway
            );
            $productController->handleRequest($_SERVER["REQUEST_METHOD"], $itemId,$detailId);
            break;
        case "upload":
            $uploadsController = new UploadController();
            $uploadsController->handleRequest($_SERVER["REQUEST_METHOD"]);
            break;
        case "users":
            $userGateway = new UserGateway($db);
            $userController = new UserController($userGateway);
            $userController->handleRequest($_SERVER["REQUEST_METHOD"], $itemId);
            break;
        default:
            http_response_code(404);
            exit;
    }


} catch (RuntimeException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}






