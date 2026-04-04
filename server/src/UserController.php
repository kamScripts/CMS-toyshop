<?php

class UserController {
    private UserGateway $userGateway;
    public function __construct(UserGateway $userGateway) {
        $this->userGateway = $userGateway;
    }
    public function handleRequest(string $method,string $id):void{
        if (is_numeric($id))// if id is numeric then CRUD admin operations
        {
            switch ($method){
                            case "GET":
                                break;
                            case "POST":
                                break;
                            case "PATCH":
                                break;
                            case "DELETE":
                                break;
                            default:
                                break;
            }
        }
        else
        {
            switch ($id){
                case "login":
                    break;
                case "register":
                    break;
                    case "logout":
                        break;
                case "me":
                    break;
                case "checkUsername":
                    break;
                default:
                    break;
            }
        }

    }
    public function register(array $data):int{return 0;}
    public function login(array $data):int{return 0;}
    public function logout():int{return 0;}
    public function delete(string $id):int{return 0;}

}
