<?php

/**
 * Modèle Transactions — historique unifié des actions effectuées sur la
 * plateforme (ventes, achats, mouvements de stock), avec l'auteur de
 * chaque action et un filtre par période
 */

class transactionModel
{
    private PDO $db;

    public function __construct()
    {
        global $pdo;
        $this->db = $pdo;
    }

    /**
     * @param string $start Date de début (Y-m-d H:i:s)
     * @param string $end Date de fin (Y-m-d H:i:s)
     * @param string|null $sellerId Si fourni, ne retourne que les ventes de ce vendeur
     *                              (utilisé pour l'espace vendeur, qui ne voit que ses propres transactions)
     */
    public function getTransactions(string $start, string $end, ?string $sellerId = null): array
    {
        $ventes = $this->db->prepare(
            "SELECT 'vente' AS type, s.created_at AS dt, s.total_amount AS montant,
                    u.name AS utilisateur,
                    CONCAT('Vente #', s.sale_number, IF(s.customer_name IS NOT NULL AND s.customer_name != '', CONCAT(' — ', s.customer_name), '')) AS detail
             FROM sales s
             JOIN users u ON u.id = s.seller_id
             WHERE s.payment_status = 'paye' AND s.created_at BETWEEN :start1 AND :end1"
            . ($sellerId !== null ? ' AND s.seller_id = :seller_id' : '')
        );
        $ventes->bindValue(':start1', $start);
        $ventes->bindValue(':end1', $end);
        if ($sellerId !== null) {
            $ventes->bindValue(':seller_id', $sellerId);
        }
        $ventes->execute();
        $transactions = $ventes->fetchAll();

        if ($sellerId === null) {
            $achats = $this->db->prepare(
                "SELECT 'achat' AS type, p.created_at AS dt, p.total_amount AS montant,
                        u.name AS utilisateur,
                        CONCAT('Achat', IF(p.supplier IS NOT NULL AND p.supplier != '', CONCAT(' — ', p.supplier), '')) AS detail
                 FROM purchases p
                 JOIN users u ON u.id = p.recorded_by
                 WHERE p.created_at BETWEEN :start2 AND :end2"
            );
            $achats->bindValue(':start2', $start);
            $achats->bindValue(':end2', $end);
            $achats->execute();
            $transactions = array_merge($transactions, $achats->fetchAll());

            $mouvements = $this->db->prepare(
                "SELECT 'stock' AS type, m.created_at AS dt, m.quantity_change AS montant,
                        u.name AS utilisateur,
                        CONCAT(pr.name, IF(m.reason IS NOT NULL AND m.reason != '', CONCAT(' — ', m.reason), '')) AS detail,
                        m.type AS sous_type
                 FROM stock_movements m
                 JOIN users u ON u.id = m.created_by
                 JOIN products pr ON pr.id = m.product_id
                 WHERE m.type != 'vente' AND m.created_at BETWEEN :start3 AND :end3"
            );
            $mouvements->bindValue(':start3', $start);
            $mouvements->bindValue(':end3', $end);
            $mouvements->execute();
            $transactions = array_merge($transactions, $mouvements->fetchAll());
        }

        usort($transactions, fn($a, $b) => strcmp($b['dt'], $a['dt']));

        return $transactions;
    }

    /**
     * Années disponibles (à partir des ventes), pour peupler le filtre "Année"
     */
    public function anneesDisponibles(): array
    {
        $stmt = $this->db->query(
            "SELECT DISTINCT YEAR(created_at) AS annee FROM sales ORDER BY annee DESC"
        );
        $annees = array_map(fn($row) => (int) $row['annee'], $stmt->fetchAll());

        if (empty($annees) || !in_array((int) date('Y'), $annees, true)) {
            $annees[] = (int) date('Y');
        }

        rsort($annees);
        return $annees;
    }
}
