<?php

class ModelGateway
{
    private PDO $pdo;
    public function __construct(Database $database) {
        $this->pdo = $database->connect();
    }

    public function getAll(): array {
        $stmt = $this->pdo->prepare("SELECT * FROM model");
        $stmt->execute();
        $data = [];
        while ($row = $stmt->fetch() ){
            $data[] = $row;
        }
        return $data;
    }
    public function get(string $id): array | bool {
        $sql = "SELECT * FROM model WHERE model_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row;
    }
    public function create(array $data): string {
        $sql = "INSERT INTO model (model_name, collection_id, brand_id, scale_id, description)
                VALUES (:model_name, :collection_id, :brand_id, :scale_id, :description)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":model_name", $data["model_name"], PDO::PARAM_STR);
        $stmt->bindValue(":collection_id", $data["collection_id"] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(":brand_id", $data["brand_id"] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(":scale_id", $data["scale_id"] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(":description", $data["description"] ?? null, PDO::PARAM_STR);
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }
    public function update(array $current, array $new): int {
        $sql = "UPDATE model
                SET model_name = :model_name, collection_id = :collection_id,
                brand_id = :brand_id, scale_id = :scale_id, description = :description
                WHERE model_id = :model_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(
            ":model_name", $new["model_name"] ?? $current["model_name"],
            PDO::PARAM_STR
        );
        $stmt->bindValue(":collection_id", $new["collection_id"] ?? $current["collection_id"], PDO::PARAM_INT);
        $stmt->bindValue(":brand_id", $new["brand_id"] ?? $current["brand_id"], PDO::PARAM_INT);
        $stmt->bindValue(":scale_id", $new["scale_id"] ?? $current["scale_id"], PDO::PARAM_INT);
        $stmt->bindValue(":description", $new["description"] ?? $current["description"], PDO::PARAM_STR);
        $stmt->bindValue(":id", $current["model_id"], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }
    public function delete(string $id): int {
        $sql = "DELETE FROM model WHERE model_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }
}