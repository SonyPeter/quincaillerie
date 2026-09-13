<?php

/**
 * Modèle Utilisateur (admin / vendeur)
 * Table `users` : id (uuid), name, email, phone, username, password,
 * must_change_password, role, is_active, ...
 */

class userModel
{
    private PDO $db;

    public function __construct()
    {
        global $pdo;
        $this->db = $pdo;
    }

    public function findByUsername(string $username): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM users WHERE username = :username AND is_active = 1 LIMIT 1'
        );
        $stmt->execute(['username' => $username]);
        return $stmt->fetch();
    }

    public function findById(string $id): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM users ORDER BY created_at DESC')->fetchAll();
    }

    /**
     * Tous les autres utilisateurs actifs (pour choisir un destinataire de message)
     */
    public function allExcept(string $id): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM users WHERE id != :id AND is_active = 1 ORDER BY name ASC'
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll();
    }

    /**
     * Liste des vendeurs (pour l'espace "Comptes" de l'admin)
     */
    public function allVendeurs(): array
    {
        return $this->db->query(
            "SELECT * FROM users WHERE role = 'vendeur' ORDER BY name ASC"
        )->fetchAll();
    }

    public function create(array $data, string $createdBy): string
    {
        $id = Uuid::v4();
        $stmt = $this->db->prepare(
            'INSERT INTO users (id, name, email, phone, username, password, role, created_by)
             VALUES (:id, :name, :email, :phone, :username, :password, :role, :created_by)'
        );
        $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?: null,
            'username' => $data['username'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => $data['role'],
            'created_by' => $createdBy,
        ]);

        return $id;
    }

    /**
     * Créé par l'admin, avec un mot de passe temporaire à changer
     * obligatoirement à la première connexion
     */
    public function createVendeur(array $data, string $createdBy): string
    {
        $id = Uuid::v4();
        $stmt = $this->db->prepare(
            "INSERT INTO users (id, name, email, phone, username, password, role, must_change_password, created_by)
             VALUES (:id, :name, :email, :phone, :username, :password, 'vendeur', 1, :created_by)"
        );
        $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?: null,
            'username' => $data['username'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'created_by' => $createdBy,
        ]);

        return $id;
    }

    public function updateProfile(string $id, string $name, string $email, ?string $phone): void
    {
        $stmt = $this->db->prepare(
            'UPDATE users SET name = :name, email = :email, phone = :phone WHERE id = :id'
        );
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'phone' => $phone ?: null,
            'id' => $id,
        ]);
    }

    public function updatePassword(string $id, string $newPassword): void
    {
        $stmt = $this->db->prepare(
            'UPDATE users SET password = :password, must_change_password = 0 WHERE id = :id'
        );
        $stmt->execute([
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
            'id' => $id,
        ]);
    }

    public function updateRole(string $id, string $role): void
    {
        $stmt = $this->db->prepare('UPDATE users SET role = :role WHERE id = :id');
        $stmt->execute([
            'role' => $role,
            'id' => $id,
        ]);
    }
}
