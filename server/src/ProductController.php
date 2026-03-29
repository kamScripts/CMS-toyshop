<?php
class ProductController {
    private BrandGateway $brandGateway;
    public function __construct(BrandGateway $brandGateway) {
        $this->brandGateway = $brandGateway;
    }
    // ? przed 2 para to nullable val - innymi slowy optional argument.
    public function processRequest(string $method, ?string $id): void {
        if ($id) {
            $this->processResourcesRequest($method, $id);
        } else {
            $this->processCollectionsRequest($method);
        }
    }
    private function processResourcesRequest(string $method, string $id): void {
        $product = $this->gateway->get($id);

        if(!$product) {
            http_response_code(404);
            echo json_encode(["message" => "Resource not found."]);
            return;
        }
        switch($method) {
            case "PATCH":
                $data = (array) json_decode(file_get_contents("php://input"), TRUE); //in real project $data = $_POST

                $errors = $this->getValidationErrors($data, false);

                if (! empty($errors)) {
                    http_response_code(422);// unprocessable entity http response code.
                    echo json_encode(["errors"=>$errors]);
                    break;
                }

                $rows = $this->gateway->update($product, $data);
                echo json_encode([
                    "message" => "Product(id=$id)  updated successfully.",
                    "rows" => $rows,
                ]);
                break;
            case "GET":
                echo json_encode($product);
                break;
            case "DELETE":
                $rows = $this->gateway->delete($id);
                echo json_encode([
                    "message" => "Product(id=$id)  deleted successfully.",
                    "rows" => $rows,
                ]);
                break;
            default:
                http_response_code(405);
                header("Allow: GET,PATCH,DELETE");
        }
    }
    private function processCollectionsRequest(string $method): void {
        switch ($method) {
            case "GET": //get method returns a list of products
                echo json_encode(["products" => $this->brandGateway->getAll()]);
                break;
            case "POST":
                // json_decode returns null if post request is empty,
                // therefore cast to array -> if null return empty array instead of null
                $data = (array)json_decode(file_get_contents("php://input"), TRUE); //in real project $data = $_POST

                $errors = $this->getValidationErrors($data);

                if (! empty($errors)) {
                    http_response_code(422);// unprocessable entity http response code.
                    echo json_encode(["errors"=>$errors]);
                    break;
                }

                $id = $this->brandGateway->create($data);

                http_response_code(201); // CREATE http response code.
                echo json_encode(
                    ["message"=>"Product created",
                        "id"=> $id
                    ]);
                break;
            default:
                http_response_code(405); // method not allowed http response code.
                header("Allow: GET,POST");

        }
    }
    //validate name only if row is new
    /*
    private function getValidationErrors(array $data ,bool $is_new = true): array {
        $errors = [];
        if ($is_new && empty($data["name"])) {
            $errors[] = "Name is required";
        }
        if (array_key_exists("size", $data)) {

            if (filter_var($data["size"], FILTER_VALIDATE_INT) === false) {
                $errors[] = "Size must be an integer";
            }
        }
        return $errors;
    }*/

}