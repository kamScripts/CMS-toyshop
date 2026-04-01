<?php

/**
 * TODO: add update and delete method that uses correct gateway to edit records from input array
 * TODO: create input validation function
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
            "columns" => Utilities::extractFromAssociativeArray($this->brandGateway->describeTable())
            ],
        "scale" => [
                "gateway" => "scaleGateway",
                "columns" => Utilities::extractFromAssociativeArray($this->scaleGateway->describeTable())
            ],
        "collection" => [
                "gateway" => "collectionGateway",
                "columns" => Utilities::extractFromAssociativeArray($this->collectionGateway->describeTable())
            ],
        "model" => [
                "gateway" => "modelGateway",
                "columns" => Utilities::extractFromAssociativeArray($this->modelGateway->describeTable())
            ],
        "variant" => [
                "gateway" => "variantGateway",
                "columns" => Utilities::extractFromAssociativeArray($this->variantGateway->describeTable())
            ],
        ];

    }
    public function handleRequest(string $method, ?string $id): void
    {
        if ($id) {
            $this->processItemRequest($method, $id);
        } else {
            $this->processCollectionsRequest($method);
        }
    }
    private function processItemRequest(string $method, string $id): void
    {
        $product = $this->variantGateway->getSingleJoin($id);

        if(!$product) {
            http_response_code(404);
            echo json_encode(["message" => "Resource not found."]);
            return;
        }
        switch($method) {
            case "PATCH":
                $data = (array) json_decode(file_get_contents("php://input"), TRUE);
                $rows=$this->updateProduct($id,$data);
                echo json_encode([
                    "message" => "Product(id=$id)  updated successfully.",
                    "rows" => $rows,
                    "data" => $data
                ]);
                break;
            case "GET":
                echo json_encode($product);
                break;

            default:
                http_response_code(405);
                header("Allow: GET,PATCH");
        }
    }
    private function processCollectionsRequest(string $method): void
    {
        switch ($method) {
            case "GET": //get method returns a list of products
                $products = $this->variantGateway->getAllProducts();
                echo json_encode(["products" => $products]);
                break;
            case "POST":
                // json_decode returns null if post request is empty,
                // therefore cast to array -> if null return empty array instead of null
                $data = (array)json_decode(file_get_contents("php://input"), TRUE); //in real project $data = $_POST
                $id = $this->createProduct($data);

                http_response_code(201); // CREATE http response code.
                echo json_encode(
                    ["message"=>"Product created",
                        "id"=> $id
                    ]);
                break;
            case "DELETE":
                $input = (array) json_decode(file_get_contents("php://input"), true);

                $productId   = (int) ($input['id'] ?? -1);
                $tableToDelete = $input['name'] ?? null;

                if ($productId <= 0) {
                    http_response_code(400);
                    echo json_encode(["message" => "Invalid or missing product ID."]);
                    break;
                }

                $rows = $this->deleteProduct($productId, $tableToDelete);

                $message = $tableToDelete
                    ? "Table '{$tableToDelete}' for product(id=$productId) deleted successfully."
                    : "Product(id=$productId) fully deleted successfully.";

                echo json_encode([
                    "message" => $message,
                    "rows"    => $rows,
                    "table"   => $tableToDelete,
                    "id"      => $productId
                ]);
                break;
            default:
                http_response_code(405); // method not allowed http response code.
                header("Allow: GET,POST DELETE");

        }
    }


    /**TODO: add transaction, test
     * @param array $data: new records data
     * @return int: number of created records
     */
    private function createProduct(array $data): int {
        $affectedRows = 0;
        try {
            foreach ($this->tableMap as $table) {
                $copy = unserialize(serialize($table));
                array_shift($copy["columns"]);
                $values = array_intersect_key($data, array_flip($copy["columns"]));
                if (empty($values)) {
                    continue;
                }
                // Only call create() if values are equal to columns except id column
                if (array_keys($values) === array_values($copy["columns"])) {
                    $gateway = $this->{$copy["gateway"]};
                    $affectedRows +=(int) $gateway->create($values);
                }

            }
        } catch (Exception $ex) {
            http_response_code(500);
            echo json_encode($ex->getMessage());
        } finally {
            return $affectedRows;
        }
    }

    /**TODO: make as Transaction
     * @param int $currentId: ID of current record
     * @param array $data: collection of records to update like "field" => $newValues
     * @return int affectedRows: number of affected rows
     */
    private function updateProduct(int $currentId, array $data): int
    {
        $affectedRows = 0;

        foreach ($this->tableMap as $table) {
            // Extract only the columns that belong to this table
            $changes = array_intersect_key($data, array_flip($table["columns"]));
            if (empty($changes)) {
                continue;
            }
            $gateway = $this->{$table["gateway"]};
            $affectedRows += $gateway->update($currentId, $changes);   // clean signature

        }

        return $affectedRows;
    }

    /** TODO: transactions
     * @param int $productId
     * @param string|null $tableName
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
            echo "Delete failed: " . $e->getMessage();
            return 0;
        } finally {
            return $deletedRows;
        }
    }
}
