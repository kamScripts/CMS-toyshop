<?php

class VariantGateway
{
    private PDO $pdo;
    private string $joinQuery = "
        SELECT 
            m.model_id,
            m.model_name,
            v.variant,
            m.description,
            v.variant_id,

            v.sku,
            v.price,
            v.stock,
            v.imagepath,
            b.brand_id,
            b.brand_name,
            s.scale_id,
            s.scale_name,
            c.collection_id,
            c.category_name AS collection_name
            

        FROM variant v
        LEFT JOIN model m ON v.model_id = m.model_id
        LEFT JOIN brand b ON m.brand_id = b.brand_id
        LEFT JOIN scale s ON m.scale_id = s.scale_id
        LEFT JOIN collection c ON m.collection_id = c.collection_id";
    public function __construct(Database $database) {
        $this->pdo = $database->connect();
    }
    public function getSingleJoin(string $id): array | bool{
        $stmt = $this->pdo->prepare($this->joinQuery . " WHERE variant_id = :id");
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
    public function getAllProducts(): array | bool
    {
        // Start from variant (the "many" side) and LEFT JOIN everything else.
        // This ensures every variant appears as its own row, even if some models have no variants.


        $stmt = $this->pdo->prepare($this->joinQuery);
        $stmt->execute();
        return $stmt->fetchAll();
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
        return $stmt->fetch();
    }
    public function create(array $data): string {
        $sql = "INSERT INTO variant (variant_name, model_id, variant, sku, price, stock,imagepath)
                VALUES (:name, :model_id, :variant, :sku, :price, :stock, :imagepath)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":variant_name", $data["variant_name"]);
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
            ":variant_name", $new["variant_name"] ?? $current["variant_name"]
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