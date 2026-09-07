-- Schéma de la base de données : Gestion de quincaillerie
-- Base : db_quincaillerie
--
-- Toutes les clés primaires sont des UUID (CHAR(36)), générées côté PHP
-- (UUID v4) avant chaque insertion, pour rester compatibles avec toutes
-- les versions de MySQL/MariaDB.

CREATE DATABASE IF NOT EXISTS db_quincaillerie CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_quincaillerie;

-- ============================================================
-- Utilisateurs (admins et vendeurs)
-- ============================================================
CREATE TABLE users (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(30) NULL,
    username VARCHAR(60) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    must_change_password TINYINT(1) NOT NULL DEFAULT 0,
    role ENUM('admin', 'vendeur') NOT NULL DEFAULT 'vendeur',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_by CHAR(36) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- Catégories de produits
-- ============================================================
CREATE TABLE categories (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- Produits
-- ============================================================
CREATE TABLE products (
    id CHAR(36) PRIMARY KEY,
    category_id CHAR(36) NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT NULL,
    image VARCHAR(255) NULL,
    quantity INT NOT NULL DEFAULT 0,
    unit_price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    cost_price DECIMAL(12,2) NULL,
    low_stock_threshold INT UNSIGNED NOT NULL DEFAULT 10,
    is_published TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ============================================================
-- Achats (approvisionnement du stock) — admin uniquement
-- ============================================================
CREATE TABLE purchases (
    id CHAR(36) PRIMARY KEY,
    supplier VARCHAR(150) NULL,
    recorded_by CHAR(36) NOT NULL,
    total_amount DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    note TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE purchase_items (
    id CHAR(36) PRIMARY KEY,
    purchase_id CHAR(36) NOT NULL,
    product_id CHAR(36) NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    unit_cost DECIMAL(12,2) NOT NULL,
    subtotal DECIMAL(14,2) NOT NULL,
    FOREIGN KEY (purchase_id) REFERENCES purchases(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ============================================================
-- Ventes — le vendeur vend et confirme le paiement
-- ============================================================
CREATE TABLE sales (
    id CHAR(36) PRIMARY KEY,
    sale_number INT UNSIGNED AUTO_INCREMENT UNIQUE,
    seller_id CHAR(36) NOT NULL,
    total_amount DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    payment_status ENUM('en_attente', 'paye') NOT NULL DEFAULT 'en_attente',
    payment_method ENUM('cash', 'moncash', 'natcash') NOT NULL DEFAULT 'cash',
    customer_name VARCHAR(150) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE sale_items (
    id CHAR(36) PRIMARY KEY,
    sale_id CHAR(36) NOT NULL,
    product_id CHAR(36) NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    unit_price DECIMAL(12,2) NOT NULL,
    subtotal DECIMAL(14,2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ============================================================
-- Retraits de caisse — demandés par un vendeur (optionnel) ou l'admin,
-- toujours validés par un admin
-- ============================================================
CREATE TABLE cash_withdrawals (
    id CHAR(36) PRIMARY KEY,
    type ENUM('produit_retourne', 'achat', 'salaire_employe', 'depense_diverse') NOT NULL,
    amount DECIMAL(14,2) NOT NULL,
    description TEXT NULL,
    requested_by CHAR(36) NULL,
    approved_by CHAR(36) NULL,
    status ENUM('en_attente', 'valide', 'refuse') NOT NULL DEFAULT 'en_attente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    resolved_at TIMESTAMP NULL,
    FOREIGN KEY (requested_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- Conversions de caisse — l'admin convertit un solde NatCash/MonCash en Cash
-- ============================================================
CREATE TABLE cash_conversions (
    id CHAR(36) PRIMARY KEY,
    from_method ENUM('natcash', 'moncash') NOT NULL,
    amount DECIMAL(14,2) NOT NULL,
    converted_by CHAR(36) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (converted_by) REFERENCES users(id)
) ENGINE=InnoDB;

-- ============================================================
-- Messagerie vendeur -> admin (avec notification non lue)
-- ============================================================
CREATE TABLE messages (
    id CHAR(36) PRIMARY KEY,
    sender_id CHAR(36) NOT NULL,
    recipient_id CHAR(36) NULL,
    content TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (recipient_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Recipient_id NULL = message envoyé à tout le monde (diffusion).
-- Le statut "lu" est suivi par destinataire dans message_reads
-- (nécessaire pour les diffusions, lues indépendamment par chacun).
CREATE TABLE message_reads (
    message_id CHAR(36) NOT NULL,
    user_id CHAR(36) NOT NULL,
    read_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (message_id, user_id),
    FOREIGN KEY (message_id) REFERENCES messages(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- Mouvements de stock : historique de toutes les actions qui changent
-- une quantité (réapprovisionnement, produit défectueux, ajustement,
-- vente). Alimenté automatiquement, jamais modifié à la main.
-- ============================================================
CREATE TABLE stock_movements (
    id CHAR(36) PRIMARY KEY,
    product_id CHAR(36) NOT NULL,
    type ENUM('reapprovisionnement', 'produit_defectueux', 'ajustement', 'vente') NOT NULL,
    quantity_change INT NOT NULL,
    quantity_after INT NOT NULL,
    reason TEXT NULL,
    created_by CHAR(36) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ============================================================
-- Index utiles
-- ============================================================
CREATE INDEX idx_stock_movements_product ON stock_movements(product_id, created_at);
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_sales_seller ON sales(seller_id);
CREATE INDEX idx_sale_items_product ON sale_items(product_id);
CREATE INDEX idx_purchase_items_product ON purchase_items(product_id);
CREATE INDEX idx_withdrawals_status ON cash_withdrawals(status);
CREATE INDEX idx_messages_recipient_read ON messages(recipient_id, is_read);

-- ============================================================
-- Compte admin par défaut (mot de passe : admin123 — à changer après la 1ère connexion)
-- Hash généré avec password_hash('admin123', PASSWORD_DEFAULT)
-- id généré en UUID v4 fixe pour ce seed
-- ============================================================
INSERT INTO users (id, name, email, username, password, role) VALUES
('00000000-0000-4000-8000-000000000001', 'Administrateur', 'admin@quincaillerie.local', 'admin', '$2y$10$s2mN3X4fvoLmXWhG9ChldeLrlJftfkzSKUMS9QY5SoJ3QtYA9LhDq', 'admin');
