-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : mar. 16 déc. 2025 à 00:12
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `projetweb3`
--

-- --------------------------------------------------------

--
-- Structure de la table `article`
--

CREATE TABLE `article` (
  `id_article` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `contenu` text NOT NULL,
  `date_publication` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `article`
--

INSERT INTO `article` (`id_article`, `titre`, `contenu`, `date_publication`) VALUES
(3, 'sport', 'Pratiquer une activité physique régulière aide à rester en bonne santé, à prévenir les maladies et à se sentir bien chaque jour.', '2025-11-23'),
(4, 'sante', 'La santé est un trésor : protéger son corps par le sport et une alimentation saine est essentiel.', '2025-11-23'),
(5, 'education', 'Chaque enfant mérite une éducation de qualité pour réussir dans la vie.', '2025-11-23');

-- --------------------------------------------------------

--
-- Structure de la table `bad_words`
--

CREATE TABLE `bad_words` (
  `id` int(11) NOT NULL,
  `word` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `bad_words`
--

INSERT INTO `bad_words` (`id`, `word`, `created_at`) VALUES
(1, 'merde', '2025-12-11 16:11:50'),
(2, 'con', '2025-12-11 16:11:50'),
(3, 'connard', '2025-12-11 16:11:50'),
(4, 'salope', '2025-12-11 16:11:50'),
(5, 'pute', '2025-12-11 16:11:50'),
(6, 'fuck', '2025-12-11 16:11:50'),
(7, 'shit', '2025-12-11 16:11:50'),
(8, 'damn', '2025-12-11 16:11:50'),
(9, 'idiot', '2025-12-11 16:11:50'),
(10, 'stupide', '2025-12-11 16:11:50'),
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
(49, 'dumb', '2025-12-11 16:11:50'),
(50, 'retard', '2025-12-11 16:11:50'),
(51, 'moron', '2025-12-11 16:11:50'),
(52, 'm3rd3', '2025-12-11 16:11:50'),
(53, 'c0n', '2025-12-11 16:11:50'),
(54, 'f*ck', '2025-12-11 16:11:50'),
(55, 'sh!t', '2025-12-11 16:11:50'),
(56, 'b!tch', '2025-12-11 16:11:50'),
(57, '@sshole', '2025-12-11 16:11:50'),
(58, 'nazi', '2025-12-11 16:11:50'),
(59, 'hitler', '2025-12-11 16:11:50'),
(62, 'merdique', '2025-12-11 16:11:50'),
(63, 'connerie', '2025-12-11 16:11:50'),
(64, 'saloperie', '2025-12-11 16:11:50'),
(65, 'putasserie', '2025-12-11 16:11:50'),
(66, 'enfoiré', '2025-12-11 16:11:50'),
(67, 'enfoirée', '2025-12-11 16:11:50'),
(69, 'salaude', '2025-12-11 16:11:50'),
(72, 'trou du cul', '2025-12-11 16:11:50'),
(73, 'trouduc', '2025-12-11 16:11:50'),
(74, 'foutre', '2025-12-11 16:11:50'),
(76, 'niquer', '2025-12-11 16:11:50'),
(77, 'nique', '2025-12-11 16:11:50'),
(78, 'branleur', '2025-12-11 16:11:50'),
(79, 'branleuse', '2025-12-11 16:11:50'),
(80, 'branlette', '2025-12-11 16:11:50'),
(81, 'suce', '2025-12-11 16:11:50'),
(82, 'sucer', '2025-12-11 16:11:50'),
(83, 'sucette', '2025-12-11 16:11:50'),
(85, 'bites', '2025-12-11 16:11:50'),
(86, 'chibre', '2025-12-11 16:11:50'),
(87, 'couille', '2025-12-11 16:11:50'),
(89, 'burnes', '2025-12-11 16:11:50'),
(90, 'chatte', '2025-12-11 16:11:50'),
(91, 'chattes', '2025-12-11 16:11:50'),
(92, 'chat', '2025-12-11 16:11:50'),
(93, 'cul', '2025-12-11 16:11:50'),
(94, 'culs', '2025-12-11 16:11:50'),
(95, 'fion', '2025-12-11 16:11:50'),
(96, 'pisse', '2025-12-11 16:11:50'),
(97, 'pisser', '2025-12-11 16:11:50'),
(98, 'piss', '2025-12-11 16:11:50'),
(99, 'pète', '2025-12-11 16:11:50'),
(100, 'péter', '2025-12-11 16:11:50'),
(101, 'pet', '2025-12-11 16:11:50'),
(102, 'caca', '2025-12-11 16:11:50'),
(107, 'chiasse', '2025-12-11 16:11:50'),
(109, 'bordel de merde', '2025-12-11 16:11:50'),
(110, 'putain de', '2025-12-11 16:11:50'),
(116, 'enculée', '2025-12-11 16:11:50'),
(122, 'foutu', '2025-12-11 16:11:50'),
(126, 'conneries', '2025-12-11 16:11:50'),
(128, 'cons', '2025-12-11 16:11:50'),
(131, 'connasses', '2025-12-11 16:11:50'),
(133, 'salopes', '2025-12-11 16:11:50'),
(135, 'putes', '2025-12-11 16:11:50'),
(137, 'putains', '2025-12-11 16:11:50'),
(139, 'bitches', '2025-12-11 16:11:50'),
(142, 'fucked', '2025-12-11 16:11:50'),
(144, 'shits', '2025-12-11 16:11:50'),
(145, 'shitty', '2025-12-11 16:11:50'),
(147, 'damned', '2025-12-11 16:11:50'),
(149, 'assholes', '2025-12-11 16:11:50'),
(151, 'bastards', '2025-12-11 16:11:50'),
(153, 'craps', '2025-12-11 16:11:50'),
(155, 'hells', '2025-12-11 16:11:50'),
(157, 'stupids', '2025-12-11 16:11:50'),
(159, 'idiots', '2025-12-11 16:11:50'),
(161, 'dumber', '2025-12-11 16:11:50'),
(163, 'retards', '2025-12-11 16:11:50'),
(165, 'morons', '2025-12-11 16:11:50'),
(167, 'imbéciles', '2025-12-11 16:11:50'),
(169, 'crétins', '2025-12-11 16:11:50'),
(171, 'débiles', '2025-12-11 16:11:50'),
(173, 'abrutis', '2025-12-11 16:11:50'),
(175, 'salauds', '2025-12-11 16:11:50'),
(177, 'salaudes', '2025-12-11 16:11:50'),
(182, 'pédés', '2025-12-11 16:11:50'),
(184, 'tapettes', '2025-12-11 16:11:50'),
(186, 'pédales', '2025-12-11 16:11:50'),
(188, 'nazis', '2025-12-11 16:11:50'),
(190, 'hitlers', '2025-12-11 16:11:50');

-- --------------------------------------------------------

--
-- Structure de la table `candidatures`
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
  `date_reponse` timestamp NULL DEFAULT NULL,
  `vu` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `candidatures`
--

INSERT INTO `candidatures` (`id`, `mission_id`, `utilisateur_id`, `pseudo_gaming`, `niveau_experience`, `disponibilites`, `email`, `statut`, `message_candidature`, `date_candidature`, `date_reponse`, `vu`) VALUES
(1, 1, 2, 'ProGamer92', 'Expert', 'Disponible soirs et weekends', 'jean.dupont@email.com', 'en_attente', 'Je suis tres interesse! J ai 5 ans d experience en raids coordonnes.', '2025-11-26 17:31:09', NULL, 0),
(2, 1, 3, 'ShadowKnight', 'Avance', 'Flexible', 'marie.martin@email.com', 'acceptee', 'Confirme ma participation au raid boss!', '2025-11-26 17:31:09', NULL, 0),
(3, 2, 2, 'CyberNinja', 'Avance', 'Soirs apres 20h', 'jean.dupont@email.com', 'en_attente', 'J ai de l experience en competition, j aimerais participer.', '2025-11-26 17:31:09', NULL, 0),
(4, 3, 3, 'TestMaster', 'Expert', 'Flexible - travail remote', 'marie.martin@email.com', 'acceptee', 'J ai une experience extensive en QA testing.', '2025-11-26 17:31:09', NULL, 0),
(5, 4, 2, 'LearningFocus', 'Debutant', 'Weekends', 'jean.dupont@email.com', 'rejetee', 'Je suis debutant et j aimerais apprendre Dota2.', '2025-11-26 17:31:09', NULL, 0),
(12, 5, 4, 'midox', 'Expert', 'azd', 'Mahmoud.Mkaddem@esprit.tn', 'acceptee', NULL, '2025-12-11 17:26:19', '2025-12-11 17:26:37', 1),
(13, 5, 1, 'midox', 'Expert', 'azd', 'Mahmoud.Mkaddem@esprit.tn', 'acceptee', NULL, '2025-12-11 17:26:39', NULL, 0);

-- --------------------------------------------------------

--
-- Structure de la table `evenement`
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `heure_evenement` time DEFAULT NULL,
  `duree_minutes` int(11) DEFAULT NULL,
  `type_evenement` varchar(100) DEFAULT 'gratuit',
  `prix` decimal(10,2) DEFAULT NULL,
  `vues` int(11) NOT NULL DEFAULT 0,
  `theme` varchar(100) DEFAULT 'evenement'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `evenement`
--

INSERT INTO `evenement` (`id_evenement`, `titre`, `description`, `date_evenement`, `lieu`, `image`, `id_organisation`, `created_by`, `created_at`, `updated_at`, `heure_evenement`, `duree_minutes`, `type_evenement`, `prix`, `vues`, `theme`) VALUES
(2, 'gta5', 'un evenment de gta5 online mode', '2026-12-09', 'beb swi9a', 'uploads/events/6937791112171_Grand_Theft_Auto_V.png', 3, 4, '2025-12-09 01:19:13', '2025-12-15 10:37:27', '15:18:00', 30, 'gratuit', NULL, 23, 'evenement');

-- --------------------------------------------------------

--
-- Structure de la table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `id_mission` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL DEFAULT 5 COMMENT 'Rating from 1 to 5',
  `commentaire` longtext DEFAULT NULL,
  `date_feedback` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `feedback`
--

INSERT INTO `feedback` (`id`, `id_mission`, `id_utilisateur`, `rating`, `commentaire`, `date_feedback`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 4, 'feedback test', '2025-11-26 19:25:21', '2025-11-26 19:25:21', '2025-11-27 15:53:42'),
(3, 5, 1, 3, 'test', '2025-12-11 16:15:35', '2025-12-11 16:15:35', '2025-12-11 16:15:35'),
(4, 5, 4, 5, 'great!', '2025-12-14 22:35:54', '2025-12-14 22:35:54', '2025-12-14 22:35:54');

-- --------------------------------------------------------

--
-- Structure de la table `historique`
--

CREATE TABLE `historique` (
  `id_historique` int(11) NOT NULL,
  `id_util` int(11) NOT NULL,
  `id_quiz` int(11) NOT NULL,
  `date_tentative` datetime DEFAULT current_timestamp(),
  `score` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `historique`
--

INSERT INTO `historique` (`id_historique`, `id_util`, `id_quiz`, `date_tentative`, `score`) VALUES
(1, 4, 4, '2025-12-13 15:19:56', 1),
(2, 4, 4, '2025-12-13 15:47:08', 2),
(3, 4, 4, '2025-12-14 14:18:17', 10);

-- --------------------------------------------------------

--
-- Structure de la table `item_comments`
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
-- Structure de la table `likes_missions`
--

CREATE TABLE `likes_missions` (
  `id` int(11) NOT NULL,
  `mission_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `date_like` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `likes_missions`
--

INSERT INTO `likes_missions` (`id`, `mission_id`, `utilisateur_id`, `date_like`) VALUES
(1, 5, 4, '2025-12-09 10:51:52');

-- --------------------------------------------------------

--
-- Structure de la table `missions`
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

--
-- Déchargement des données de la table `missions`
--

INSERT INTO `missions` (`id`, `titre`, `jeu`, `theme`, `niveau_difficulte`, `description`, `competences_requises`, `salaire_propose`, `duree_estimee`, `nombre_places`, `date_creation`, `date_debut`, `date_fin`, `statut`, `createur_id`, `partenaire_id`) VALUES
(1, 'Raid Boss Expert Team', 'God of War Ragnarok', 'Combat', 'Expert', 'Nous cherchons des joueurs experts pour un raid coordonne sur les boss difficiles. Excellent teamwork requis.', 'Combat avance, Communication en equipe, Coordination', 150.00, '5-7 heures', 4, '2025-11-26 17:31:09', '2025-11-23', '2025-12-23', 'Ouverte', 1, NULL),
(2, 'Speedrun Coordinateur', 'Counter-Strike 2', 'Competition', 'Difficile', 'Besoin d un coordinateur pour organiser et diriger un speedrun du jeu. Experience en leadership requise.', 'Leadership, Connaissance CS2, Streaming', 200.00, '10 heures', 1, '2025-11-26 17:31:09', '2025-11-23', '2025-12-13', 'Ouverte', 1, NULL),
(3, 'Testeur Qualite Produit', 'Fortnite Battle Royale', 'Testing', 'Moyen', 'Testez les nouvelles fonctionnalites et reportez les bugs avant la sortie publique.', 'Attention aux details, Rapport de bug, Gameplay solide', 100.00, '8 heures/semaine', 3, '2025-11-26 17:31:09', '2025-11-23', '2026-01-22', 'Ouverte', 1, NULL),
(4, 'Guide Strategie Debutant', 'Dota 2', 'Enseignement', 'Facile', 'Creer des guides strategiques pour les nouveaux joueurs de Dota 2.', 'Pedagogie, Connaissance Dota 2', 80.00, '6 heures', 2, '2025-11-26 17:31:09', '2025-11-23', '2025-12-08', 'En cours', 1, NULL),
(5, 'Event Moderateur', 'Minecraft', 'Evenement', 'Moyen', 'Moderez un tournoi Minecraft de 2 jours avec plus de 100 participants.', 'Moderation, Impartialite, Connaissance Minecraft', 250.00, '16 heures', 3, '2025-11-26 17:31:09', '2025-11-28', '2025-12-28', 'Ouverte', 1, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `orders`
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

--
-- Déchargement des données de la table `orders`
--

INSERT INTO `orders` (`id`, `utilisateur_id`, `name`, `email`, `phone`, `address`, `city`, `shipping`, `total`, `status`, `created_at`, `updated_at`) VALUES
(7, 4, 'testazda', 'sghaiersana069@gmail.com', '24343010', 'azdadzazd', 'adzfazdazd', 'standard', 119.98, 'pending', '2025-11-30 15:04:34', '2025-11-30 15:04:34');

-- --------------------------------------------------------

--
-- Structure de la table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `item_id`, `name`, `price`, `qty`) VALUES
(1, 7, 2, 'Halo Infinite', 59.99, 2);

-- --------------------------------------------------------

--
-- Structure de la table `partenaires`
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

--
-- Déchargement des données de la table `partenaires`
--

INSERT INTO `partenaires` (`id`, `nom`, `logo`, `type`, `statut`, `description`, `email`, `telephone`, `site_web`, `created_by`, `created_at`, `updated_at`, `rating_sum`, `rating_count`, `rating_avg`) VALUES
(2, 'TestGames Inc', 'view/frontoffice/assets/uploads/logos/logo_testgames.jpg', 'testeur', 'actif', 'Entreprise de test de jeux vidéo avec une équipe expérimentée', 'info@testgames.com', '+33123456790', 'https://www.testgames.com', 1, '2025-11-27 16:41:50', '2025-11-27 16:41:50', 0, 0, NULL),
(3, 'GameStore Online', 'view/frontoffice/assets/uploads/logos/logo_gamestore.jpg', 'vendeur', 'actif', 'Boutique en ligne de jeux vidéo avec large sélection', 'sales@gamestore.com', '+33123456791', 'https://www.gamestore-online.com', 1, '2025-11-27 16:41:50', '2025-11-27 16:41:50', 0, 0, NULL),
(4, 'Esports Arena', 'view/frontoffice/assets/uploads/logos/logo_esports.jpg', 'sponsor', 'actif', 'Organisateur d événements esport et tournois gaming', 'events@esportsarena.com', '+33123456792', 'https://www.esports-arena.com', 1, '2025-11-27 16:41:50', '2025-11-27 16:41:50', 0, 0, NULL),
(5, 'TechGaming Labs', 'view/frontoffice/assets/uploads/logos/logo_techgaming.jpg', 'testeur', 'actif', 'Laboratoire de test hardware et software gaming', 'labs@techgaming.com', '+33123456793', 'https://www.techgaminglabs.com', 1, '2025-11-27 16:41:50', '2025-11-27 16:41:50', 0, 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `participation`
--

CREATE TABLE `participation` (
  `id_participation` int(11) NOT NULL,
  `id_evenement` int(11) NOT NULL,
  `id_volontaire` int(11) NOT NULL,
  `date_participation` date DEFAULT curdate(),
  `statut` enum('en attente','acceptée','refusée') DEFAULT 'en attente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `quantite` int(11) DEFAULT 1,
  `montant_total` decimal(10,2) DEFAULT NULL,
  `mode_paiement` varchar(100) DEFAULT NULL,
  `reference_paiement` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `participation`
--

INSERT INTO `participation` (`id_participation`, `id_evenement`, `id_volontaire`, `date_participation`, `statut`, `created_at`, `quantite`, `montant_total`, `mode_paiement`, `reference_paiement`) VALUES
(1, 2, 4, '2025-12-14', 'en attente', '2025-12-14 19:02:18', 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `quiz`
--

CREATE TABLE `quiz` (
  `id_quiz` int(11) NOT NULL,
  `question` text NOT NULL,
  `reponse1` varchar(255) NOT NULL,
  `reponse2` varchar(255) NOT NULL,
  `reponse3` varchar(255) NOT NULL,
  `bonne_reponse` varchar(10) NOT NULL,
  `id_article` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `quiz`
--

INSERT INTO `quiz` (`id_quiz`, `question`, `reponse1`, `reponse2`, `reponse3`, `bonne_reponse`, `id_article`) VALUES
(1, 'Pourquoi est-il important de faire du sport régulièrement ?', 'Pour s’ennuyer', 'Pour rester en bonne santé', 'Pour regarder la télévision', '2', 4),
(3, 'Quelle habitude aide à renforcer le corps et prévenir les maladies ?', 'Manger équilibré', 'Rester assis toute la journée', 'Dormir très peu', '1', 4),
(4, 'Le sommeil contribue à', 'Améliorer la santé mentale et physique', 'Ralentir le corps', 'Rien du tout', '1', 4),
(5, 'Pourquoi aller à l’école est important ?', 'Pour apprendre et grandir', 'Pour ne rien faire', 'Pour dormir', '1', 5);

-- --------------------------------------------------------

--
-- Structure de la table `reclamation`
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `category` varchar(50) DEFAULT 'general',
  `department` varchar(100) DEFAULT 'General Support'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `reclamation`
--

INSERT INTO `reclamation` (`id`, `sujet`, `description`, `email`, `date_creation`, `statut`, `reponse`, `utilisateur_id`, `product_id`, `priorite`, `updated_at`, `category`, `department`) VALUES
(2, 'ileeeeeeeeeef', '', 'ileeeeeeeeeef@gmail.com', '2025-11-26 17:16:51', 'Traite', NULL, 4, NULL, 'Moyenne', '2025-11-27 15:47:42', 'general', 'General Support'),
(3, 'ileeeeeeeeeef', '', 'ileeeeeeeeeef@gmail.com', '2025-11-26 17:16:53', 'Traite', NULL, 4, NULL, 'Moyenne', '2025-11-30 14:02:45', 'general', 'General Support'),
(6, 'Réclamation test', 'c une réclamation de test', 'mahmoudmkadeem2005@gmail.com', '2025-12-02 09:11:48', 'Traite', NULL, 4, NULL, 'Moyenne', '2025-12-02 09:32:02', 'general', 'General Support'),
(8, 'j\'ai un probleme test', 'reclamation de test de l\'email \r\n', 'Mahmoud.Mkaddem@esprit.tn', '2025-12-08 23:50:37', 'Traite', NULL, 4, NULL, 'Moyenne', '2025-12-08 23:51:17', 'general', 'General Support'),
(9, 'test', 'azd', 'sghaiersana069@gmail.com', '2025-12-09 00:23:37', 'Traite', NULL, 4, NULL, 'Moyenne', '2025-12-09 00:44:41', 'general', 'General Support'),
(10, 'j\'ai un probleme test', 'ervgzarefazerfcazecfc', 'sghaiersana069@gmail.com', '2025-12-11 17:52:32', 'Non traite', NULL, 1, NULL, 'Moyenne', '2025-12-11 17:52:32', 'general', 'General Support'),
(11, 'j\'ai un probleme test', 'ervgzarefazerfcazecfc', 'sghaiersana069@gmail.com', '2025-12-11 17:54:34', 'Non traite', NULL, 1, NULL, 'Moyenne', '2025-12-11 17:54:34', 'general', 'General Support'),
(12, 'azdazd', 'kkkk', 'azdazd@azd.com', '2025-12-11 17:55:00', 'Non traite', NULL, 1, NULL, 'Moyenne', '2025-12-11 17:55:00', 'general', 'General Support'),
(14, '[Mission] j\'ai un probleme test', 'Type: Mission | Mission #2 (Speedrun Coordinateur)\n\nkazndjazdjanzd', 'Mahmoud.Mkaddem@esprit.tn', '2025-12-14 16:54:28', 'Non traite', NULL, 1, NULL, 'Moyenne', '2025-12-14 16:54:28', 'general', 'General Support'),
(15, '[Autre] azdazd', 'Type: Autre\n\nkkkk', 'azdazd@azd.com', '2025-12-14 18:39:10', 'Non traite', NULL, 1, NULL, 'Moyenne', '2025-12-14 18:39:10', 'general', 'General Support'),
(17, '[Partenaire] azdazd', 'Type: Partenaire | Partenaire #4 (Esports Arena)\n\nazdazd', 'admin@engage.com', '2025-12-15 16:56:19', '', NULL, 1, NULL, 'Moyenne', '2025-12-15 16:56:34', 'general', 'General Support'),
(18, '[Partenaire] azdazd', 'Type: Partenaire | Partenaire #4 (Esports Arena)\n\nazdazd', 'admin@engage.com', '2025-12-15 16:56:42', 'Non traite', NULL, 1, NULL, 'Moyenne', '2025-12-15 16:56:42', 'general', 'General Support'),
(19, '[Partenaire] azdazd', 'Type: Partenaire | Partenaire #4 (Esports Arena)\n\nazdazd', 'admin@engage.com', '2025-12-15 16:56:43', 'Non traite', NULL, 1, NULL, 'Moyenne', '2025-12-15 16:56:43', 'general', 'General Support'),
(20, '[Partenaire] azdazd', 'Type: Partenaire | Partenaire #4 (Esports Arena)\n\nazdazd', 'admin@engage.com', '2025-12-15 17:00:39', 'Non traite', NULL, 1, NULL, 'Moyenne', '2025-12-15 17:00:39', 'general', 'General Support'),
(22, '[Partenaire] azdazd', 'Type: Partenaire | Partenaire #5 (TechGaming Labs)\n\nazdazd', 'admin@engage.com', '2025-12-15 17:01:24', 'Non traite', NULL, 1, NULL, 'Moyenne', '2025-12-15 17:01:24', 'general', 'General Support'),
(23, '[Partenaire] azdazd', 'Type: Partenaire | Partenaire #5 (TechGaming Labs)\n\nazdazd', 'admin@engage.com', '2025-12-15 17:01:25', 'Non traite', NULL, 1, NULL, 'Moyenne', '2025-12-15 17:01:25', 'general', 'General Support');

-- --------------------------------------------------------

--
-- Structure de la table `response`
--

CREATE TABLE `response` (
  `id` int(11) NOT NULL,
  `reclamation_id` int(11) NOT NULL,
  `contenu` longtext NOT NULL,
  `date_response` timestamp NOT NULL DEFAULT current_timestamp(),
  `vu` tinyint(1) NOT NULL DEFAULT 0,
  `admin_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `response`
--

INSERT INTO `response` (`id`, `reclamation_id`, `contenu`, `date_response`, `vu`, `admin_id`) VALUES
(2, 2, 'azdazdazdazdazd', '2025-11-27 15:47:42', 1, 4),
(3, 3, 'azdazd', '2025-11-30 14:02:45', 1, 4),
(4, 6, 'reponse test', '2025-12-02 09:32:02', 1, 4),
(5, 6, 'test compte admin', '2025-12-08 20:18:41', 1, 1),
(7, 8, 'reponse test de recu email', '2025-12-08 23:51:17', 1, 1),
(8, 8, 'recu test', '2025-12-09 00:15:08', 1, 1),
(9, 8, 'azdazdazd', '2025-12-09 00:18:26', 1, 1),
(10, 8, 'azdazdazd', '2025-12-09 00:18:33', 1, 1),
(11, 8, 'azdazdazd', '2025-12-09 00:18:37', 1, 1),
(12, 8, 'dzzazdazd', '2025-12-09 00:21:00', 1, 1),
(13, 3, 'azdazdazdazd', '2025-12-09 00:22:07', 1, 4),
(14, 3, 'azdazdazdazd', '2025-12-09 00:22:10', 1, 4),
(15, 8, 'pssss', '2025-12-09 00:27:53', 1, 1),
(16, 9, 'az', '2025-12-09 00:44:41', 1, 1),
(17, 8, 'a', '2025-12-09 00:44:48', 1, 1),
(18, 8, 'a', '2025-12-09 00:44:51', 1, 1),
(19, 8, 'a', '2025-12-09 00:44:54', 1, 1),
(20, 8, 'a', '2025-12-09 00:44:56', 1, 1),
(21, 8, 'a', '2025-12-09 00:44:59', 1, 1),
(22, 8, 'a', '2025-12-09 00:45:02', 1, 1),
(23, 8, 'a', '2025-12-09 00:45:05', 1, 1),
(24, 8, 'une autre reponse de test', '2025-12-09 09:24:38', 1, 1),
(25, 8, 'repondre test', '2025-12-09 09:52:36', 1, 1),
(26, 8, 'aazd', '2025-12-09 10:52:55', 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `store_items`
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

--
-- Déchargement des données de la table `store_items`
--

INSERT INTO `store_items` (`id`, `partenaire_id`, `nom`, `prix`, `stock`, `categorie`, `image`, `description`, `plateforme`, `age_minimum`, `likes_count`, `views_count`, `created_by`, `created_at`, `updated_at`, `rating_sum`, `rating_count`, `rating_avg`) VALUES
(2, 2, 'Halo Infinite', 59.99, 10, 'Shooter', 'view/frontoffice/assets/uploads/games/halo_infinite.jpg', 'Le retour du légendaire Halo avec un multijoueur révolutionnaire.', 'Xbox Series X/S', 16, 0, 1, 1, '2025-11-27 16:41:50', '2025-11-30 14:31:13', 1, 1, 1.00),
(3, 3, 'Counter-Strike 2', 0.00, 9999, 'Shooter', 'view/frontoffice/assets/uploads/games/cs2.jpg', 'Le jeu de tir tactique compétitif qui a révolutionné l esport.', 'PC', 16, 1, 3, 1, '2025-11-27 16:41:50', '2025-12-14 11:30:49', 0, 0, NULL),
(4, 4, 'The Legend of Zelda: Tears of the Kingdom', 69.99, 5, 'Action-Aventure', 'view/frontoffice/assets/uploads/games/zelda_totk.jpg', 'La suite tant attendue de Breath of the Wild sur Nintendo Switch.', 'Nintendo Switch', 12, 0, 0, 1, '2025-11-27 16:41:50', '2025-11-27 16:41:50', 0, 0, NULL),
(5, 5, 'Mario Kart 8 Deluxe', 59.99, 8, 'Course', 'view/frontoffice/assets/uploads/games/mario_kart.jpg', 'Le jeu de course le plus populaire sur Nintendo Switch.', 'Nintendo Switch', 3, 0, 0, 1, '2025-11-27 16:41:50', '2025-11-27 16:41:50', 0, 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `user_bans`
--

CREATE TABLE `user_bans` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `banned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `img` varchar(255) DEFAULT NULL,
  `auth` varchar(200) NOT NULL,
  `face` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id_util`, `prenom`, `nom`, `dt_naiss`, `mail`, `num`, `mdp`, `typee`, `q1`, `rp1`, `q2`, `rp2`, `gamer_tag`, `created_at`, `updated_at`, `img`, `auth`) VALUES
(1, 'Admin', 'System', '1990-01-01', 'admin@engage.com', 600000001, 'azerty', 'admin', 'Quelle est votre couleur preferee?', 'Bleu', 'Quel est votre animal prefere?', 'Chat', 'AdminGamer', '2025-11-23 14:45:53', '2025-12-15 19:51:31', 'profile_1_1765827104.png', ''),
(2, 'Jean', 'Dupont', '1995-05-15', 'jean.dupont@email.com', 612345678, '112233', 'user', 'Quelle est votre couleur preferee?', 'Rouge', 'Quel est votre animal prefere?', 'Chien', 'ProGamer92', '2025-11-23 14:45:53', '2025-12-15 19:45:38', NULL, ''),
(3, 'Marie', 'Martin', '1992-03-20', 'marie.martin@email.com', 623456789, 'password123', 'user', 'Quelle est votre couleur preferee?', 'Vert', 'Quel est votre animal prefere?', 'Oiseau', 'ShadowKnight', '2025-11-23 14:45:53', '2025-11-23 14:45:53', NULL, ''),
(4, 'maha', 'mkaddem', '2005-12-09', 'Mahmoud.Mkaddem@esprit.tn', 24343010, 'hamahama200', '', NULL, NULL, NULL, NULL, NULL, '2025-11-26 16:59:26', '2025-11-26 16:59:26', NULL, ''),
(5, 'eya', 'aziz', '2002-11-12', 'eya@gmail.com', 44557744, 'azerty', 'user', NULL, NULL, NULL, NULL, NULL, '2025-12-15 23:02:21', '2025-12-15 23:02:21', NULL, '');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `article`
--
ALTER TABLE `article`
  ADD PRIMARY KEY (`id_article`);

--
-- Index pour la table `bad_words`
--
ALTER TABLE `bad_words`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_word` (`word`);

--
-- Index pour la table `candidatures`
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
-- Index pour la table `evenement`
--
ALTER TABLE `evenement`
  ADD PRIMARY KEY (`id_evenement`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_date` (`date_evenement`),
  ADD KEY `idx_organisation` (`id_organisation`);

--
-- Index pour la table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_mission_feedback` (`id_mission`,`id_utilisateur`),
  ADD KEY `idx_mission` (`id_mission`),
  ADD KEY `idx_utilisateur` (`id_utilisateur`),
  ADD KEY `idx_rating` (`rating`),
  ADD KEY `idx_date_feedback` (`date_feedback`);

--
-- Index pour la table `historique`
--
ALTER TABLE `historique`
  ADD PRIMARY KEY (`id_historique`),
  ADD KEY `idx_hist_util` (`id_util`),
  ADD KEY `idx_hist_quiz` (`id_quiz`);

--
-- Index pour la table `item_comments`
--
ALTER TABLE `item_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_item` (`store_item_id`),
  ADD KEY `idx_user` (`utilisateur_id`),
  ADD KEY `idx_status` (`status`);

--
-- Index pour la table `likes_missions`
--
ALTER TABLE `likes_missions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`mission_id`,`utilisateur_id`),
  ADD KEY `fk_like_mission` (`mission_id`),
  ADD KEY `fk_like_utilisateur` (`utilisateur_id`);

--
-- Index pour la table `missions`
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
-- Index pour la table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`utilisateur_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Index pour la table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order` (`order_id`),
  ADD KEY `idx_item` (`item_id`);

--
-- Index pour la table `partenaires`
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
-- Index pour la table `participation`
--
ALTER TABLE `participation`
  ADD PRIMARY KEY (`id_participation`),
  ADD UNIQUE KEY `unique_participation` (`id_evenement`,`id_volontaire`),
  ADD KEY `fk_participation_evenement` (`id_evenement`),
  ADD KEY `fk_participation_volontaire` (`id_volontaire`);

--
-- Index pour la table `quiz`
--
ALTER TABLE `quiz`
  ADD PRIMARY KEY (`id_quiz`),
  ADD KEY `quiz_article_fk` (`id_article`);

--
-- Index pour la table `reclamation`
--
ALTER TABLE `reclamation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_statut` (`statut`),
  ADD KEY `idx_priorite` (`priorite`),
  ADD KEY `idx_date_creation` (`date_creation`),
  ADD KEY `idx_utilisateur_id` (`utilisateur_id`),
  ADD KEY `idx_reclamation_category` (`category`),
  ADD KEY `idx_reclamation_priority` (`priorite`);
ALTER TABLE `reclamation` ADD FULLTEXT KEY `ft_sujet_description` (`sujet`,`description`);

--
-- Index pour la table `response`
--
ALTER TABLE `response`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reclamation_id` (`reclamation_id`),
  ADD KEY `idx_admin_id` (`admin_id`),
  ADD KEY `idx_date_response` (`date_response`);

--
-- Index pour la table `store_items`
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
-- Index pour la table `user_bans`
--
ALTER TABLE `user_bans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ban_utilisateur` (`utilisateur_id`),
  ADD KEY `idx_expires_at` (`expires_at`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id_util`),
  ADD UNIQUE KEY `unique_mail` (`mail`),
  ADD UNIQUE KEY `unique_gamer_tag` (`gamer_tag`),
  ADD KEY `idx_typee` (`typee`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `article`
--
ALTER TABLE `article`
  MODIFY `id_article` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `bad_words`
--
ALTER TABLE `bad_words`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=409;

--
-- AUTO_INCREMENT pour la table `candidatures`
--
ALTER TABLE `candidatures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `evenement`
--
ALTER TABLE `evenement`
  MODIFY `id_evenement` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `historique`
--
ALTER TABLE `historique`
  MODIFY `id_historique` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `item_comments`
--
ALTER TABLE `item_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `likes_missions`
--
ALTER TABLE `likes_missions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `missions`
--
ALTER TABLE `missions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `partenaires`
--
ALTER TABLE `partenaires`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `participation`
--
ALTER TABLE `participation`
  MODIFY `id_participation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `quiz`
--
ALTER TABLE `quiz`
  MODIFY `id_quiz` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `reclamation`
--
ALTER TABLE `reclamation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT pour la table `response`
--
ALTER TABLE `response`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT pour la table `store_items`
--
ALTER TABLE `store_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `user_bans`
--
ALTER TABLE `user_bans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id_util` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `candidatures`
--
ALTER TABLE `candidatures`
  ADD CONSTRAINT `candidatures_ibfk_1` FOREIGN KEY (`mission_id`) REFERENCES `missions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `candidatures_ibfk_2` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id_util`) ON DELETE CASCADE;

--
-- Contraintes pour la table `evenement`
--
ALTER TABLE `evenement`
  ADD CONSTRAINT `evenement_ibfk_1` FOREIGN KEY (`id_organisation`) REFERENCES `utilisateur` (`id_util`) ON DELETE CASCADE,
  ADD CONSTRAINT `evenement_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `utilisateur` (`id_util`) ON DELETE CASCADE;

--
-- Contraintes pour la table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`id_mission`) REFERENCES `missions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_util`) ON DELETE CASCADE;

--
-- Contraintes pour la table `historique`
--
ALTER TABLE `historique`
  ADD CONSTRAINT `historique_quiz_fk` FOREIGN KEY (`id_quiz`) REFERENCES `quiz` (`id_quiz`) ON DELETE CASCADE,
  ADD CONSTRAINT `historique_util_fk` FOREIGN KEY (`id_util`) REFERENCES `utilisateur` (`id_util`) ON DELETE CASCADE;

--
-- Contraintes pour la table `item_comments`
--
ALTER TABLE `item_comments`
  ADD CONSTRAINT `item_comments_ibfk_1` FOREIGN KEY (`store_item_id`) REFERENCES `store_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_comments_ibfk_2` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id_util`) ON DELETE CASCADE;

--
-- Contraintes pour la table `missions`
--
ALTER TABLE `missions`
  ADD CONSTRAINT `missions_ibfk_1` FOREIGN KEY (`createur_id`) REFERENCES `utilisateur` (`id_util`) ON DELETE CASCADE,
  ADD CONSTRAINT `missions_ibfk_2` FOREIGN KEY (`partenaire_id`) REFERENCES `partenaires` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id_util`) ON DELETE CASCADE;

--
-- Contraintes pour la table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `store_items` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `partenaires`
--
ALTER TABLE `partenaires`
  ADD CONSTRAINT `partenaires_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `utilisateur` (`id_util`) ON DELETE SET NULL;

--
-- Contraintes pour la table `participation`
--
ALTER TABLE `participation`
  ADD CONSTRAINT `participation_ibfk_1` FOREIGN KEY (`id_evenement`) REFERENCES `evenement` (`id_evenement`) ON DELETE CASCADE,
  ADD CONSTRAINT `participation_ibfk_2` FOREIGN KEY (`id_volontaire`) REFERENCES `utilisateur` (`id_util`) ON DELETE CASCADE;

--
-- Contraintes pour la table `quiz`
--
ALTER TABLE `quiz`
  ADD CONSTRAINT `quiz_article_fk` FOREIGN KEY (`id_article`) REFERENCES `article` (`id_article`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reclamation`
--
ALTER TABLE `reclamation`
  ADD CONSTRAINT `reclamation_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id_util`) ON DELETE CASCADE,
  ADD CONSTRAINT `reclamation_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `store_items` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `response`
--
ALTER TABLE `response`
  ADD CONSTRAINT `response_ibfk_1` FOREIGN KEY (`reclamation_id`) REFERENCES `reclamation` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `response_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `utilisateur` (`id_util`) ON DELETE CASCADE;

--
-- Contraintes pour la table `store_items`
--
ALTER TABLE `store_items`
  ADD CONSTRAINT `store_items_ibfk_1` FOREIGN KEY (`partenaire_id`) REFERENCES `partenaires` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `store_items_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `utilisateur` (`id_util`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
