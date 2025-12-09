-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 26, 2025 at 05:40 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `projetweb3`
--

-- --------------------------------------------------------

--
-- Table structure for table `candidatures`
--

CREATE TABLE `candidatures` (
  `id` int(11) NOT NULL,
  `mission_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `pseudo_gaming` varchar(100) NOT NULL,
  `niveau_experience` enum('Debutant','Intermediaire','Avance','Expert') DEFAULT 'Intermediaire',
  `disponibilites` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `statut` enum('en_attente','acceptee','rejetee','annulee') DEFAULT 'en_attente',
  `message_candidature` longtext DEFAULT NULL,
  `date_candidature` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_reponse` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `evenement`
--

CREATE TABLE `evenement` (
  `id_evenement` int(11) NOT NULL,
  `titre` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `date_evenement` date NOT NULL,
  `lieu` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `id_organisation` int(11) NOT NULL COMMENT 'User who organized the event',
  `created_by` int(11) NOT NULL COMMENT 'User who created the event',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `item_comments`
--

CREATE TABLE `item_comments` (
  `id` int(11) NOT NULL,
  `store_item_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `author_name` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `missions`
--

CREATE TABLE `missions` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `jeu` varchar(100) NOT NULL,
  `theme` varchar(100) DEFAULT NULL,
  `niveau_difficulte` enum('Facile','Moyen','Difficile','Expert') DEFAULT 'Moyen',
  `description` longtext DEFAULT NULL,
  `competences_requises` varchar(500) DEFAULT NULL,
  `salaire_propose` decimal(10,2) DEFAULT NULL,
  `duree_estimee` varchar(100) DEFAULT NULL,
  `nombre_places` int(11) DEFAULT 1,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `statut` enum('Ouverte','En cours','Fermee','Annulee') DEFAULT 'Ouverte',
  `createur_id` int(11) NOT NULL,
  `partenaire_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `name` varchar(120) DEFAULT NULL,
  `email` varchar(160) DEFAULT NULL,
  `phone` varchar(40) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(80) DEFAULT NULL,
  `shipping` varchar(20) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `partenaires`
--

CREATE TABLE `partenaires` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `type` enum('sponsor','testeur','vendeur') NOT NULL DEFAULT 'sponsor',
  `statut` enum('actif','inactif','en_attente','suspendu') NOT NULL DEFAULT 'en_attente',
  `description` longtext DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `site_web` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL COMMENT 'Admin user who created this partner',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `rating_sum` int(11) DEFAULT 0,
  `rating_count` int(11) DEFAULT 0,
  `rating_avg` decimal(3,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `participation`
--

CREATE TABLE `participation` (
  `id_participation` int(11) NOT NULL,
  `id_evenement` int(11) NOT NULL,
  `id_volontaire` int(11) NOT NULL,
  `date_participation` date DEFAULT curdate(),
  `statut` enum('en attente','acceptée','refusée') DEFAULT 'en attente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reclamation`
--

CREATE TABLE `reclamation` (
  `id` int(11) NOT NULL,
  `sujet` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `email` varchar(255) NOT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `statut` enum('Non traite','En cours','Traite') DEFAULT 'Non traite',
  `reponse` longtext DEFAULT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `priorite` enum('Basse','Moyenne','Haute','Urgente') DEFAULT 'Moyenne',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `response`
--

CREATE TABLE `response` (
  `id` int(11) NOT NULL,
  `reclamation_id` int(11) NOT NULL,
  `contenu` longtext NOT NULL,
  `date_response` timestamp NOT NULL DEFAULT current_timestamp(),
  `admin_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `store_items`
--

CREATE TABLE `store_items` (
  `id` int(11) NOT NULL,
  `partenaire_id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `categorie` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `plateforme` varchar(100) DEFAULT NULL,
  `age_minimum` int(11) DEFAULT 3,
  `likes_count` int(11) NOT NULL DEFAULT 0,
  `views_count` int(11) NOT NULL DEFAULT 0,
  `created_by` int(11) DEFAULT NULL COMMENT 'Admin user who added this item',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `rating_sum` int(11) DEFAULT 0,
  `rating_count` int(11) DEFAULT 0,
  `rating_avg` decimal(3,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id_util` int(11) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `dt_naiss` date DEFAULT NULL,
  `mail` varchar(255) NOT NULL,
  `num` int(11) DEFAULT NULL,
  `mdp` varchar(255) NOT NULL,
  `typee` enum('admin','user') DEFAULT 'user',
  `q1` varchar(255) DEFAULT NULL,
  `rp1` varchar(255) DEFAULT NULL,
  `q2` varchar(255) DEFAULT NULL,
  `rp2` varchar(255) DEFAULT NULL,
  `gamer_tag` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `utilisateur`
--

INSERT INTO `utilisateur` (`id_util`, `prenom`, `nom`, `dt_naiss`, `mail`, `num`, `mdp`, `typee`, `q1`, `rp1`, `q2`, `rp2`, `gamer_tag`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'System', '1990-01-01', 'admin@engage.com', 600000001, 'admin123', 'admin', 'Quelle est votre couleur preferee?', 'Bleu', 'Quel est votre animal prefere?', 'Chat', 'AdminGamer', '2025-11-23 14:45:53', '2025-11-23 14:45:53'),
(2, 'Jean', 'Dupont', '1995-05-15', 'jean.dupont@email.com', 612345678, 'password123', 'user', 'Quelle est votre couleur preferee?', 'Rouge', 'Quel est votre animal prefere?', 'Chien', 'ProGamer92', '2025-11-23 14:45:53', '2025-11-23 14:45:53'),
(3, 'Marie', 'Martin', '1992-03-20', 'marie.martin@email.com', 623456789, 'password123', 'user', 'Quelle est votre couleur preferee?', 'Vert', 'Quel est votre animal prefere?', 'Oiseau', 'ShadowKnight', '2025-11-23 14:45:53', '2025-11-23 14:45:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `candidatures`
--
ALTER TABLE `candidatures`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_candidature` (`mission_id`,`utilisateur_id`),
  ADD KEY `idx_mission_id` (`mission_id`),
  ADD KEY `idx_utilisateur_id` (`utilisateur_id`),
  ADD KEY `idx_statut` (`statut`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_date_candidature` (`date_candidature`);
ALTER TABLE `candidatures` ADD FULLTEXT KEY `ft_pseudo_email` (`pseudo_gaming`,`email`);

--
-- Indexes for table `evenement`
--
ALTER TABLE `evenement`
  ADD PRIMARY KEY (`id_evenement`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_date` (`date_evenement`),
  ADD KEY `idx_organisation` (`id_organisation`);

--
-- Indexes for table `item_comments`
--
ALTER TABLE `item_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_item` (`store_item_id`),
  ADD KEY `idx_user` (`utilisateur_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `missions`
--
ALTER TABLE `missions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `partenaire_id` (`partenaire_id`),
  ADD KEY `idx_statut` (`statut`),
  ADD KEY `idx_jeu` (`jeu`),
  ADD KEY `idx_date_creation` (`date_creation`),
  ADD KEY `idx_createur` (`createur_id`);
ALTER TABLE `missions` ADD FULLTEXT KEY `ft_titre_description` (`titre`,`description`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`utilisateur_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order` (`order_id`),
  ADD KEY `idx_item` (`item_id`);

--
-- Indexes for table `partenaires`
--
ALTER TABLE `partenaires`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_nom` (`nom`),
  ADD UNIQUE KEY `unique_email` (`email`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_statut` (`statut`),
  ADD KEY `idx_type` (`type`);
ALTER TABLE `partenaires` ADD FULLTEXT KEY `ft_nom_desc` (`nom`,`description`);

--
-- Indexes for table `participation`
--
ALTER TABLE `participation`
  ADD PRIMARY KEY (`id_participation`),
  ADD UNIQUE KEY `unique_participation` (`id_evenement`,`id_volontaire`),
  ADD KEY `fk_participation_evenement` (`id_evenement`),
  ADD KEY `fk_participation_volontaire` (`id_volontaire`);

--
-- Indexes for table `reclamation`
--
ALTER TABLE `reclamation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_statut` (`statut`),
  ADD KEY `idx_priorite` (`priorite`),
  ADD KEY `idx_date_creation` (`date_creation`),
  ADD KEY `idx_utilisateur_id` (`utilisateur_id`);
ALTER TABLE `reclamation` ADD FULLTEXT KEY `ft_sujet_description` (`sujet`,`description`);

--
-- Indexes for table `response`
--
ALTER TABLE `response`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reclamation_id` (`reclamation_id`),
  ADD KEY `idx_admin_id` (`admin_id`),
  ADD KEY `idx_date_response` (`date_response`);

--
-- Indexes for table `store_items`
--
ALTER TABLE `store_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_partenaire` (`partenaire_id`),
  ADD KEY `idx_categorie` (`categorie`),
  ADD KEY `idx_prix` (`prix`),
  ADD KEY `idx_stock` (`stock`);
ALTER TABLE `store_items` ADD FULLTEXT KEY `ft_nom_desc` (`nom`,`description`);

--
-- Indexes for table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id_util`),
  ADD UNIQUE KEY `unique_mail` (`mail`),
  ADD UNIQUE KEY `unique_gamer_tag` (`gamer_tag`),
  ADD KEY `idx_typee` (`typee`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `candidatures`
--
ALTER TABLE `candidatures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `evenement`
--
ALTER TABLE `evenement`
  MODIFY `id_evenement` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_comments`
--
ALTER TABLE `item_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `missions`
--
ALTER TABLE `missions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `partenaires`
--
ALTER TABLE `partenaires`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `participation`
--
ALTER TABLE `participation`
  MODIFY `id_participation` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reclamation`
--
ALTER TABLE `reclamation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `response`
--
ALTER TABLE `response`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `store_items`
--
ALTER TABLE `store_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id_util` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `candidatures`
--
ALTER TABLE `candidatures`
  ADD CONSTRAINT `candidatures_ibfk_1` FOREIGN KEY (`mission_id`) REFERENCES `missions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `candidatures_ibfk_2` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id_util`) ON DELETE CASCADE;

--
-- Constraints for table `evenement`
--
ALTER TABLE `evenement`
  ADD CONSTRAINT `evenement_ibfk_1` FOREIGN KEY (`id_organisation`) REFERENCES `utilisateur` (`id_util`) ON DELETE CASCADE,
  ADD CONSTRAINT `evenement_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `utilisateur` (`id_util`) ON DELETE CASCADE;

--
-- Constraints for table `item_comments`
--
ALTER TABLE `item_comments`
  ADD CONSTRAINT `item_comments_ibfk_1` FOREIGN KEY (`store_item_id`) REFERENCES `store_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_comments_ibfk_2` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id_util`) ON DELETE CASCADE;

--
-- Constraints for table `missions`
--
ALTER TABLE `missions`
  ADD CONSTRAINT `missions_ibfk_1` FOREIGN KEY (`createur_id`) REFERENCES `utilisateur` (`id_util`) ON DELETE CASCADE,
  ADD CONSTRAINT `missions_ibfk_2` FOREIGN KEY (`partenaire_id`) REFERENCES `partenaires` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id_util`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `store_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `partenaires`
--
ALTER TABLE `partenaires`
  ADD CONSTRAINT `partenaires_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `utilisateur` (`id_util`) ON DELETE SET NULL;

--
-- Constraints for table `participation`
--
ALTER TABLE `participation`
  ADD CONSTRAINT `participation_ibfk_1` FOREIGN KEY (`id_evenement`) REFERENCES `evenement` (`id_evenement`) ON DELETE CASCADE,
  ADD CONSTRAINT `participation_ibfk_2` FOREIGN KEY (`id_volontaire`) REFERENCES `utilisateur` (`id_util`) ON DELETE CASCADE;

--
-- Constraints for table `reclamation`
--
ALTER TABLE `reclamation`
  ADD CONSTRAINT `reclamation_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id_util`) ON DELETE CASCADE,
  ADD CONSTRAINT `reclamation_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `store_items` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `response`
--
ALTER TABLE `response`
  ADD CONSTRAINT `response_ibfk_1` FOREIGN KEY (`reclamation_id`) REFERENCES `reclamation` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `response_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `utilisateur` (`id_util`) ON DELETE CASCADE;

--
-- Constraints for table `store_items`
--
ALTER TABLE `store_items`
  ADD CONSTRAINT `store_items_ibfk_1` FOREIGN KEY (`partenaire_id`) REFERENCES `partenaires` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `store_items_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `utilisateur` (`id_util`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
