<?php

/**
 * Modèle Vente
 *
 * Cycle de vie d'une vente :
 * 1. create()  — fiche remplie, statut "en_attente" (proforma imprimable, stock inchangé)
 * 2. validate() — argent reçu : statut "paye" (compte dans le total des ventes du tableau
 *    de bord) et stock décrémenté via productModel::adjustStock()
 * 3. cancel()  — proforma abandonnée, supprimée (uniquement si encore "en_attente")
 */

class saleModel
{
    private PDO $db;

    public function __construct()
    {
        global $pdo;
        $this->db = $pdo;
    }

    /**
     * @param string|null $sellerId Si fourni, ne retourne que les ventes de ce vendeur
     * (un vendeur ne doit jamais voir les ventes des autres)
     */
    public function all(?string $sellerId = null): array
    {
        $sql = 'SELECT s.*, u.name AS seller_name
                FROM sales s
                JOIN users u ON u.id = s.seller_id';
        $params = [];

        if ($sellerId !== null) {
            $sql .= ' WHERE s.seller_id = :seller_id';
            $params['seller_id'] = $sellerId;
        }

        $sql .= ' ORDER BY s.created_at DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find(string $id): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT s.*, u.name AS seller_name
             FROM sales s
             JOIN users u ON u.id = s.seller_id
             WHERE s.id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function itemsBySale(string $saleId): array
    {
        $stmt = $this->db->prepare(
            'SELECT si.*, p.name AS product_name
             FROM sale_items si
             JOIN products p ON p.id = si.product_id
             WHERE si.sale_id = :sale_id'
        );
        $stmt->execute(['sale_id' => $saleId]);
        return $stmt->fetchAll();
    }

    public function create(string $sellerId, string $paymentMethod, ?string $customerName, float $totalAmount): string
    {
        $id = Uuid::v4();
        $stmt = $this->db->prepare(
            'INSERT INTO sales (id, seller_id, total_amount, payment_status, payment_method, customer_name)
             VALUES (:id, :seller_id, :total_amount, \'en_attente\', :payment_method, :customer_name)'
        );
        $stmt->execute([
            'id' => $id,
            'seller_id' => $sellerId,
            'total_amount' => $totalAmount,
            'payment_method' => $paymentMethod,
            'customer_name' => $customerName ?: null,
        ]);
        return $id;
    }

    public function addItem(string $saleId, string $productId, int $quantity, float $unitPrice): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO sale_items (id, sale_id, product_id, quantity, unit_price, subtotal)
             VALUES (:id, :sale_id, :product_id, :quantity, :unit_price, :subtotal)'
        );
        $stmt->execute([
            'id' => Uuid::v4(),
            'sale_id' => $saleId,
            'product_id' => $productId,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'subtotal' => $quantity * $unitPrice,
        ]);
    }

    public function markAsPaid(string $id): void
    {
        $this->db->prepare("UPDATE sales SET payment_status = 'paye' WHERE id = :id")
            ->execute(['id' => $id]);
    }

    public function cancel(string $id): void
    {
        $this->db->prepare("DELETE FROM sales WHERE id = :id AND payment_status = 'en_attente'")
            ->execute(['id' => $id]);
    }

    public function totalPaidBySeller(string $sellerId): float
    {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(total_amount), 0) FROM sales WHERE seller_id = :seller_id AND payment_status = 'paye'"
        );
        $stmt->execute(['seller_id' => $sellerId]);
        return (float) $stmt->fetchColumn();
    }

    public function totalPaidTodayBySeller(string $sellerId): float
    {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(total_amount), 0) FROM sales
             WHERE seller_id = :seller_id AND payment_status = 'paye' AND DATE(created_at) = CURDATE()"
        );
        $stmt->execute(['seller_id' => $sellerId]);
        return (float) $stmt->fetchColumn();
    }

    public function countPendingBySeller(string $sellerId): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM sales WHERE seller_id = :seller_id AND payment_status = 'en_attente'"
        );
        $stmt->execute(['seller_id' => $sellerId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Total des ventes payées, par jour, sur les 7 derniers jours, pour un
     * vendeur donné (jours sans vente inclus à 0)
     */
    public function salesLast7DaysBySeller(string $sellerId): array
    {
        $stmt = $this->db->prepare(
            "SELECT DATE(created_at) AS d, SUM(total_amount) AS total
             FROM sales
             WHERE seller_id = :seller_id AND payment_status = 'paye' AND created_at >= (CURDATE() - INTERVAL 6 DAY)
             GROUP BY DATE(created_at)"
        );
        $stmt->execute(['seller_id' => $sellerId]);

        $parJour = [];
        foreach ($stmt->fetchAll() as $row) {
            $parJour[$row['d']] = (float) $row['total'];
        }

        $joursFr = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];

        $jours = [];
        for ($i = 6; $i >= 0; $i--) {
            $timestamp = strtotime("-$i day");
            $date = date('Y-m-d', $timestamp);
            $jours[] = [
                'date' => $date,
                'label' => $joursFr[(int) date('w', $timestamp)],
                'total' => $parJour[$date] ?? 0.0,
            ];
        }

        return $jours;
    }
}
