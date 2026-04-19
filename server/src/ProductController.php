<?php

/** Product Controller class - CRUD operations
 * handle /carModels ; /carModels/id ; /carModels/category ; /carModels/category/id
 * TODO: Create Response class to eliminate repetitive response structuring which is extra fragile to changes
 * TODO: Improve tableMaps and its init - reduce repetitive code
 *
 */
class ProductController {
    private BrandGateway $brandGateway;
    private ScaleGateway $scaleGateway;
    private CollectionGateway $collectionGateway;
    private ModelGateway $modelGateway;
    private VariantGateway $variantGateway;
    private array $tableMap;

    /** Initialize Controller with table gateways, and tables schema map for correct validation
     * @param BrandGateway $brandGateway
     * @param ScaleGateway $scaleGateway
     * @param CollectionGateway $collectionGateway
     * @param ModelGateway $modelGateway
     * @param VariantGateway $variantGateway
     */
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

        $this->tableMap = [   //tables schema
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

    /**Request router process based on URI path
     * @param string $method: http method
     * @param string|null $id: URI part after products endpoint
     * @param string|null $detailId: URI part after $id
     * @return void
     */
    public function handleRequest(string $method, ?string $id, ?string $detailId): void
    {
        if (($id)) {
            $this->processItemRequest($method, $id, $detailId);
        } else {
            $this->processCollectionsRequest($method);
        }
    }

    /** process db CRUD, and view single product record.
     * Handles methods: PUT,GET,PATCH,DELETE,
     * @param string $method:
     * @param string $id:tableName or product_id
     * @param string|null $detailId:  record id
     * @return void
     */
    private function processItemRequest(string $method, string $id, ?string $detailId): void
    {
        if(is_numeric($id)) {
            $product = $this->variantGateway->getSingleJoin($id);

            if (!$product) {
                http_response_code(404);
                echo json_encode(["status"=>"error","message" => "Resource not found."]);
                return;
            }
            switch ($method) {
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
                    header("Allow: GET");
            }
        }
        else { //if itemId - is category
            //Unnecessary switch - reduce repetitive code in GET
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
                $result = $this->createProduct($data, $id);
                if ($result>0) {
                    http_response_code(201);
                    echo json_encode([
                        "status" => "success",
                        "message" => "item with index $result successfully inserted."
                    ]);
                }
                else {
                    http_response_code(400);
                    echo json_encode([
                        "status" => "error"
                    ]);
                }
            }
            if ($method == "DELETE") {
                if ($detailId <= 0) {
                    http_response_code(400);
                    echo json_encode(["status"=>"error", "message" => "Invalid or missing product ID."]);
                }

                $rows = $this->deleteProduct($id, $detailId);

                echo json_encode([
                    "message" => "Item deleted successfully.",
                    "rows"    => $rows,
                    "table"   => $id,
                    "status"      => "success"
                ]);
            }
            if ($method == "PATCH") {
                if ($detailId <= 0) {
                    http_response_code(400);
                    echo json_encode(["status"=>"error", "message" => "Invalid or missing product ID."]);
                    return;
                }
                $data = (array)json_decode(file_get_contents("php://input"), TRUE);
                $rows = $this->updateProduct($data, $id, $detailId);
                if ($rows > 0) {
                    echo json_encode([
                        "status" => "success",
                        "message" => "Product(id=$id)  updated successfully.",
                        "rows" => $rows,
                        "data" => $data
                    ]);

                } else {
                    http_response_code(400);
                    echo json_encode([
                        "status" => "error"
                    ]);
                }
            }
        }
    }

    /**Return joint data of whole products collection, handle GET HTTP request.
     * @param string $method: http method
     * @return void
     */
    private function processCollectionsRequest(string $method): void
    {
        switch ($method) {
            case "GET": //get method returns a list of products
                $products = $this->variantGateway->getAllProducts();
                echo json_encode([
                    "status" => "success",
                    "products" => $products]);
                break;
            default:
                http_response_code(405); // method not allowed http response code.
                header("Allow: GET");
        }
    }


    /**Insert single record.
     * @param array $data: new record data.
     * @param string $tableName : insert target.
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

    /** Update single record.
     * @param int $currentId: ID of current record
     * @param array $data: collection of records to update like "field" => $newValues
     * @return int affectedRows: number of affected rows
     */
    private function updateProduct( array $data, string $tableName, int $currentId): int
    {
        $validator = new InputValidator($this->tableMap);

        $validationErrors = $validator->validateInput($tableName, $data);
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
        $gateway = $this->{$this->tableMap[$tableName]["gateway"]};


        return $gateway->update($currentId, $data);

    }

    /** Delete single table record.
     * @param int $productId
     * @param string $tableName
     * @return int: 1 if success
     */
    private function deleteProduct(string $tableName, int $productId): int
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
