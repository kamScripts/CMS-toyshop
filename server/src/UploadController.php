<?php

/** TODO:update upload path to public/images dir.
 *Image upload Controller class
 * Handles image upload and validates image details.
 */
class UploadController
{
    /**Initialise controller with liekly default values.
     * @param int $max_size: max image size (Bytes).
     * @param int $max_width: max width(pixels).
     * @param int $max_height: max height(pixels).
     * @param array $allowed_extensions allowed file types.
     */
    public function __construct(

        private int $max_size = 5000000,
        private int $max_width = 1280,
        private int $max_height = 1280,
        private array $allowed_extensions = ["jpg", "jpeg", "png"],
    ){}

    /**Handles HTTP requests
     * @param string $method: HTTP request method
     * @return void
     */
    public function handleRequest(string $method):void {

        if ($method !== "POST") {
            http_response_code(405);
            header("Allow: POST");
            echo json_encode(["message" => "Method not allowed.","status" => "error"
            ]);
        }
        $this->handleImageUpload();
    }

    /**Core Function, validates file properties,
     * then sanitises filename removing non-alpha-numeric characters excluding hyphen and underscore.
     * File is moved to a specified directory on a server (server/images/products/).
     * Response 200 if success and formated  otherwise 400
     * @return void
     */
    private function handleImageUpload():void {
        //Correct Upload validation
        //UPLOAD_ERR_OK - 'There is no error, the file uploaded with success.'
        if(empty($_FILES["image"]) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(["message" => "File upload error.","status" => "error"]);
            return;
        }
        $file = $_FILES["image"];
        // file size validation
        if ($file["size"] > $this->max_size) {

            http_response_code(400);
            $mb = $this->max_size/1000000;
            echo json_encode([
                "message" => "File exceeded maximum allowed size($mb MB).",
                "status" => "error"]);
            return;
        }
        // split path - sanitise filename and validate extension
        $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        $filename = strtolower(pathinfo($file["name"], PATHINFO_FILENAME));
        $filename = preg_replace("/[^a-zA-Z0-9_-]/", "", $filename);// replace dangerous characters
        //verify if extension allowed by controller.
        if (!in_array($file_extension, $this->allowed_extensions)) {
            http_response_code(400);
            echo json_encode(["message" => "File extension not allowed.","status" => "error"]);
            return;
        }
        // image dimensions validation
        $dims = getimagesize($file["tmp_name"]);
        if (!$dims) {
            http_response_code(400);
            echo json_encode(["message" => "Uploaded file invalid.","status" => "error"]);
            return;
        }
        $width = $dims[0];
        $height = $dims[1];
        if($width > $this->max_width || $height > $this->max_height) {
            http_response_code(400);
            echo json_encode([
                "message" => "File dimensions exceeds maximum allowed size.",
                "status" => "error",
                "details" => [
                    "width" => $width,
                    "height" => $height,
                    "max_width" => $this->max_width,
                    "max_height" => $this->max_height,
                    "max_size" => $this->max_size,
                    "allowed_extensions" => $this->allowed_extensions
                ]
            ]);
            return;
        }
        //After sanitising name, validating image details, safely join file path.
        $cleanFilename = $filename . "." . $file_extension;

        $destination = __DIR__ . "/../../assets/products/" . $cleanFilename;
        //save file to the destination
        if (move_uploaded_file($file["tmp_name"], $destination)) {
            $imagePath = $cleanFilename;

            echo json_encode([
                "message"    => "Image uploaded successfully",
                "status"     => "success",
                "filename"   => $cleanFilename,
                "image_path" => $imagePath,
                "size"       => $file['size']
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "message" => "Failed to save the uploaded file.",
                "status"  => "error",
            ]);
        }
    }
}