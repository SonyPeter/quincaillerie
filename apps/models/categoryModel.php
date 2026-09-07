<?php

/**
 * Modèle Catégorie de produits
 */

class categoryModel
{
    private PDO $db;

    public function __construct()
    {
        global $pdo;
        $this->db = $pdo;
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM categories ORDER BY name ASC')->fetchAll();
    }

    public function find(string $id): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function findByName(string $name): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE name = :name LIMIT 1');
        $stmt->execute(['name' => $name]);
        return $stmt->fetch();
    }

    public function create(string $name, ?string $description): string
    {
        $id = Uuid::v4();
        $stmt = $this->db->prepare(
            'INSERT INTO categories (id, name, description) VALUES (:id, :name, :description)'
        );
        $stmt->execute(['id' => $id, 'name' => $name, 'description' => $description ?: null]);
        return $id;
    }

    public function update(string $id, string $name, ?string $description): void
    {
        $stmt = $this->db->prepare(
            'UPDATE categories SET name = :name, description = :description WHERE id = :id'
        );
        $stmt->execute(['name' => $name, 'description' => $description ?: null, 'id' => $id]);
    }

    /**
     * Catégories avec le nombre de produits publiés — pour la page d'accueil publique
     */
    public function allWithProductCount(): array
    {
        return $this->db->query(
            "SELECT c.*, COUNT(p.id) AS product_count
             FROM categories c
             LEFT JOIN products p ON p.category_id = c.id AND p.is_published = 1
             GROUP BY c.id
             ORDER BY c.name ASC"
        )->fetchAll();
    }

    public function countProducts(string $id): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM products WHERE category_id = :id');
        $stmt->execute(['id' => $id]);
        return (int) $stmt->fetchColumn();
    }

    public function delete(string $id): void
    {
        $this->db->prepare('DELETE FROM categories WHERE id = :id')->execute(['id' => $id]);
    }
}
