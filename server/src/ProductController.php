<?php
class ProductController {
    private BrandGateway $brandGateway;
    private ScaleGateway $scaleGateway;
    public function __construct(
        BrandGateway $brandGateway,
        ScaleGateway $scaleGateway) {
        $this->brandGateway = $brandGateway;
        $this->scaleGateway = $scaleGateway;
    }

    public function handleRequest(string $method, ?string $id): void {
        if ($id) {
            $this->processItemRequest($method, $id);
        } else {
            $this->processCollectionsRequest($method);
        }
    }
    private function processItemRequest(string $method, string $id): void {
        $product = $this->brandGateway->get($id);

        if(!$product) {
            http_response_code(404);
            echo json_encode(["message" => "Resource not found."]);
            return;
        }
        switch($method) {
            case "PATCH":
                $data = (array) json_decode(file_get_contents("php://input"), TRUE); //in real project $data = $_POST
                $rows = $this->brandGateway->update($product, $data);
                echo json_encode([
                    "message" => "Product(id=$id)  updated successfully.",
                    "rows" => $rows,
                ]);
                break;
            case "GET":
                echo json_encode($product);
                break;
            case "DELETE":
                $rows = $this->brandGateway->delete($id);
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
}