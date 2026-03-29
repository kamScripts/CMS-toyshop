<?php

class ScaleGateway
{
    private PDO $pdo;
    public function __construct(Database $database) {
        $this->pdo = $database->connect();
    }
    public function getAll(): array {
        $stmt = $this->pdo->prepare("SELECT * FROM scale");
        $stmt->execute();
        $data = [];
        while ($row = $stmt->fetch() ){
            $data[] = $row;
        }
        return $data;
    }
    public function get(string $id): array | bool {
        $sql = "SELECT * FROM scale WHERE scale_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row;
    }
    public function create(array $data): string {
        $sql = "INSERT INTO scale (scale_name) VALUES (:scale_name)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":scale_name", $data["scale_name"], PDO::PARAM_STR);
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }
    public function update(array $current, array $new): int {
        $sql = "UPDATE scale SET scale_name = :scale_name WHERE scale_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(
            ":scale_name", $new["scale_name"] ?? $current["scale_name"],
            PDO::PARAM_STR
        );
        $stmt->bindValue(":id", $current["scale_id"], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }
    public function delete(string $id): int {
        $sql = "DELETE FROM scale WHERE scale_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }
}