-- =====================================================
-- ENGAGE Platform - Complete Database Installation
-- Generated: December 16, 2025
-- =====================================================
-- This file contains the complete database schema and initial data
-- Run this file once to set up the entire database

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Create and use database
--
CREATE DATABASE IF NOT EXISTS `projetweb3` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `projetweb3`;

-- =====================================================
-- TABLE STRUCTURES
-- =====================================================

--
-- Table structure for table `article`
--
DROP TABLE IF EXISTS `article`;
CREATE TABLE `article` (
  `id_article` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `contenu` text NOT NULL,
  `date_publication` date NOT NULL,
  PRIMARY KEY (`id_article`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `bad_words`
--
DROP TABLE IF EXISTS `bad_words`;
CREATE TABLE `bad_words` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `word` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `word` (`word`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `candidatures`
--
DROP TABLE IF EXISTS `candidatures`;
CREATE TABLE `candidatures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mission_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `pseudo_gaming` varchar(100) DEFAULT NULL,
  `niveau_experience` enum('Debutant','Intermediaire','Avance','Expert') NOT NULL DEFAULT 'Intermediaire',
  `disponibilites` text DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `cv` varchar(500) DEFAULT NULL,
  `statut` enum('en_attente','acceptee','rejetee') NOT NULL DEFAULT 'en_attente',
  `message_candidature` text DEFAULT NULL,
  `date_candidature` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_reponse` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_candidature_mission` (`mission_id`),
  KEY `fk_candidature_utilisateur` (`utilisateur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `evenement`
--
DROP TABLE IF EXISTS `evenement`;
CREATE TABLE `evenement` (
  `id_evenement` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `date_evenement` date NOT NULL,
  `heure_evenement` time DEFAULT NULL,
  `lieu` varchar(255) DEFAULT NULL,
  `prix` decimal(10,2) DEFAULT 0.00,
  `type_evenement` enum('gratuit','payant') DEFAULT 'gratuit',
  `vues` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_evenement`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `event_feedback`
--
DROP TABLE IF EXISTS `event_feedback`;
CREATE TABLE `event_feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_event` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL DEFAULT 5,
  `commentaire` longtext DEFAULT NULL,
  `date_feedback` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_event_user` (`id_event`,`id_utilisateur`),
  KEY `idx_event` (`id_event`),
  KEY `idx_user` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `feedback`
--
DROP TABLE IF EXISTS `feedback`;
CREATE TABLE `feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_mission` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `commentaire` text DEFAULT NULL,
  `date_feedback` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_mission_user` (`id_mission`,`id_utilisateur`),
  KEY `idx_mission` (`id_mission`),
  KEY `idx_user` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `item_comments`
--
DROP TABLE IF EXISTS `item_comments`;
CREATE TABLE `item_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `rating` tinyint(1) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_comment_item` (`item_id`),
  KEY `fk_comment_user` (`utilisateur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `likes`
--
DROP TABLE IF EXISTS `likes`;
CREATE TABLE `likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mission_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_like` (`mission_id`,`user_id`),
  KEY `idx_mission` (`mission_id`),
  KEY `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `likes_missions`
--
DROP TABLE IF EXISTS `likes_missions`;
CREATE TABLE `likes_missions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mission_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `date_like` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_like` (`mission_id`,`utilisateur_id`),
  KEY `idx_mission` (`mission_id`),
  KEY `idx_user` (`utilisateur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `missions`
--
DROP TABLE IF EXISTS `missions`;
CREATE TABLE `missions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `jeu` varchar(100) NOT NULL,
  `theme` varchar(100) DEFAULT NULL,
  `niveau_difficulte` enum('Facile','Moyen','Difficile','Expert') DEFAULT 'Moyen',
  `description` text NOT NULL,
  `competences_requises` text DEFAULT NULL,
  `salaire_propose` decimal(10,2) DEFAULT NULL,
  `duree_estimee` varchar(100) DEFAULT NULL,
  `nombre_places` int(11) NOT NULL DEFAULT 1,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `statut` enum('Ouverte','Fermee','En_cours','Terminee') NOT NULL DEFAULT 'Ouverte',
  `createur_id` int(11) DEFAULT NULL,
  `partenaire_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_mission_createur` (`createur_id`),
  KEY `fk_mission_partenaire` (`partenaire_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `orders`
--
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `shipping` enum('standard','express') NOT NULL DEFAULT 'standard',
  `total` decimal(10,2) NOT NULL,
  `payment_method` enum('online','onsite') NOT NULL DEFAULT 'online',
  `status` enum('pending','confirmed','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_order_user` (`utilisateur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `order_items`
--
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `qty` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_orderitem_order` (`order_id`),
  KEY `fk_orderitem_item` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `partenaires`
--
DROP TABLE IF EXISTS `partenaires`;
CREATE TABLE `partenaires` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `logo` varchar(500) DEFAULT NULL,
  `type` enum('sponsor','vendeur','testeur') NOT NULL DEFAULT 'sponsor',
  `statut` enum('actif','inactif') NOT NULL DEFAULT 'actif',
  `description` text DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `site_web` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `rating_sum` int(11) DEFAULT 0,
  `rating_count` int(11) DEFAULT 0,
  `rating_avg` decimal(3,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `participation`
--
DROP TABLE IF EXISTS `participation`;
CREATE TABLE `participation` (
  `id_participation` int(11) NOT NULL AUTO_INCREMENT,
  `id_evenement` int(11) NOT NULL,
  `id_volontaire` int(11) NOT NULL,
  `date_participation` timestamp NOT NULL DEFAULT current_timestamp(),
  `reference_paiement` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `views` int(11) DEFAULT 0,
  PRIMARY KEY (`id_participation`),
  KEY `idx_event` (`id_evenement`),
  KEY `idx_user` (`id_volontaire`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `reclamation`
--
DROP TABLE IF EXISTS `reclamation`;
CREATE TABLE `reclamation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int(11) NOT NULL,
  `sujet` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `priorite` enum('Basse','Moyenne','Élevée','Urgent') DEFAULT 'Moyenne',
  `statut` enum('Non traite','En cours','Traite') DEFAULT 'Non traite',
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `type_reclamation` enum('mission','evenement','partenaire','utilisateur','technique','autre') DEFAULT 'autre',
  `mission_id` int(11) DEFAULT NULL,
  `evenement_id` int(11) DEFAULT NULL,
  `partenaire_id` int(11) DEFAULT NULL,
  `utilisateur_cible_id` int(11) DEFAULT NULL,
  `technique_detail` text DEFAULT NULL,
  `category` varchar(50) DEFAULT 'general',
  `department` varchar(100) DEFAULT 'General Support',
  PRIMARY KEY (`id`),
  KEY `idx_user` (`utilisateur_id`),
  KEY `idx_category` (`category`),
  KEY `idx_priority` (`priorite`),
  KEY `idx_reclamation_category` (`category`),
  KEY `idx_reclamation_priority` (`priorite`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `reclamation_tags`
--
DROP TABLE IF EXISTS `reclamation_tags`;
CREATE TABLE `reclamation_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reclamation_id` int(11) NOT NULL,
  `tag` varchar(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_reclamation_tag` (`reclamation_id`,`tag`),
  KEY `idx_tag` (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `response`
--
DROP TABLE IF EXISTS `response`;
CREATE TABLE `response` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reclamation_id` int(11) NOT NULL,
  `response_text` text NOT NULL,
  `date_response` timestamp NOT NULL DEFAULT current_timestamp(),
  `admin_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_response_reclamation` (`reclamation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `store_items`
--
DROP TABLE IF EXISTS `store_items`;
CREATE TABLE `store_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `discounted_price` decimal(10,2) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image` varchar(500) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `partenaire_id` int(11) DEFAULT NULL,
  `statut` enum('disponible','rupture','archive') NOT NULL DEFAULT 'disponible',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `rating_sum` int(11) DEFAULT 0,
  `rating_count` int(11) DEFAULT 0,
  `rating_avg` decimal(3,2) DEFAULT NULL,
  `views` int(11) DEFAULT 0,
  `platform` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_item_partenaire` (`partenaire_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `utilisateur`
--
DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE `utilisateur` (
  `id_util` int(11) NOT NULL AUTO_INCREMENT,
  `prenom` varchar(100) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `dt_naiss` date DEFAULT NULL,
  `mail` varchar(150) NOT NULL,
  `num` varchar(20) DEFAULT NULL,
  `mdp` varchar(255) NOT NULL,
  `typee` enum('admin','client') NOT NULL DEFAULT 'client',
  `q1` varchar(255) DEFAULT NULL,
  `rp1` varchar(255) DEFAULT NULL,
  `q2` varchar(255) DEFAULT NULL,
  `rp2` varchar(255) DEFAULT NULL,
  `auth` varchar(20) DEFAULT 'desactive',
  `img` varchar(255) DEFAULT NULL,
  `face` text DEFAULT NULL COMMENT 'Face.js descriptor for facial recognition',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_util`),
  UNIQUE KEY `mail` (`mail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- INITIAL DATA
-- =====================================================

--
-- Data for table `article`
--
INSERT INTO `article` (`id_article`, `titre`, `contenu`, `date_publication`) VALUES
(3, 'sport', 'Pratiquer une activité physique régulière aide à rester en bonne santé, à prévenir les maladies et à se sentir bien chaque jour.', '2025-11-23'),
(4, 'sante', 'La santé est un trésor : protéger son corps par le sport et une alimentation saine est essentiel.', '2025-11-23'),
(5, 'education', 'Chaque enfant mérite une éducation de qualité pour réussir dans la vie.', '2025-11-23');

--
-- Data for table `bad_words`
--
INSERT INTO `bad_words` (`id`, `word`, `created_at`) VALUES
(1, 'merde', '2025-12-09 00:32:47'),
(2, 'con', '2025-12-09 00:32:47'),
(3, 'connard', '2025-12-09 00:32:47'),
(4, 'salope', '2025-12-09 00:32:47'),
(5, 'pute', '2025-12-09 00:32:47'),
(6, 'fuck', '2025-12-09 00:32:47'),
(7, 'shit', '2025-12-09 00:32:47'),
(8, 'damn', '2025-12-09 00:32:47'),
(9, 'idiot', '2025-12-09 00:32:47'),
(10, 'stupide', '2025-12-09 00:32:47'),
(14, 'connasse', '2025-12-11 16:11:50'),
(17, 'putain', '2025-12-11 16:11:50'),
(18, 'enculé', '2025-12-11 16:11:50'),
(19, 'enculer', '2025-12-11 16:11:50'),
(20, 'bite', '2025-12-11 16:11:50'),
(21, 'couilles', '2025-12-11 16:11:50'),
(22, 'chier', '2025-12-11 16:11:50'),
(23, 'chié', '2025-12-11 16:11:50'),
(24, 'bordel', '2025-12-11 16:11:50'),
(25, 'crétin', '2025-12-11 16:11:50'),
(27, 'imbécile', '2025-12-11 16:11:50'),
(29, 'débile', '2025-12-11 16:11:50'),
(30, 'abruti', '2025-12-11 16:11:50'),
(31, 'salaud', '2025-12-11 16:11:50'),
(32, 'fils de pute', '2025-12-11 16:11:50'),
(33, 'fdp', '2025-12-11 16:11:50'),
(34, 'pd', '2025-12-11 16:11:50'),
(35, 'pédé', '2025-12-11 16:11:50'),
(36, 'tapette', '2025-12-11 16:11:50'),
(37, 'pédale', '2025-12-11 16:11:50'),
(39, 'fucking', '2025-12-11 16:11:50'),
(42, 'bitch', '2025-12-11 16:11:50'),
(43, 'asshole', '2025-12-11 16:11:50'),
(44, 'bastard', '2025-12-11 16:11:50'),
(45, 'crap', '2025-12-11 16:11:50'),
(46, 'hell', '2025-12-11 16:11:50'),
(47, 'stupid', '2025-12-11 16:11:50'),
(49, 'dumb', '2025-12-11 16:11:50');

--
-- Data for table `candidatures`
--
INSERT INTO `candidatures` (`id`, `mission_id`, `utilisateur_id`, `pseudo_gaming`, `niveau_experience`, `disponibilites`, `email`, `cv`, `statut`, `message_candidature`, `date_candidature`, `date_reponse`) VALUES
(1, 1, 2, 'ProGamer92', 'Avance', 'Lundi à vendredi, 18h-22h', 'jean.dupont@email.com', NULL, 'en_attente', 'Je joue à Fortnite depuis 3 ans, j\'ai déjà participé à des tests beta. Très motivé pour cette mission !', '2025-12-01 20:05:07', NULL),
(2, 1, 3, 'ShadowKnight', 'Intermediaire', 'Week-ends et soirées', 'marie.martin@email.com', NULL, 'acceptee', 'Passionnée de Fortnite, je serais ravie de contribuer à l\'amélioration du jeu.', '2025-12-01 20:05:07', NULL),
(3, 3, 2, 'ProGamer92', 'Expert', 'Tous les jours 20h-23h', 'jean.dupont@email.com', NULL, 'en_attente', 'Coach expérimenté en Valorant, j\'ai déjà aidé plusieurs équipes à monter en rank.', '2025-12-01 20:05:07', NULL),
(4, 2, 3, 'ShadowKnight', 'Debutant', 'Mercredi et samedi', 'marie.martin@email.com', NULL, 'rejetee', 'Je débute en développement mais je suis très motivée pour apprendre.', '2025-12-01 20:05:07', NULL);

--
-- Data for table `feedback`
--
INSERT INTO `feedback` (`id`, `id_mission`, `id_utilisateur`, `rating`, `commentaire`, `date_feedback`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 3, 'ileey', '2025-12-02 08:41:34', '2025-12-02 08:41:34', '2025-12-02 08:41:34'),
(2, 1, 4, 2, '', '2025-12-02 09:11:40', '2025-12-02 09:11:40', '2025-12-02 09:11:40'),
(3, 1, 5, 3, 'tress bien', '2025-12-02 09:14:07', '2025-12-02 09:14:07', '2025-12-02 09:14:07'),
(4, 5, 1, 4, 'waaaaw', '2025-12-08 22:24:10', '2025-12-08 22:24:10', '2025-12-08 22:24:10');

--
-- Data for table `likes_missions`
--
INSERT INTO `likes_missions` (`id`, `mission_id`, `utilisateur_id`, `date_like`) VALUES
(1, 7, 1, '2025-12-09 00:10:58'),
(2, 10, 7, '2025-12-13 17:36:58');

--
-- Data for table `missions`
--
INSERT INTO `missions` (`id`, `titre`, `jeu`, `theme`, `niveau_difficulte`, `description`, `competences_requises`, `salaire_propose`, `duree_estimee`, `nombre_places`, `date_creation`, `date_debut`, `date_fin`, `statut`, `createur_id`, `partenaire_id`) VALUES
(1, 'Testeur de jeu Alpha - Fortnite', 'Fortnite', 'valorant', 'Facile', 'Nous recherchons des testeurs pour la nouvelle saison de Fortnite. Vous testerez les nouvelles armes, cartes et mécaniques de jeu.', 'Bonnes connaissances de Fortnite, capacité à décrire les bugs, sérieux', 500.00, '2 semaines', 10, '2025-12-01 20:03:51', '2025-12-01', '2025-12-15', 'Ouverte', 1, NULL),
(2, 'Développeur de mods - Minecraft', 'Minecraft', 'Création', 'Moyen', 'Création de mods pour ajouter de nouvelles fonctionnalités au jeu. Idéal pour les développeurs passionnés.', 'Java, JSON, expérience avec Minecraft Forge', 1200.50, '1 mois', 5, '2025-12-01 20:03:51', '2025-12-10', '2026-01-10', 'Ouverte', 2, NULL),
(3, 'Coach eSport - Valorant', 'Valorant', 'esport', 'Difficile', 'Coach pour équipe amateur souhaitant progresser en compétition. Analyse de parties et stratégies.', 'Rank Diamant+, expérience en coaching, bonne communication', 800.00, '3 mois', 3, '2025-12-01 20:03:51', '2025-12-05', '2026-03-05', 'Ouverte', 3, NULL),
(4, 'Traducteur - RPG Fantasy', 'Skyrim', 'roblex', '', 'Traduction de quêtes et dialogues du français vers l\'anglais pour un mod communautaire.', 'Bilingue français/anglais, connaissance des jeux RPG', 300.75, '2 semaines', 8, '2025-12-01 20:03:51', '2025-11-30', '2025-12-14', 'Fermee', 1, NULL),
(5, 'Graphiste - Conception de skins', 'League of Legends', 'education', 'Expert', 'Création de skins originaux pour champions de LoL. Portfolio exigé.', 'Photoshop, Illustrator, connaissance de l\'univers LoL', 1500.00, '6 semaines', 2, '2025-12-01 20:03:51', '2025-12-15', '2026-01-26', 'Ouverte', 2, NULL),
(7, 'mission test', 'fifa', 'sport', 'Moyen', 'une bonne mission', 'motivé', NULL, NULL, 1, '2025-12-08 22:40:08', '2025-12-08', '2025-12-31', 'Ouverte', 1, NULL),
(9, 'voyage', 'valorant', 'education', 'Facile', 'une bonne mission', 'un expert', NULL, NULL, 1, '2025-12-13 16:46:19', '2025-12-20', '2025-12-27', 'Ouverte', 1, NULL),
(10, 'esprit', 'friv', 'education', 'Difficile', 'une mission interresnte', 'un debutant', NULL, NULL, 1, '2025-12-13 17:10:36', '2025-12-14', '2025-12-25', 'Ouverte', 1, NULL);

--
-- Data for table `orders`
--
INSERT INTO `orders` (`id`, `utilisateur_id`, `name`, `email`, `phone`, `address`, `city`, `shipping`, `total`, `payment_method`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'eya korsan', 'eyakorsan50@gmail.com', '50698083', 'ariana', 'ariana', 'express', 60.00, 'onsite', 'pending', '2025-12-16 00:21:09', '2025-12-16 00:21:09'),
(2, 1, 'eya', 'eedd@gmail.com', '50698083', 'ariana', 'ariana', 'standard', 70.00, 'onsite', 'pending', '2025-12-16 00:31:46', '2025-12-16 00:31:46');

--
-- Data for table `order_items`
--
INSERT INTO `order_items` (`id`, `order_id`, `item_id`, `name`, `price`, `qty`) VALUES
(1, 1, 4, 'Star Wars', 60.00, 1),
(2, 2, 5, 'sims', 70.00, 1);

--
-- Data for table `partenaires`
--
INSERT INTO `partenaires` (`id`, `nom`, `logo`, `type`, `statut`, `description`, `email`, `telephone`, `site_web`, `created_by`, `created_at`, `updated_at`, `rating_sum`, `rating_count`, `rating_avg`) VALUES
(1, 'sony', 'view/frontoffice/assets/uploads/logos/logo_6940b83e63615.webp', 'sponsor', 'actif', 'Sony Corporation, fondée en 1946, est une entreprise multinationale japonaise qui a évolué d\'une petite entreprise de réparation électronique à un géant diversifié.', 'sony@gmail.com', '50698083', 'https://www.sony.com/fr-tn/all-products', NULL, '2025-12-15 18:08:01', '2025-12-16 01:39:10', 0, 0, NULL),
(2, 'EA', 'view/frontoffice/assets/uploads/logos/logo_6940b823eea97.webp', 'sponsor', 'actif', 'EA Play est un service de jeu vidéo par abonnement d\'Electronic Arts.', 'EA@gmail.com', '50698083', 'https://www.ea.com/fr-fr/ea-play', NULL, '2025-12-15 18:11:27', '2025-12-16 01:38:43', 0, 0, NULL),
(3, 'Nintendo', 'view/frontoffice/assets/uploads/logos/logo_6940b815c96f1.webp', 'vendeur', 'actif', 'Founded in 1889 by Fusajiro Yamauchi in Kyoto, Japan.', 'Nintendo@gmail.com', '+21650698083', 'https://www.nintendo.com/fr-fr/', NULL, '2025-12-15 18:13:38', '2025-12-16 01:38:29', 0, 0, NULL);

--
-- Data for table `store_items` (corrected to match table structure)
--
INSERT INTO `store_items` (`id`, `partenaire_id`, `name`, `price`, `stock`, `category`, `image`, `description`, `platform`, `views`, `rating_sum`, `rating_count`, `rating_avg`, `created_at`, `updated_at`) VALUES
(1, 1, 'the last of us', 94.32, 47, 'simulation', 'view/frontoffice/assets/uploads/games/game_6940b6b051f6c.webp', '', 'ps5', 3, 0, 0, NULL, '2025-12-15 18:29:14', '2025-12-16 07:45:20'),
(2, 2, 'Battlefield', 63.00, 21, 'action', 'view/frontoffice/assets/uploads/games/game_6940b6a1acdfd.webp', '', 'PC', 1, 0, 0, NULL, '2025-12-15 18:51:36', '2025-12-16 01:32:17'),
(3, 3, 'nintendo', 32.00, 92, 'educatif', 'view/frontoffice/assets/uploads/games/game_6940b6c172d05.webp', '', 'ps4', 2, 0, 0, NULL, '2025-12-15 18:27:52', '2025-12-16 01:32:49'),
(4, 1, 'Star Wars', 60.00, 63, 'action', 'view/frontoffice/assets/uploads/games/game_6940b6fb61586.webp', '', 'ps5', 2, 0, 0, NULL, '2025-12-15 18:24:50', '2025-12-16 07:45:43'),
(5, 2, 'sims', 70.00, 10, 'simulation', 'view/frontoffice/assets/uploads/games/game_6940b6e5ab1c0.webp', '', 'PC', 3, 0, 0, NULL, '2025-12-15 18:25:19', '2025-12-16 07:45:32'),
(6, 2, 'fifa', 38.63, 20, 'sport', 'view/frontoffice/assets/uploads/games/game_6940b6d6c244f.webp', '', 'PC', 2, 0, 0, NULL, '2025-12-15 18:26:50', '2025-12-16 07:46:20');

-- =====================================================
-- END OF DATABASE INSTALLATION
-- =====================================================

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
