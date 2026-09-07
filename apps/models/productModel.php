<?php

/**
 * Modèle Produit
 */

class productModel
{
    private PDO $db;

    public function __construct()
    {
        global $pdo;
        $this->db = $pdo;
    }

    /**
     * Produits publiés (visibles dans le catalogue, vendables)
     */
    public function all(): array
    {
        return $this->db->query(
            'SELECT p.*, c.name AS category_name
             FROM products p
             JOIN categories c ON c.id = p.category_id
             WHERE p.is_published = 1
             ORDER BY p.name ASC'
        )->fetchAll();
    }

    /**
     * Produits créés depuis un achat mais pas encore ajoutés au catalogue
     * (pas de prix de vente défini)
     */
    public function allPending(): array
    {
        return $this->db->query(
            'SELECT p.*, c.name AS category_name
             FROM products p
             JOIN categories c ON c.id = p.category_id
             WHERE p.is_published = 0
             ORDER BY p.created_at DESC'
        )->fetchAll();
    }

    public function find(string $id): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM products WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create(array $data): string
    {
        $id = Uuid::v4();
        $stmt = $this->db->prepare(
            'INSERT INTO products (id, category_id, name, description, quantity, unit_price, low_stock_threshold, is_published)
             VALUES (:id, :category_id, :name, :description, :quantity, :unit_price, :low_stock_threshold, :is_published)'
        );
        $stmt->execute([
            'id' => $id,
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?: null,
            'quantity' => $data['quantity'],
            'unit_price' => $data['unit_price'],
            'low_stock_threshold' => $data['low_stock_threshold'],
            'is_published' => $data['is_published'] ?? 1,
        ]);
        return $id;
    }

    /**
     * Ajoute un produit créé depuis un achat (catégorie, nom, description,
     * photo) sans prix de vente : il reste hors catalogue tant que l'admin
     * ne l'a pas publié via publish()
     */
    public function createDraft(array $data): string
    {
        return $this->create([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'quantity' => 0,
            'unit_price' => 0,
            'low_stock_threshold' => 10,
            'is_published' => 0,
        ]);
    }

    /**
     * Ajoute un produit en attente au catalogue : fixe sa description
     * finale et son prix de vente, et le rend visible/vendable
     */
    public function publish(string $id, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE products
             SET description = :description, unit_price = :unit_price,
                 low_stock_threshold = :low_stock_threshold, is_published = 1
             WHERE id = :id'
        );
        $stmt->execute([
            'description' => $data['description'] ?: null,
            'unit_price' => $data['unit_price'],
            'low_stock_threshold' => $data['low_stock_threshold'],
            'id' => $id,
        ]);
    }

    /**
     * Modifie les informations du produit (jamais la quantité : elle ne
     * change que via adjustStock(), pour garder un historique complet)
     */
    public function update(string $id, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE products
             SET category_id = :category_id, name = :name, description = :description,
                 unit_price = :unit_price, low_stock_threshold = :low_stock_threshold
             WHERE id = :id'
        );
        $stmt->execute([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?: null,
            'unit_price' => $data['unit_price'],
            'low_stock_threshold' => $data['low_stock_threshold'],
            'id' => $id,
        ]);
    }

    public function updateImage(string $id, string $filename): void
    {
        $this->db->prepare('UPDATE products SET image = :image WHERE id = :id')
            ->execute(['image' => $filename, 'id' => $id]);
    }

    /**
     * Enregistre le prix payé au dernier achat (cost_price), indépendant du
     * prix de vente (unit_price) : il peut être différent à chaque achat.
     */
    public function updateCostPrice(string $id, float $costPrice): void
    {
        $this->db->prepare('UPDATE products SET cost_price = :cost_price WHERE id = :id')
            ->execute(['cost_price' => $costPrice, 'id' => $id]);
    }

    /**
     * Change la quantité en stock d'un produit et enregistre l'action dans
     * l'historique des mouvements (réapprovisionnement, produit défectueux,
     * ajustement, vente). $change est positif (ajout) ou négatif (retrait).
     * Lève une exception si le retrait ferait passer le stock sous zéro.
     */
    public function adjustStock(string $id, int $change, string $type, ?string $reason, string $userId): int
    {
        $product = $this->find($id);
        if (!$product) {
            throw new InvalidArgumentException('Produit introuvable.');
        }

        $newQuantity = (int) $product['quantity'] + $change;
        if ($newQuantity < 0) {
            throw new RuntimeException('Quantité insuffisante en stock.');
        }

        $this->db->beginTransaction();
        try {
            $this->db->prepare('UPDATE products SET quantity = :quantity WHERE id = :id')
                ->execute(['quantity' => $newQuantity, 'id' => $id]);

            (new stockMovementModel())->create($id, $type, $change, $newQuantity, $reason, $userId);

            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }

        return $newQuantity;
    }

    public function delete(string $id): void
    {
        $this->db->prepare('DELETE FROM products WHERE id = :id')->execute(['id' => $id]);
    }
}
