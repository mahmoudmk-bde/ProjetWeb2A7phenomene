-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : dim. 14 déc. 2025 à 19:53
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
(1, 'merde', '2025-12-09 00:32:47'),
(2, 'con', '2025-12-09 00:32:47'),
(3, 'connard', '2025-12-09 00:32:47'),
(4, 'salope', '2025-12-09 00:32:47'),
(5, 'pute', '2025-12-09 00:32:47'),
(6, 'fuck', '2025-12-09 00:32:47'),
(7, 'shit', '2025-12-09 00:32:47'),
(8, 'damn', '2025-12-09 00:32:47'),
(9, 'idiot', '2025-12-09 00:32:47'),
(10, 'stupide', '2025-12-09 00:32:47');

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
  `cv` varchar(255) DEFAULT NULL,
  `statut` enum('en_attente','acceptee','rejetee','annulee') DEFAULT 'en_attente',
  `message_candidature` longtext DEFAULT NULL,
  `date_candidature` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_reponse` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `candidatures`
--

INSERT INTO `candidatures` (`id`, `mission_id`, `utilisateur_id`, `pseudo_gaming`, `niveau_experience`, `disponibilites`, `email`, `cv`, `statut`, `message_candidature`, `date_candidature`, `date_reponse`) VALUES
(1, 1, 2, 'ProGamer92', 'Avance', 'Lundi à vendredi, 18h-22h', 'jean.dupont@email.com', NULL, 'en_attente', 'Je joue à Fortnite depuis 3 ans, j\'ai déjà participé à des tests beta. Très motivé pour cette mission !', '2025-12-01 20:05:07', NULL),
(2, 1, 3, 'ShadowKnight', 'Intermediaire', 'Week-ends et soirées', 'marie.martin@email.com', NULL, 'acceptee', 'Passionnée de Fortnite, je serais ravie de contribuer à l\'amélioration du jeu.', '2025-12-01 20:05:07', NULL),
(3, 3, 2, 'ProGamer92', 'Expert', 'Tous les jours 20h-23h', 'jean.dupont@email.com', NULL, 'en_attente', 'Coach expérimenté en Valorant, j\'ai déjà aidé plusieurs équipes à monter en rank.', '2025-12-01 20:05:07', NULL),
(4, 2, 3, 'ShadowKnight', 'Debutant', 'Mercredi et samedi', 'marie.martin@email.com', NULL, 'rejetee', 'Je débute en développement mais je suis très motivée pour apprendre.', '2025-12-01 20:05:07', NULL),
(6, 1, 5, 'Gamer_5', 'Intermediaire', 'À définir', 'eyakh@gmail.com', NULL, 'en_attente', NULL, '2025-12-02 09:14:40', NULL),
(7, 3, 5, 'Gamer_5', 'Intermediaire', 'À définir', 'eyakh@gmail.com', NULL, 'en_attente', NULL, '2025-12-02 09:15:13', NULL),
(8, 1, 4, 'Gamer_4', 'Intermediaire', 'À définir', 'ileeygh@esprit.tn', NULL, 'en_attente', NULL, '2025-12-02 09:16:28', NULL),
(9, 4, 4, 'Gamer_4', 'Intermediaire', 'À définir', 'ileeygh@esprit.tn', NULL, 'en_attente', NULL, '2025-12-02 09:19:56', NULL),
(10, 3, 4, 'Gamer_4', 'Intermediaire', 'À définir', 'ileeygh@esprit.tn', NULL, 'en_attente', NULL, '2025-12-02 09:35:28', NULL),
(11, 5, 4, 'Gamer_4', 'Intermediaire', 'À définir', 'ileeygh@esprit.tn', NULL, 'en_attente', NULL, '2025-12-02 10:25:17', NULL),
(12, 5, 7, 'valorant', 'Debutant', '2H/matin', 'eyaaziz@gmail.com', NULL, 'acceptee', NULL, '2025-12-08 21:31:36', NULL),
(13, 7, 7, 'ileey', 'Expert', '2H/matin', 'eyaaziz@gmail.com', NULL, 'en_attente', NULL, '2025-12-08 22:44:40', NULL),
(14, 1, 7, 'mymy', 'Debutant', 'tout jour', 'eyaaziz@gmail.com', 'assets/uploads/cv/1765235146_388f4548d7a5.pdf', 'en_attente', NULL, '2025-12-08 23:05:46', NULL),
(15, 2, 8, 'zizou', 'Debutant', 'toujours', 'test1@gmil.com', 'assets/uploads/cv/1765236816_6c17d78dfded.pdf', 'acceptee', NULL, '2025-12-08 23:33:36', '2025-12-13 16:23:44'),
(20, 10, 6, 'ileey', 'Debutant', 'toujours', 'Ilef.Ghanmi@esprit.tn', 'assets/uploads/cv/1765656169_8db812b42e38.pdf', 'en_attente', NULL, '2025-12-13 20:02:49', NULL),
(21, 9, 6, 'ileef1', 'Avance', '2H/matin', 'Ilef.Ghanmi@esprit.tn', 'assets/uploads/cv/1765656969_72c9505eb0be.pdf', 'en_attente', NULL, '2025-12-13 20:16:09', NULL),
(22, 7, 1, 'admin', 'Avance', 'toujours', 'admin@engage.com', 'assets/uploads/cv/1765658470_b28a2fde18c2.pdf', 'en_attente', NULL, '2025-12-13 20:41:10', NULL);

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 1, 1, 3, 'ileey', '2025-12-02 08:41:34', '2025-12-02 08:41:34', '2025-12-02 08:41:34'),
(2, 1, 4, 2, '', '2025-12-02 09:11:40', '2025-12-02 09:11:40', '2025-12-02 09:11:40'),
(3, 1, 5, 3, 'tress bien', '2025-12-02 09:14:07', '2025-12-02 09:14:07', '2025-12-02 09:14:07'),
(4, 5, 1, 4, 'waaaaw', '2025-12-08 22:24:10', '2025-12-08 22:24:10', '2025-12-08 22:24:10');

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
(1, 7, 1, '2025-12-09 00:10:58'),
(2, 10, 7, '2025-12-13 17:36:58');

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
(1, 'Testeur de jeu Alpha - Fortnite', 'Fortnite', 'valorant', 'Facile', 'Nous recherchons des testeurs pour la nouvelle saison de Fortnite. Vous testerez les nouvelles armes, cartes et mécaniques de jeu.', 'Bonnes connaissances de Fortnite, capacité à décrire les bugs, sérieux', 500.00, '2 semaines', 10, '2025-12-01 20:03:51', '2025-12-01', '2025-12-15', 'Ouverte', 1, NULL),
(2, 'Développeur de mods - Minecraft', 'Minecraft', 'Création', 'Moyen', 'Création de mods pour ajouter de nouvelles fonctionnalités au jeu. Idéal pour les développeurs passionnés.', 'Java, JSON, expérience avec Minecraft Forge', 1200.50, '1 mois', 5, '2025-12-01 20:03:51', '2025-12-10', '2026-01-10', 'Ouverte', 2, NULL),
(3, 'Coach eSport - Valorant', 'Valorant', 'esport', 'Difficile', 'Coach pour équipe amateur souhaitant progresser en compétition. Analyse de parties et stratégies.', 'Rank Diamant+, expérience en coaching, bonne communication', 800.00, '3 mois', 3, '2025-12-01 20:03:51', '2025-12-05', '2026-03-05', 'Ouverte', 3, NULL),
(4, 'Traducteur - RPG Fantasy', 'Skyrim', 'roblex', '', 'Traduction de quêtes et dialogues du français vers l\'anglais pour un mod communautaire.', 'Bilingue français/anglais, connaissance des jeux RPG', 300.75, '2 semaines', 8, '2025-12-01 20:03:51', '2025-11-30', '2025-12-14', 'Fermee', 1, NULL),
(5, 'Graphiste - Conception de skins', 'League of Legends', 'education', 'Expert', 'Création de skins originaux pour champions de LoL. Portfolio exigé.', 'Photoshop, Illustrator, connaissance de l\'univers LoL', 1500.00, '6 semaines', 2, '2025-12-01 20:03:51', '2025-12-15', '2026-01-26', 'Ouverte', 2, NULL),
(7, 'mission test', 'fifa', 'sport', 'Moyen', 'une bonne mission', 'motivé', NULL, NULL, 1, '2025-12-08 22:40:08', '2025-12-08', '2025-12-31', 'Ouverte', 1, NULL),
(9, 'voyage', 'valorant', 'education', 'Facile', 'une bonne mission', 'un expert', NULL, NULL, 1, '2025-12-13 16:46:19', '2025-12-20', '2025-12-27', 'Ouverte', 1, NULL),
(10, 'esprit', 'friv', 'education', 'Difficile', 'une mission interresnte', 'un debutant', NULL, NULL, 1, '2025-12-13 17:10:36', '2025-12-14', '2025-12-25', 'Ouverte', 1, NULL);

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `response`
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

--
-- Déchargement des données de la table `user_bans`
--

INSERT INTO `user_bans` (`id`, `utilisateur_id`, `reason`, `banned_at`, `expires_at`, `is_active`) VALUES
(1, 6, 'Commentaire contenant des mots interdits', '2025-12-09 00:32:47', '2025-12-12 01:32:47', 1);

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
  `img` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id_util`, `prenom`, `nom`, `dt_naiss`, `mail`, `num`, `mdp`, `typee`, `q1`, `rp1`, `q2`, `rp2`, `gamer_tag`, `created_at`, `updated_at`, `img`) VALUES
(1, 'Admin', 'System', '1990-01-01', 'admin@engage.com', 600000001, 'admin123', 'admin', 'Quelle est votre couleur preferee?', 'Bleu', 'Quel est votre animal prefere?', 'Chat', 'AdminGamer', '2025-11-23 13:45:53', '2025-11-23 13:45:53', NULL),
(2, 'Jean', 'Dupont', '1995-05-15', 'jean.dupont@email.com', 612345678, 'azerty', 'user', 'Quelle est votre couleur preferee?', 'Rouge', 'Quel est votre animal prefere?', 'Chien', 'ProGamer92', '2025-11-23 13:45:53', '2025-12-14 17:15:24', NULL),
(3, 'Marie', 'Martin', '1992-03-20', 'marie.martin@email.com', 623456789, 'password123', 'user', 'Quelle est votre couleur preferee?', 'Vert', 'Quel est votre animal prefere?', 'Oiseau', 'ShadowKnight', '2025-11-23 13:45:53', '2025-11-23 13:45:53', NULL),
(4, 'ileey', 'gh', '2004-03-22', 'ileeygh@esprit.tn', 22456763, 'test321', '', NULL, NULL, NULL, NULL, NULL, '2025-12-02 09:10:42', '2025-12-02 09:10:42', NULL),
(5, 'eya', 'kh', '2000-03-12', 'eyakh@gmail.com', 22345678, 'test1234', '', NULL, NULL, NULL, NULL, NULL, '2025-12-02 09:13:18', '2025-12-02 09:13:18', NULL),
(6, 'ilef', 'gh', '2005-06-27', 'Ilef.Ghanmi@esprit.tn', 90529900, 'test123', '', NULL, NULL, NULL, NULL, NULL, '2025-12-02 13:16:23', '2025-12-02 13:16:23', NULL),
(7, 'eya', 'aziz', '2000-02-22', 'eyaaziz@gmail.com', 23487955, 'test1234', '', NULL, NULL, NULL, NULL, NULL, '2025-12-08 21:05:09', '2025-12-08 21:05:09', NULL),
(8, 'test', 'ts', '2004-06-10', 'test1@gmil.com', 34567890, '123test', '', NULL, NULL, NULL, NULL, NULL, '2025-12-08 23:32:50', '2025-12-08 23:32:50', NULL),
(9, 'eya', 'kors', '2003-11-12', 'eya@gmail.com', 11443355, 'azerty', '', NULL, NULL, NULL, NULL, NULL, '2025-12-14 17:13:08', '2025-12-14 17:13:08', NULL);

--
-- Index pour les tables déchargées
--

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
-- Index pour la table `reclamation`
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
-- AUTO_INCREMENT pour la table `bad_words`
--
ALTER TABLE `bad_words`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `candidatures`
--
ALTER TABLE `candidatures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT pour la table `evenement`
--
ALTER TABLE `evenement`
  MODIFY `id_evenement` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `item_comments`
--
ALTER TABLE `item_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `likes_missions`
--
ALTER TABLE `likes_missions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `missions`
--
ALTER TABLE `missions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `partenaires`
--
ALTER TABLE `partenaires`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `participation`
--
ALTER TABLE `participation`
  MODIFY `id_participation` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `reclamation`
--
ALTER TABLE `reclamation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `response`
--
ALTER TABLE `response`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `store_items`
--
ALTER TABLE `store_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `user_bans`
--
ALTER TABLE `user_bans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id_util` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
