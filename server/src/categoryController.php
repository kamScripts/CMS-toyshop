<?php
declare(strict_types=1);

/**
 * Category Controller - Simple crud for lookup tables
 * Handles: brand, scale, collection, model handles CREATE RECORD, GET ALL, DELETE
 * Get Single record handle by /modelCars/{table}/{id}
 * URL pattern: /categories/{table}   and   /categories/{table}/{id}
 */
class CategoryController
{
    //Reduce access to DB only to necessary tables for CMS app.
    private array $allowedTables = ['brand', 'scale', 'collection', 'model'];

    private BrandGateway $brandGateway;
    private ScaleGateway $scaleGateway;
    private CollectionGateway $collectionGateway;
    private ModelGateway $modelGateway;

    public function __construct(
        BrandGateway $brandGateway,
        ScaleGateway $scaleGateway,
        CollectionGateway $collectionGateway,
        ModelGateway $modelGateway
    ) {
        $this->brandGateway = $brandGateway;
        $this->scaleGateway = $scaleGateway;
        $this->collectionGateway = $collectionGateway;
        $this->modelGateway = $modelGateway;
    }

    public function handleRequest(string $method, ?string $table, ?string $id): void
    {
        // Check if table is allowed
        if (!$table || !in_array($table, $this->allowedTables, true)) {
            http_response_code(404);
            echo json_encode([
                "status" => "error",
                "message" => "Invalid category table."
            ]);
            return;
        }

        switch ($method) {
            case 'GET':
                $this->getAll($table);
                break;

            case 'POST':
                $this->create($table);
                break;

            case 'DELETE':
                $this->delete($table, $id);
                break;

            default:
                http_response_code(405);
                header("Allow: GET, POST, DELETE");
                echo json_encode(["status" => "error", "message" => "Method not allowed"]);
        }
    }

    private function getAll(string $table): void
    {
        $gateway = $this->getGateway($table);
        $data = $gateway->getAll();

        echo json_encode([
            "status" => "success",
            "message" => "$table retrieved successfully.",
            "data" => $data
        ]);
    }

    private function create(string $table): void
    {
        $data = (array) json_decode(file_get_contents("php://input"), true);

        if (empty($data)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "No data received"]);
            return;
        }

        $gateway = $this->getGateway($table);
        $newId = $gateway->create($data);

        if ($newId) {
            http_response_code(201);
            echo json_encode([
                "status" => "success",
                "message" => "New $table created successfully.",
                "id" => $newId
            ]);
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Failed to create $table."]);
        }
    }

    private function delete(string $table, ?string $id): void
    {
        if (!$id || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Invalid or missing ID."]);
            return;
        }

        $gateway = $this->getGateway($table);
        $rows = $gateway->delete($id);

        if ($rows > 0) {
            echo json_encode([
                "status" => "success",
                "message" => "$table deleted successfully.",
                "rows" => $rows
            ]);
        } else {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Record not found."]);
        }
    }

    private function getGateway(string $table)
    {
        return match ($table) {
            'brand'       => $this->brandGateway,
            'scale'       => $this->scaleGateway,
            'collection'  => $this->collectionGateway,
            'model'       => $this->modelGateway,
            default       => throw new Exception("Invalid table")
        };
    }
}