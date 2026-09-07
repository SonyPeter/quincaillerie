<?php

/**
 * Modèle Achat — approvisionnement du stock auprès de fournisseurs
 */

class purchaseModel
{
    private PDO $db;

    public function __construct()
    {
        global $pdo;
        $this->db = $pdo;
    }

    public function all(): array
    {
        return $this->db->query(
            'SELECT p.*, u.name AS recorded_by_name
             FROM purchases p
             JOIN users u ON u.id = p.recorded_by
             ORDER BY p.created_at DESC'
        )->fetchAll();
    }

    public function itemsByPurchase(string $purchaseId): array
    {
        $stmt = $this->db->prepare(
            'SELECT pi.*, pr.name AS product_name
             FROM purchase_items pi
             JOIN products pr ON pr.id = pi.product_id
             WHERE pi.purchase_id = :purchase_id'
        );
        $stmt->execute(['purchase_id' => $purchaseId]);
        return $stmt->fetchAll();
    }

    public function create(?string $supplier, string $recordedBy, float $totalAmount, ?string $note): string
    {
        $id = Uuid::v4();
        $stmt = $this->db->prepare(
            'INSERT INTO purchases (id, supplier, recorded_by, total_amount, note)
             VALUES (:id, :supplier, :recorded_by, :total_amount, :note)'
        );
        $stmt->execute([
            'id' => $id,
            'supplier' => $supplier ?: null,
            'recorded_by' => $recordedBy,
            'total_amount' => $totalAmount,
            'note' => $note ?: null,
        ]);
        return $id;
    }

    public function addItem(string $purchaseId, string $productId, int $quantity, float $unitCost): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO purchase_items (id, purchase_id, product_id, quantity, unit_cost, subtotal)
             VALUES (:id, :purchase_id, :product_id, :quantity, :unit_cost, :subtotal)'
        );
        $stmt->execute([
            'id' => Uuid::v4(),
            'purchase_id' => $purchaseId,
            'product_id' => $productId,
            'quantity' => $quantity,
            'unit_cost' => $unitCost,
            'subtotal' => $quantity * $unitCost,
        ]);
    }
}
