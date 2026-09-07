<?php

/**
 * Modèle Caisse & retraits — soldes par moyen de paiement (cash, natcash,
 * moncash), conversion NatCash/MonCash vers Cash, et demandes de retrait
 */

class caisseModel
{
    private PDO $db;

    public function __construct()
    {
        global $pdo;
        $this->db = $pdo;
    }

    /**
     * Solde de chaque caisse : ventes payées par ce moyen, plus les
     * conversions reçues (cash uniquement), moins les conversions envoyées
     * (natcash/moncash) et les retraits validés (cash uniquement)
     */
    public function soldes(): array
    {
        $ventes = $this->db->query(
            "SELECT payment_method, COALESCE(SUM(total_amount), 0) AS total
             FROM sales
             WHERE payment_status = 'paye'
             GROUP BY payment_method"
        )->fetchAll(PDO::FETCH_KEY_PAIR);

        $conversionsEnvoyees = $this->db->query(
            'SELECT from_method, COALESCE(SUM(amount), 0) AS total FROM cash_conversions GROUP BY from_method'
        )->fetchAll(PDO::FETCH_KEY_PAIR);

        $conversionsRecues = (float) $this->db->query(
            'SELECT COALESCE(SUM(amount), 0) FROM cash_conversions'
        )->fetchColumn();

        $retraitsCash = (float) $this->db->query(
            "SELECT COALESCE(SUM(amount), 0) FROM cash_withdrawals WHERE status = 'valide'"
        )->fetchColumn();

        $cash = (float) ($ventes['cash'] ?? 0) + $conversionsRecues - $retraitsCash;
        $natcash = (float) ($ventes['natcash'] ?? 0) - (float) ($conversionsEnvoyees['natcash'] ?? 0);
        $moncash = (float) ($ventes['moncash'] ?? 0) - (float) ($conversionsEnvoyees['moncash'] ?? 0);

        return ['cash' => $cash, 'natcash' => $natcash, 'moncash' => $moncash];
    }

    /**
     * Convertit un montant NatCash ou MonCash en Cash
     */
    public function convertir(string $fromMethod, float $amount, string $userId): void
    {
        if (!in_array($fromMethod, ['natcash', 'moncash'], true)) {
            throw new InvalidArgumentException('Moyen de paiement invalide pour une conversion.');
        }
        if ($amount <= 0) {
            throw new InvalidArgumentException('Le montant à convertir doit être positif.');
        }

        $soldes = $this->soldes();
        if ($amount > $soldes[$fromMethod]) {
            throw new InvalidArgumentException('Le montant dépasse le solde disponible.');
        }

        $stmt = $this->db->prepare(
            'INSERT INTO cash_conversions (id, from_method, amount, converted_by)
             VALUES (:id, :from_method, :amount, :converted_by)'
        );
        $stmt->execute([
            'id' => Uuid::v4(),
            'from_method' => $fromMethod,
            'amount' => $amount,
            'converted_by' => $userId,
        ]);
    }

    /**
     * Demandes de retrait, les plus récentes en premier
     */
    public function demandes(?string $status = null, ?string $requestedBy = null): array
    {
        $sql = "SELECT w.*, ru.name AS requested_by_name, au.name AS approved_by_name
                FROM cash_withdrawals w
                LEFT JOIN users ru ON ru.id = w.requested_by
                LEFT JOIN users au ON au.id = w.approved_by
                WHERE 1 = 1";
        $params = [];

        if ($status !== null) {
            $sql .= ' AND w.status = :status';
            $params['status'] = $status;
        }
        if ($requestedBy !== null) {
            $sql .= ' AND w.requested_by = :requested_by';
            $params['requested_by'] = $requestedBy;
        }

        $sql .= ' ORDER BY w.created_at DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function countEnAttente(): int
    {
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM cash_withdrawals WHERE status = 'en_attente'"
        )->fetchColumn();
    }

    public function creerDemande(string $type, float $amount, ?string $description, string $requestedBy): void
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Le montant du retrait doit être positif.');
        }

        $stmt = $this->db->prepare(
            "INSERT INTO cash_withdrawals (id, type, amount, description, requested_by, status)
             VALUES (:id, :type, :amount, :description, :requested_by, 'en_attente')"
        );
        $stmt->execute([
            'id' => Uuid::v4(),
            'type' => $type,
            'amount' => $amount,
            'description' => $description ?: null,
            'requested_by' => $requestedBy,
        ]);
    }

    public function resoudre(string $id, string $statut, string $approvedBy): void
    {
        if (!in_array($statut, ['valide', 'refuse'], true)) {
            throw new InvalidArgumentException('Statut invalide.');
        }

        $stmt = $this->db->prepare(
            'UPDATE cash_withdrawals
             SET status = :status, approved_by = :approved_by, resolved_at = NOW()
             WHERE id = :id AND status = \'en_attente\''
        );
        $stmt->execute([
            'status' => $statut,
            'approved_by' => $approvedBy,
            'id' => $id,
        ]);
    }
}
