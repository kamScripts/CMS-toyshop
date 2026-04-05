<?php

class UserValidator
{
    public function validateUsername(string $username): array {
        $errors = [];
        if(!preg_match("/^[a-zA-Z0-9_-]+$/", $username)){
            $errors[]="Username must contain only alphanumeric characters or (_-).";
        }
        if(mb_strlen($username) > 16){
            $errors[]="Username must contain less than 16 characters.";
        }
        if(mb_strlen($username) < 3){
            $errors[]="Username must contain at least 3 characters.";
        }
        return $errors;
    }
    public function validateEmail(string $email): array {
        $errors = [];
        $address = explode("@", $email)[1];
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $errors[]="Invalid email.";
        }
        if(mb_strlen($address[0]) > 64){
            $errors[]="email must contain less than 64 characters.";
        }
        return $errors;
    }
    public function validatePassword(string $password) : array {
        $errors = [];
        if(mb_strlen($password) > 32){
            $errors[]="Password must contain less than 32 characters.";
        }
        if(mb_strlen($password) < 9){
            $errors[]="Password must contain at least 9 characters.";
        }
        if(!preg_match('/[a-zA-Z]/', $password)){
            $errors[]="Password must contain at least one letter.";
        }
        if(!preg_match('/[0-9]/', $password)){
            $errors[]="Password must contain at least one number.";
        }
        if(!preg_match('/[!@#$%^&*]/', $password)){
            $errors[]="Password must contain at least one special character.";
        }
        return $errors;
    }
}