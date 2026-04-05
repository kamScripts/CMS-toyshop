<?php

class UserController {
    private UserGateway $userGateway;
    private UserValidator $userValidator;
    public function __construct(UserGateway $userGateway) {
        $this->userGateway = $userGateway;
        $this->userValidator = new UserValidator();
    }
    public function handleRequest(string $method,string $action):void{
        if($method === "POST"){
            $data = (array) json_decode(file_get_contents("php://input"));
            switch ($action) {
                case "register":
                    $result = $this->register($data);
                    break;
                    case "login":
                        $this->login();
                        break;
                    case "logout":
                        $this->logout();
                        break;
                    case "checkUsername":
                        $this->checkUsername();
                        break;


            }
        }elseif ($method === "GET"){
            switch ($action) {
                case "me":
                    $this->getCurrentUser();
                    break;
                default:
                    http_response_code(405);
                    header("Allow: GET");

            }
        }elseif ($method === "DELETE"){
            if (is_numeric($action)) {
                $this->delete($action);
            }
        }
    }
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



        if(!empty($errors["username"]) || !empty($errors["email"]) || !empty($errors["password"])){
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
                    "userId" => $userId,
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
    public function login():int{return 0;}
    public function logout():int{return 0;}
    public function checkUsername():int{return 0;}
    public function getCurrentUser():array{return [];}
    public function delete(string $id):int{return 0;}
    private function destroySession():void{
        $_SESSION = array();
        if (session_id() !== "" || isset($_COOKIE[session_name()])){
            setcookie(session_name(), '', time() - 3600);
        }
        session_destroy();
    }

}
