-- Active: 1779583949864@@127.0.0.1@3306@mysql
-- Base de données : monmagasin
CREATE DATABASE IF NOT EXISTS monmagasin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE monmagasin;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nom        VARCHAR(100)  NOT NULL,
    prenom     VARCHAR(100)  NOT NULL,
    email      VARCHAR(150)  NOT NULL UNIQUE,
    password   VARCHAR(255)  NOT NULL,
    role       VARCHAR(50)   NOT NULL DEFAULT 'Vendeur',
    created_at TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Compte administrateur de démonstration (mot de passe : admin123)
INSERT IGNORE INTO users (nom, prenom, email, password, role)
VALUES ('Magasin', 'Admin', 'admin@monmagasin.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Super Utilisateur');
