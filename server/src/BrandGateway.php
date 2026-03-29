<?php

class BrandGateway
{
    private PDO $pdo;
    public function __construct(Database $database) {
        $this->pdo = $database->connect();
    }
    public function getAll(): array {
        $stmt = $this->pdo->prepare("SELECT * FROM brand");
        $stmt->execute();
        $data = [];
        while ($row = $stmt->fetch() ){
            $data[] = $row;
        }
        return $data;
    }
}