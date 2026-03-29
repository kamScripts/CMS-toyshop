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
header("Access-Control-Allow-Methods: GET, POST,PATCH, OPTIONS");
header("content-type: application/json; charset=UTF-8");

$parts = explode("/", $_SERVER["REQUEST_URI"]);
print_r($parts);
//Process
switch ($parts[3]) {
    case "carModels":
        echo "carModels";
        break;
    default:
        http_response_code(404);
        exit;
}



