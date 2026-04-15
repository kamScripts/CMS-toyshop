<?php
declare(strict_types=1);

class CategoryController
{
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
        if (!$table || !in_array($table, $this->allowedTables, true)) {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Invalid category table: " . ($table ?? 'null')]);
            return;
        }

        try {
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
                    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Error in " . $table,
                "debug" => $e->getMessage()
            ]);
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

        $gateway = $this->getGateway($table);
        $newId = $gateway->create($data);

        if ($newId > 0) {
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
    $rows = $gateway->delete((string)$id);   // ← Cast to string

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
            default       => throw new Exception("Invalid table: " . $table)
        };
    }
}