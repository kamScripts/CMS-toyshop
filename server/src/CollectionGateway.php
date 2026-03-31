<?php

class CollectionGateway
{
    private PDO $pdo;
    public function __construct(Database $database) {
        $this->pdo = $database->connect();
    }
    public function getAll(): array {
        $stmt = $this->pdo->prepare("SELECT * FROM collection");
        $stmt->execute();
        $data = [];
        while ($row = $stmt->fetch() ){
            $data[] = $row;
        }
        return $data;
    }
    public function get(string $id): array | bool {
        $sql = "SELECT * FROM collection WHERE collection_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row;
    }
    public function create(array $data): string {
        $sql = "INSERT INTO collection (category_name) VALUES (:category_name)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":category_name", $data["category_name"], PDO::PARAM_STR);
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }
    public function update(int $currentId, array $new): int {
        $current = $this->get($currentId);
        $sql = "UPDATE collection SET category_name = :category_name WHERE collection_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(
            ":category_name", $new["category_name"] ?? $current["category_name"],
            PDO::PARAM_STR
        );
        $stmt->bindValue(":id", $current["collection_id"], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }
    public function delete(string $id): int {
        $sql = "DELETE FROM collection WHERE collection_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }
    public function describeTable(): array {
        $sql = "SHOW COLUMNS IN collection";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
