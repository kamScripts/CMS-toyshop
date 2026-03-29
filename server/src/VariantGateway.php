<?php

class VariantGateway
{
    private PDO $pdo;
    public function __construct(Database $database) {
        $this->pdo = $database->connect();
    }
    public function getAll(): array {
        $stmt = $this->pdo->prepare("SELECT * FROM variant");
        $stmt->execute();
        $data = [];
        while ($row = $stmt->fetch() ){
            $data[] = $row;
        }
        return $data;
    }
    public function get(string $id): array | bool {
        $sql = "SELECT * FROM variant WHERE variant_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row;
    }
    public function create(array $data): string {
        $sql = "INSERT INTO variant (variant_name, model_id, variant, sku, price, stock,imagepath)
                VALUES (:name, :model_id, :variant, :sku, :price, :stock, :imagepath)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":variant_name", $data["variant_name"], PDO::PARAM_STR);
        $stmt->bindValue(":model_id", $data["model_id"], PDO::PARAM_INT);
        $stmt->bindValue(":variant", $data["variant"], PDO::PARAM_STR);
        $stmt->bindValue(":sku", $data["sku"], PDO::PARAM_STR);
        $stmt->bindValue(":price", $data["price"], PDO::PARAM_STR);
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }
    public function update(array $current, array $new): int {
        $sql = "UPDATE variant 
                SET variant_name = :variant_name, model_id = :model_id, variant = :variant,
                sku = :sku, price = :price, stock = :stock, imagepath = :imagepath
                WHERE variant_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(
            ":variant_name", $new["variant_name"] ?? $current["variant_name"],
            PDO::PARAM_STR
        );
        $stmt->bindValue(":id", $current["variant_id"], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }
    public function delete(string $id): int {
        $sql = "DELETE FROM variant WHERE variant_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }
}