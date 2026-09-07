<?php

/**
 * Modèle Mouvement de stock — historique des actions qui changent une quantité
 * (réapprovisionnement, produit défectueux, ajustement, vente)
 */

class stockMovementModel
{
    private PDO $db;

    public function __construct()
    {
        global $pdo;
        $this->db = $pdo;
    }

    public function allByProduct(string $productId): array
    {
        $stmt = $this->db->prepare(
            'SELECT m.*, u.name AS user_name
             FROM stock_movements m
             JOIN users u ON u.id = m.created_by
             WHERE m.product_id = :product_id
             ORDER BY m.created_at DESC'
        );
        $stmt->execute(['product_id' => $productId]);
        return $stmt->fetchAll();
    }

    public function create(
        string $productId,
        string $type,
        int $quantityChange,
        int $quantityAfter,
        ?string $reason,
        string $createdBy
    ): string {
        $id = Uuid::v4();
        $stmt = $this->db->prepare(
            'INSERT INTO stock_movements (id, product_id, type, quantity_change, quantity_after, reason, created_by)
             VALUES (:id, :product_id, :type, :quantity_change, :quantity_after, :reason, :created_by)'
        );
        $stmt->execute([
            'id' => $id,
            'product_id' => $productId,
            'type' => $type,
            'quantity_change' => $quantityChange,
            'quantity_after' => $quantityAfter,
            'reason' => $reason ?: null,
            'created_by' => $createdBy,
        ]);
        return $id;
    }
}
