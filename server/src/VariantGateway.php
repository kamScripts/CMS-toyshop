<?php

class VariantGateway
{
    private PDO $pdo; // PDO
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
        $sql = "INSERT INTO variant ( model_id, variant, sku, price, stock,imagepath)
                VALUES (:model_id, :variant, :sku, :price, :stock, :imagepath)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":model_id",  $data['model_id']  ?? null, PDO::PARAM_INT);
        $stmt->bindValue(":variant",   $data['variant']   ?? null, PDO::PARAM_STR);
        $stmt->bindValue(":sku",       $data['sku']       ?? null, PDO::PARAM_STR);     // or generate SKU
        $stmt->bindValue(":price",     $data['price']     ?? 0,    PDO::PARAM_STR);
        $stmt->bindValue(":stock",     $data['stock']     ?? 0,    PDO::PARAM_INT);
        $stmt->bindValue(":imagepath", $data['imagepath'] ?? 'placeholder.png', PDO::PARAM_STR);
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }
    public function update(int $currentId, array $new): int {
        $current = $this->get($currentId);
        $sql = "UPDATE variant
                SET variant = :variant, model_id = :model_id, sku = :sku,
                    price = :price, stock = :stock, imagepath = :imagepath
                WHERE variant_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue("variant", $new['variant']  ?? $current['variant'], PDO::PARAM_STR);
        $stmt->bindValue("model_id", $new['model_id']  ?? $current['model_id'], PDO::PARAM_INT);
        $stmt->bindValue("sku", $new['sku'] ?? $current['sku'], PDO::PARAM_STR);
        $stmt->bindValue(":id", $current["variant_id"], PDO::PARAM_INT);
        $stmt->bindValue(":price", $new['price']    ?? $current['price'], PDO::PARAM_STR);
        $stmt->bindValue(":stock", $new['stock']    ?? $current['stock'], PDO::PARAM_INT);
        $stmt->bindValue(":imagepath", $new['imagepath'] ?? $current['imagepath'], PDO::PARAM_STR);
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
    public function describeTable(): array {
        $sql = "SHOW COLUMNS IN variant";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}