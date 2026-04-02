<?php

/**
 * TODO: add update and delete method that uses correct gateway to edit records from input array
 * TODO: create input validation function
 * TODO: Create Response class to eliminate repetitive response structuring which is extra fragile to changes
 */
class ProductController {
    private BrandGateway $brandGateway;
    private ScaleGateway $scaleGateway;
    private CollectionGateway $collectionGateway;
    private ModelGateway $modelGateway;
    private VariantGateway $variantGateway;
    private array $tableMap;

    public function __construct(
        BrandGateway $brandGateway,
        ScaleGateway $scaleGateway,
        CollectionGateway $collectionGateway,
        ModelGateway $modelGateway,
        VariantGateway $variantGateway)
    {
        $this->brandGateway = $brandGateway;
        $this->scaleGateway = $scaleGateway;
        $this->collectionGateway = $collectionGateway;
        $this->modelGateway = $modelGateway;
        $this->variantGateway = $variantGateway;

        $this->tableMap = [   //dynamic tableMap
            "brand" => [
                "gateway" => "brandGateway",
                "columns" => Utilities::extractFromAssociativeArray($this->brandGateway->describeTable(), 'Field'),
                "types"   => Utilities::extractFromAssociativeArray($this->brandGateway->describeTable(), 'Type'),
                "nullables"=> Utilities::extractFromAssociativeArray($this->brandGateway->describeTable(), 'Null'),
                ],
            "scale" => [
                    "gateway" => "scaleGateway",
                    "columns" => Utilities::extractFromAssociativeArray($this->scaleGateway->describeTable(), 'Field'),
                    "types"   => Utilities::extractFromAssociativeArray($this->scaleGateway->describeTable(), 'Type'),
                    "nullables"=> Utilities::extractFromAssociativeArray($this->scaleGateway->describeTable(), 'Null'),
                ],
            "collection" => [
                    "gateway" => "collectionGateway",
                    "columns" => Utilities::extractFromAssociativeArray($this->collectionGateway->describeTable(), 'Field'),
                    "types"   => Utilities::extractFromAssociativeArray($this->collectionGateway->describeTable(), 'Type'),
                    "nullables"=> Utilities::extractFromAssociativeArray($this->collectionGateway->describeTable(), 'Null'),
                ],
            "model" => [
                    "gateway" => "modelGateway",
                    "columns" => Utilities::extractFromAssociativeArray($this->modelGateway->describeTable(), 'Field'),
                    "types"   => Utilities::extractFromAssociativeArray($this->modelGateway->describeTable(), 'Type'),
                    "nullables"=> Utilities::extractFromAssociativeArray($this->modelGateway->describeTable(), 'Null'),
                ],
            "variant" => [
                    "gateway" => "variantGateway",
                    "columns" => Utilities::extractFromAssociativeArray($this->variantGateway->describeTable(), 'Field'),
                    "types"   => Utilities::extractFromAssociativeArray($this->variantGateway->describeTable(), 'Type'),
                    "nullables"=> Utilities::extractFromAssociativeArray($this->variantGateway->describeTable(), 'Null'),
                ],
        ];

    }
    public function show():void{ // TEST function --> REMOVE BEFORE SUBMISSION
        print_r($this->tableMap);
    }
    public function handleRequest(string $method, ?string $id, ?string $detailId): void
    {
        if (($id)) {
            $this->processItemRequest($method, $id);
        } else {
            $this->processCollectionsRequest($method);
        }
    }
    private function processItemRequest(string $method, string $id): void
    {
        if(is_numeric($id)) {
            $product = $this->variantGateway->getSingleJoin($id);

            if (!$product) {
                http_response_code(404);
                echo json_encode(["status"=>"error","message" => "Resource not found."]);
                return;
            }
            switch ($method) {
                case "PATCH":
                    $data = (array)json_decode(file_get_contents("php://input"), TRUE);
                    $rows = $this->updateProduct($id, $data);
                    echo json_encode([
                        "status" => "success",
                        "message" => "Product(id=$id)  updated successfully.",
                        "rows" => $rows,
                        "data" => $data
                    ]);
                    break;
                case "GET": // full product item details
                    echo json_encode([
                        "status" => "success",
                        "message" => "Product(id=$id)  updated successfully.",
                        "rows" => 1,
                        "data" => $product
                    ]);
                    break;

                default:
                    http_response_code(405);
                    header("Allow: GET,PATCH");
            }
        } else { //if itemId - is category
            if ($method == "GET") {
                switch ($id) {
                    case "brand":
                        echo json_encode([
                            "status" => "success",
                            "message" => "$id successfully retrieved.",
                            "data" => $this->brandGateway->getAll()
                            ]);
                        break;
                    case "scale":
                        echo json_encode([
                            "status" => "success",
                            "message" => "$id successfully retrieved.",
                            "data" => $this->scaleGateway->getAll()
                        ]);
                        break;
                    case "collection":
                        echo json_encode([
                            "status" => "success",
                            "message" => "$id successfully retrieved.",
                            "data" => $this->collectionGateway->getAll()
                        ]);
                        break;
                    case "model":
                        echo json_encode([
                            "status" => "success",
                            "message" => "$id successfully retrieved.",
                            "data" => $this->modelGateway->getAll()
                        ]);
                        break;
                    case "variant":
                        echo json_encode([
                            "status" => "success",
                            "message" => "$id successfully retrieved.",
                            "data" => $this->variantGateway->getAll()
                        ]);
                        break;
                    default:
                        http_response_code(405);
                }
            }
            if ($method == "POST") {
                $data = (array)json_decode(file_get_contents("php://input"), TRUE);

                switch ($id) {
                    case "brand":
                        $result = $this->createProduct($data, "brand");
                        if ($result>0) {
                            echo json_encode([
                                "status" => "success",
                                "message" => " item with index $result successfully inserted."
                            ]);
                        }
                        else {
                            echo json_encode([
                                "status" => "error"
                            ]);
                        }
                        break;
                    case "scale":
                        $result = $this->createProduct($data, "scale");
                        if ($result>0) {
                            echo json_encode([
                                "status" => "success",
                                "message" => " item with index $result successfully inserted."
                            ]);
                        }
                        else {
                            echo json_encode([
                                "status" => "error"
                            ]);
                        }
                        break;
                    case "collection":
                        $result = $this->createProduct($data, "collection");
                        if ($result>0) {
                            echo json_encode([
                                "status" => "success",
                                "message" => " item with index $result successfully inserted."
                            ]);
                        }
                        else {
                            echo json_encode([
                                "status" => "error"
                            ]);
                        }
                        break;
                    case "model":
                        $result = $this->createProduct($data, "model");
                        if ($result>0) {
                            echo json_encode([
                                "status" => "success",
                                "message" => " item with index $result successfully inserted."
                            ]);
                        }
                        else {
                            echo json_encode([
                                "status" => "error"
                            ]);
                        }
                        break;
                    case "variant":
                        $result = $this->createProduct($data, "variant");
                        if ($result>0) {
                            echo json_encode([
                                "status" => "success",
                                "message" => " item with index $result successfully inserted."
                            ]);
                        }
                        else {
                            echo json_encode([
                                "status" => "error"
                            ]);
                        }
                        break;
                    default:
                        http_response_code(405);
                }
            }


        }
    }
    private function processCollectionsRequest(string $method): void
    {
        switch ($method) {
            case "GET": //get method returns a list of products
                $products = $this->variantGateway->getAllProducts();
                echo json_encode(["products" => $products]);
                break;

            case "DELETE":
                $input = (array) json_decode(file_get_contents("php://input"), true);

                $productId   = (int) ($input['id'] ?? -1);
                $tableToDelete = $input['name'] ?? null;

                if ($productId <= 0) {
                    http_response_code(400);
                    echo json_encode(["status"=>"error", "message" => "Invalid or missing product ID."]);
                    break;
                }

                $rows = $this->deleteProduct($productId, $tableToDelete);

                echo json_encode([
                    "message" => "Table '{$tableToDelete}' for product(id=$productId) deleted successfully.",
                    "rows"    => $rows,
                    "table"   => $tableToDelete,
                    "status"      => "success"
                ]);
                break;
            default:
                http_response_code(405); // method not allowed http response code.
                header("Allow: GET,POST DELETE");

        }
    }


    /**
     * TODO: add transaction, test
     * @param array $data: new records data
     * @return int: affected rows
     */
    private function createProduct(array $data, string $tableName): int {
        $validator = new InputValidator($this->tableMap);
        $errors = [];

        $validationErrors = $validator->validateInput($tableName, $data, true);
        if (!empty($validationErrors)) {
            $errors = $validationErrors;
            http_response_code(422); //Unprocessable entity.
            echo json_encode([
                "status" => "error",
                "message" => "Validation failed.",
                "errors" => $errors
            ]);
            return 0;
        }
        $affectedRows = 0;
        $gateway = $this->{$this->tableMap[$tableName]["gateway"]};
        $affectedRows += $gateway->create($data);
        return $affectedRows;
    }

    /**TODO: make as Transaction
     * @param int $currentId: ID of current record
     * @param array $data: collection of records to update like "field" => $newValues
     * @return int affectedRows: number of affected rows
     */
    private function updateProduct(int $currentId, array $data): int
    {
        $affectedRows = 0;
        try {
            foreach ($this->tableMap as $table) {
                // Extract only the columns that belong to this table
                $changes = array_intersect_key($data, array_flip($table["columns"]));

                if (empty($changes)) {
                    continue;
                }
                if(($table["columns"])) {
                    $gateway = $this->{$table["gateway"]};
                    $affectedRows += $gateway->update($currentId, $changes); }

            }
        } catch (Exception $ex) {
            http_response_code(500);
            echo json_encode($ex->getMessage());
        } finally {
            return $affectedRows;
        }
    }

    /**
     * TODO: transactions
     * @param int $productId
     * @param string $tableName
     * @return int
     */
    private function deleteProduct(int $productId, string $tableName): int
    {
        $deletedRows = 0;

        try {
            // Delete from specific table only
            if (array_key_exists($tableName, $this->tableMap)) {
                $config = $this->tableMap[$tableName];
                $gateway = $this->{$config["gateway"]};
                $deletedRows += $gateway->delete($productId);
            }
            return $deletedRows;

        } catch (Exception $e) {
            http_response_code(500);
            echo "Delete failed: " . $e->getMessage();
            return 0;
        } finally {
            return $deletedRows;
        }
    }
}
