-- ============================================
-- SCHEMA BASE DE DONNÉES - MODULE GAMIFICATION
-- Plateforme ENGAGE - Matchmaking volontariat
-- ============================================

-- Créer la base de données
CREATE DATABASE IF NOT EXISTS projetweb DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE projetweb;

-- Supprimer d'éventuelles anciennes tables (évite les erreurs lors de l'import)
DROP TABLE IF EXISTS store_items;
DROP TABLE IF EXISTS partenaires;

-- ============================================
-- TABLE: PARTENAIRES
-- ============================================
CREATE TABLE IF NOT EXISTS partenaires (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255) NOT NULL UNIQUE,
    logo VARCHAR(255),
    type ENUM('sponsor', 'testeur', 'vendeur') NOT NULL DEFAULT 'sponsor',
    statut ENUM('actif', 'inactif', 'en_attente') NOT NULL DEFAULT 'en_attente',
    description LONGTEXT,
    email VARCHAR(255) UNIQUE,
    telephone VARCHAR(20),
    site_web VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_statut (statut),
    INDEX idx_type (type),
    INDEX idx_email (email),
    FULLTEXT INDEX ft_nom_desc (nom, description)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: STORE_ITEMS (Articles/Jeux)
-- ============================================
CREATE TABLE IF NOT EXISTS store_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    partenaire_id INT NOT NULL,
    nom VARCHAR(255) NOT NULL,
    prix DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    categorie VARCHAR(100) NOT NULL,
    image VARCHAR(255),
    description LONGTEXT,
    plateforme VARCHAR(100),
    age_minimum INT DEFAULT 3,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_partenaire (partenaire_id),
    INDEX idx_categorie (categorie),
    INDEX idx_prix (prix),
    INDEX idx_stock (stock),
    FOREIGN KEY (partenaire_id) REFERENCES partenaires(id) ON DELETE CASCADE,
    FULLTEXT INDEX ft_nom_desc (nom, description)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DONNÉES DE TEST (optionnel)
-- ============================================
INSERT INTO partenaires (nom, type, statut, description, email) VALUES
('GameStudio Pro', 'sponsor', 'actif', 'Studio de développement de jeux vidéo spécialisé dans les jeux éducatifs', 'contact@gamestudiopro.com'),
('TestGames Inc', 'testeur', 'actif', 'Entreprise de test de jeux vidéo', 'info@testgames.com'),
('GameStore Online', 'vendeur', 'actif', 'Boutique en ligne de jeux vidéo', 'sales@gamestore.com');

INSERT INTO store_items (partenaire_id, nom, prix, stock, categorie, description, plateforme, age_minimum) VALUES
(1, 'EduGame Adventure', 29.99, 50, 'Éducatif', 'Jeu d''aventure éducatif pour enfants', 'PC', 7),
(2, 'Math Quest', 19.99, 30, 'Éducatif', 'Jeu de mathématiques interactif', 'Mobile', 5),
(3, 'Science Explorer', 39.99, 25, 'Éducatif', 'Exploration scientifique en jeu', 'PC', 10);

