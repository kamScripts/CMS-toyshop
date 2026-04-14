<?php

class UserController {
    private UserGateway $userGateway;
    private UserValidator $userValidator;
    public function __construct(UserGateway $userGateway) {
        $this->userGateway = $userGateway;
        $this->userValidator = new UserValidator();
    }
    public function handleRequest(string $method, ?string $action): void
    {
        // Handle actions that use a specific resource ID (numeric)
        if (is_numeric($action)) {
            $this->handleUserById($method, (int)$action);
            return;
        }

        // Handle named actions (register, login, logout, me, checkUsername, etc.)
        switch ($action) {
            case "register":
                if ($method === "POST") {
                    $data = (array) json_decode(file_get_contents("php://input"), true);
                    $this->register($data);
                } else {
                    http_response_code(405);
                    header("Allow: POST");
                }
                break;

            case "login":
                if ($method === "POST") {
                    $data = (array) json_decode(file_get_contents("php://input"), true);
                    $this->login($data);           // better to pass $data
                } else {
                    http_response_code(405);
                    header("Allow: POST");
                }
                break;

            case "logout":
                if ($method === "POST") {
                    $this->logout();
                } else {
                    http_response_code(405);
                    header("Allow: POST");
                }
                break;

            case "me":
                if ($method === "GET") {
                    $this->getCurrentUser();
                } else {
                    http_response_code(405);
                    header("Allow: GET");
                }
                break;

            case "checkUser":
                if ($method === "POST") {
                    $data = (array) json_decode(file_get_contents("php://input"), true);
                    $this->checkUsername($data);
                } else {
                    http_response_code(405);
                    header("Allow: POST");
                }
                break;

            default:
                http_response_code(404);
                echo json_encode([
                    "status" => "error",
                    "message" => "Endpoint not found"
                ]);
                break;
        }
    }

    /**Register New User
     * @param array $data: php input
     * @return void
     */
    public function register(array $data):void{

        if (!isset($data["username"],$data["email"],$data["password"])){
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Missing required fields."
            ]);
            return;
        }

        $errors = [
            "username" => $this->userValidator->validateUsername($data["username"]),
            "email" => $this->userValidator->validateEmail($data["email"]),
            "password" => $this->userValidator->validatePassword($data["password"])
        ];



        if(
            !empty($errors["username"]) ||
            !empty($errors["email"]) ||
            !empty($errors["password"])){
            http_response_code(422);
            echo json_encode([
                "status" => "error",
                "message" => "Validation failed.",
                "errors" => [
                    "username" => $errors["username"],
                    "email" => $errors["email"],
                    "password" => $errors["password"]
                ]
            ]);
            return;
        }

        $username = $data["username"];
        $email = $data["email"];
        $password = $data["password"];
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $userId = $this->userGateway->createUser($username, $email, $password_hash);
            if ($userId) {
                http_response_code(201); // HTTP status - created
                echo json_encode([
                    "status" => "success",
                    "user_id" => $userId,
                    "username" => $username,
                ]);
                return;
            } else {
                http_response_code(422);
                echo json_encode([
                    "status" => "error",
                    "message" => "Failed to create user.",
                ]);
                return;
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Database error.",
                "error" => $e->getMessage()
            ]);
            return;
        }

    }

    /** Log in a User, set sessions
     * @param array $data: php input
     * @return void
     */
    public function login(array $data):void{

        if (empty($data["username"]) || empty($data["password"])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Missing required fields."
            ]);
            return;
        }
        $username = trim($data["username"]);
        $password = $data["password"];
        $user = $this->userGateway->authenticate($username, $password);

        if ($user) {
            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["username"] = $user["username"];
            echo json_encode([
                "status" => "success",
                "message" => "Logged in successfully.",
                "user" => [
                    "username" => $user["username"],
                    "user_id" => $user["user_id"],
                ]
            ]);
        } else {
            http_response_code(401); // unauthorised
            echo json_encode([
                "status" => "error",
                "message" => "Invalid username or password."
            ]);
        }

    }

    /**Log out and safely destroy session
     * @return void
     */
    public function logout():void{
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = array(); //Clear all session variables
            if (isset($_COOKIE[session_name()])) {
                setcookie(session_name(), '', time() - -2592000, '/');
            }
            session_destroy();
        }
        echo json_encode([
            "status" => "success",
            "message" => "Logged out successfully."
        ]);
    }

    /**Check Username availability
     * @param array $data: php input
     * @return void
     */
    public function checkUsername(array $data):void{

       if (!isset($data["username"]) || $data["username"] === ""){
           http_response_code(400);
           echo json_encode([
               "status" => "error",
               "message" => "Missing required fields."
           ]);
           return;
       }
       $username = trim($data["username"]);

       $user = $this->userGateway->getUserByUsername($username);
       if ($user) {
           http_response_code(409);
           echo json_encode([
               "status" => "error",
               "message" => "Username already taken."
           ]);
       } else {
           echo json_encode([
               "status" => "success",
               "message" => "Username available."
           ]);
       }
    }

    /**Get currently logged-in user.
     * @return void
     */
    public function getCurrentUser():void{
        //Check if user is logged in
        if (!isset($_SESSION["user_id"])) {
            http_response_code(401);
            echo json_encode([
                "status" => "error",
                "message" => "Not logged in."
            ]);
            return;
        }
        $user = $this->userGateway->getUserById($_SESSION["user_id"]);
        if ($user) {
            unset($user["password"]);
            echo json_encode([
                "status" => "success",
                "user" => [
                    "username" => $user["username"],
                    "user_id" => $user["user_id"],
                    "email" => $user["email"],
                ]
            ]);
        } else {
            http_response_code(401);
            echo json_encode([
                "status" => "error",
                "message" => "User not found."
            ]);
        }
    }

    /**Delete a user from db
     * @param string $id:user_id
     * @return void
     */
    public function delete(string $id):void{
        try {
            $result = $this->userGateway->deleteUserById($id);
            if ($result>0) {
                http_response_code(200);
                echo json_encode([
                    "status" => "success",
                    "message" => "User $id deleted.",
                    "userId" => $id,
                ]);
                return;
            } else {
                http_response_code(500);
                echo json_encode([
                    "status" => "error",
                    "message" => "Failed to delete user.",
                    "userId" => $id,
                ]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Database error.",
                "error" => $e->getMessage()
            ]);
        }
    }

    /**Helper method to facilitate DB CRUD and redirect based on HTTP method
     * Only Delete available for project needs, switch for easy extension.
     * @param string $method: HTTP method
     * @param int $userId: user_id
     * @return void
     */
    private function handleUserById(string $method, int $userId): void{
        switch ($method) {
            case "DELETE":
                $this->delete($userId);
                break;

            case "GET":
                // $this->getUser($userId);
                break;

            case "PATCH":
                // $this->updateUser($userId);
                break;

            default:
                http_response_code(405);
                header("Allow: GET, PATCH, DELETE");
                break;
        }
    }

}
