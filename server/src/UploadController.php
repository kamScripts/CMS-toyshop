<?php

class UploadController
{

    public function __construct(private int $max_size, private int $max_width, private int $max_height)
    {
    }
    public function handleRequest(string $method):void {
        $allowed_image_types = array("png", "jpeg", "gif");
        if ($method !== "POST") {
            http_response_code(405);
            header("Allow: POST");
            echo json_encode(["message" => "Method not allowed.","status" => "error"]);

        }
        if (isset($_FILES["image"]["name"])) {
            $file = $_FILES["image"];
            $saveTo = __DIR__ . "/../images/" . $file["name"];
            $file_extension = pathinfo($saveTo, PATHINFO_EXTENSION);

            if (!in_array($file_extension, $allowed_image_types)) {
                http_response_code(400);
                header("Allow: POST");
                echo json_encode(["message" => "Only JPEG and PNG files are allowed.","status" => "error"]);
            }
            if ($file["size"] > $this->max_size) {
                http_response_code(400);
                header("Allow: POST");
                echo json_encode(["message" => "File is too large.","status" => "error"]);
            }
            $dims = getimagesize($file["tmp_name"]);
            if ($dims[0] > $this->max_width || $dims[1] > $this->max_height) {
                http_response_code(400);
                header("Allow: POST");
                echo json_encode(["message" => "File is too large.","status" => "error"]);
            }
            move_uploaded_file($file["tmp_name"], $saveTo);
            echo json_encode(["message" => "File uploaded.","status" => "success"]);


        }
    }
}