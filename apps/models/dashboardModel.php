<?php

/**
 * Modèle Tableau de bord — indicateurs financiers globaux (admin)
 */

class dashboardModel
{
    private PDO $db;

    public function __construct()
    {
        global $pdo;
        $this->db = $pdo;
    }

    /**
     * Argent entré (ventes payées), dépensé (achats) et retiré de la caisse
     * (retraits validés) — sert au graphique en anneau du tableau de bord
     */
    public function financeSummary(): array
    {
        $ventes = (float) $this->db->query(
            "SELECT COALESCE(SUM(total_amount), 0) FROM sales WHERE payment_status = 'paye'"
        )->fetchColumn();

        $achats = (float) $this->db->query(
            'SELECT COALESCE(SUM(total_amount), 0) FROM purchases'
        )->fetchColumn();

        $retraits = (float) $this->db->query(
            "SELECT COALESCE(SUM(amount), 0) FROM cash_withdrawals WHERE status = 'valide'"
        )->fetchColumn();

        return [
            'ventes' => $ventes,
            'achats' => $achats,
            'retraits' => $retraits,
        ];
    }

    public function countLowStock(): int
    {
        return (int) $this->db->query(
            'SELECT COUNT(*) FROM products WHERE is_published = 1 AND quantity <= low_stock_threshold'
        )->fetchColumn();
    }

    /**
     * Produits publiés dont le stock est au seuil d'alerte ou en dessous
     */
    public function lowStockProducts(): array
    {
        $stmt = $this->db->query(
            "SELECT p.name, p.quantity, p.low_stock_threshold, c.name AS category_name
             FROM products p
             JOIN categories c ON c.id = p.category_id
             WHERE p.is_published = 1 AND p.quantity <= p.low_stock_threshold
             ORDER BY p.quantity ASC"
        );

        return $stmt->fetchAll();
    }

    /**
     * Liste des ventes payées, les plus récentes en premier
     */
    public function paidSales(int $limit = 100): array
    {
        $stmt = $this->db->prepare(
            "SELECT s.sale_number, s.created_at, s.customer_name, s.total_amount, s.payment_method, u.name AS seller_name
             FROM sales s
             JOIN users u ON u.id = s.seller_id
             WHERE s.payment_status = 'paye'
             ORDER BY s.created_at DESC
             LIMIT :limit"
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function countPendingWithdrawals(): int
    {
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM cash_withdrawals WHERE status = 'en_attente'"
        )->fetchColumn();
    }

    /**
     * Total des ventes payées, par jour, sur les 7 derniers jours (jours
     * sans vente inclus à 0) — pour le graphique en barres du tableau de bord
     */
    public function salesLast7Days(): array
    {
        $stmt = $this->db->query(
            "SELECT DATE(created_at) AS d, SUM(total_amount) AS total
             FROM sales
             WHERE payment_status = 'paye' AND created_at >= (CURDATE() - INTERVAL 6 DAY)
             GROUP BY DATE(created_at)"
        );
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

    /**
     * Répartition des ventes payées par catégorie de produit (top 6)
     */
    public function salesByCategory(): array
    {
        $stmt = $this->db->query(
            "SELECT c.name AS category, SUM(si.subtotal) AS total
             FROM sale_items si
             JOIN products p ON p.id = si.product_id
             JOIN categories c ON c.id = p.category_id
             JOIN sales s ON s.id = si.sale_id
             WHERE s.payment_status = 'paye'
             GROUP BY c.id, c.name
             ORDER BY total DESC
             LIMIT 6"
        );

        return array_map(
            fn($row) => ['category' => $row['category'], 'total' => (float) $row['total']],
            $stmt->fetchAll()
        );
    }
}
