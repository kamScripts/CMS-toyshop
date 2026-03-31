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
    public function get(string $id): array | bool {
        $sql = "SELECT * FROM brand WHERE brand_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row;
    }
    public function create(array $data): string {
        $sql = "INSERT INTO brand (brand_name) VALUES (:brand_name)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":brand_name", $data["brand_name"], PDO::PARAM_STR);
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }
    public function update(int $currentId, array $new): int {
        $current = $this->get($currentId);
        $sql = "UPDATE brand SET brand_name = :brand_name WHERE brand_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(
            ":brand_name", $new["brand_name"] ?? $current["brand_name"],
            PDO::PARAM_STR
        );
        $stmt->bindValue(":id", $current["brand_id"], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }
    public function delete(string $id): int {
        $sql = "DELETE FROM brand WHERE brand_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }
    public function describeTable(): array {
        $sql = "SHOW COLUMNS IN brand";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
