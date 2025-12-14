-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 09 déc. 2025 à 02:41
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `projetweb`
--

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
  `date_reponse` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `candidatures`
--

INSERT INTO `candidatures` (`id`, `mission_id`, `utilisateur_id`, `pseudo_gaming`, `niveau_experience`, `disponibilites`, `email`, `statut`, `message_candidature`, `date_candidature`, `date_reponse`) VALUES
(1, 1, 2, 'ProGamer92', 'Expert', 'Disponible soirs et weekends', 'jean.dupont@email.com', 'en_attente', 'Je suis tres interesse! J ai 5 ans d experience en raids coordonnes.', '2025-11-23 15:45:53', NULL),
(2, 1, 3, 'ShadowKnight', 'Avance', 'Flexible', 'marie.martin@email.com', 'acceptee', 'Confirme ma participation au raid boss!', '2025-11-23 15:45:53', NULL),
(3, 2, 2, 'CyberNinja', 'Avance', 'Soirs apres 20h', 'jean.dupont@email.com', 'en_attente', 'J ai de l experience en competition, j aimerais participer.', '2025-11-23 15:45:53', NULL),
(4, 3, 3, 'TestMaster', 'Expert', 'Flexible - travail remote', 'marie.martin@email.com', 'acceptee', 'J ai une experience extensive en QA testing.', '2025-11-23 15:45:53', NULL),
(5, 4, 2, 'LearningFocus', 'Debutant', 'Weekends', 'jean.dupont@email.com', 'rejetee', 'Je suis debutant et j aimerais apprendre Dota2.', '2025-11-23 15:45:53', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `evenement`
--

CREATE TABLE `evenement` (
  `id_evenement` int(11) NOT NULL,
  `titre` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `date_evenement` date NOT NULL,
  `heure_evenement` time DEFAULT NULL,
  `duree_minutes` int(11) DEFAULT NULL,
  `lieu` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `id_organisation` int(11) NOT NULL,
  `type_evenement` enum('gratuit','payant') NOT NULL DEFAULT 'gratuit',
  `prix` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `evenement`
--

INSERT INTO `evenement` (`id_evenement`, `titre`, `description`, `date_evenement`, `heure_evenement`, `duree_minutes`, `lieu`, `image`, `id_organisation`, `type_evenement`, `prix`) VALUES
(14, 'test', 'dddddddddddddddddddddddddddd', '2025-12-07', '11:15:00', NULL, 'barcelone', '/gamingroom/uploads/events/6924a77f607d8_téléchargement.jpg', 1, 'gratuit', NULL),
(15, 'GTA5', 'azdazdazdazdazdazd', '2025-12-07', NULL, NULL, 'azdazdazd', '/gamingroom/uploads/events/6924a81c8acf5_gta-5.jpg', 2, 'gratuit', NULL),
(16, 'sdqggsdfqfdv', 'dtshrehdthsghjxsjysjf', '2025-12-03', NULL, NULL, 'sdfqvqdfqv', '/gamingroom/uploads/events/69272a57af208_images.jpg', 1, 'payant', 34.00),
(17, 'FIFA26', 'Le Club est à toi. Jouez à EA SPORTS FC™ 26 dès maintenant, avec une expérience de jeu remaniée grâce aux retours de la communauté, des défis Manager Live qui apportent des scénarios inédits à la nouvelle saison, et des archétypes inspirés des stars du football.', '2025-12-07', '14:30:00', NULL, 'Tunis beb suoika', '/gamingroom/uploads/events/692827ed99da7_FIFAe-festival.jpg', 1, 'payant', 40.00),
(18, 'gejrj', 'lllllllllllllllll', '2026-01-01', '13:41:00', NULL, 'esprit', NULL, 1, 'payant', 10.00);

-- --------------------------------------------------------

--
-- Structure de la table `item_comments`
--

CREATE TABLE `item_comments` (
  `id` int(11) NOT NULL,
  `store_item_id` int(11) NOT NULL,
  `author_name` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `item_comments`
--

INSERT INTO `item_comments` (`id`, `store_item_id`, `author_name`, `content`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'GamersUnited', 'Excellent conclusion a la saga! Les graphismes sont epoustouflants.', 'approved', '2025-11-18 15:45:53', '2025-11-23 15:45:53'),
(2, 1, 'FantasyLover', 'Une histoire emouvante avec un gameplay epique. 10/10!', 'approved', '2025-11-20 15:45:53', '2025-11-23 15:45:53'),
(3, 3, 'ProPlayer', 'Halo Infinite est un excellent jeu, le multijoueur est vraiment amusant.', 'approved', '2025-11-16 15:45:53', '2025-11-23 15:45:53'),
(4, 5, 'EsportsDaily', 'CS2 reste le meilleur FPS competitif. Gratuit c est encore mieux!', 'approved', '2025-11-21 15:45:53', '2025-11-23 15:45:53'),
(5, 9, 'NintendoFan', 'Zelda TotK est un chef-d oeuvre! Aussi bon que BotW sinon mieux.', 'approved', '2025-11-22 15:45:53', '2025-11-23 15:45:53');

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
  `createur_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `missions`
--

INSERT INTO `missions` (`id`, `titre`, `jeu`, `theme`, `niveau_difficulte`, `description`, `competences_requises`, `salaire_propose`, `duree_estimee`, `nombre_places`, `date_creation`, `date_debut`, `date_fin`, `statut`, `createur_id`) VALUES
(1, 'Raid Boss Expert Team', 'God of War Ragnarok', 'Combat', 'Expert', 'Nous cherchons des joueurs experts pour un raid coordonne sur les boss difficiles. Excellent teamwork requis.', 'Combat avance, Communication en equipe, Coordination', 150.00, '5-7 heures', 4, '2025-11-23 15:45:53', '2025-11-23', '2025-12-23', 'Ouverte', 1),
(2, 'Speedrun Coordinateur', 'Counter-Strike 2', 'Competition', 'Difficile', 'Besoin d un coordinateur pour organiser et diriger un speedrun du jeu. Experience en leadership requise.', 'Leadership, Connaissance CS2, Streaming', 200.00, '10 heures', 1, '2025-11-23 15:45:53', '2025-11-23', '2025-12-13', 'Ouverte', 1),
(3, 'Testeur Qualite Produit', 'Fortnite Battle Royale', 'Testing', 'Moyen', 'Testez les nouvelles fonctionnalites et reportez les bugs avant la sortie publique.', 'Attention aux details, Rapport de bug, Gameplay solide', 100.00, '8 heures/semaine', 3, '2025-11-23 15:45:53', '2025-11-23', '2026-01-22', 'Ouverte', 1),
(4, 'Guide Strategie Debutant', 'Dota 2', 'Enseignement', 'Facile', 'Creer des guides strategiques pour les nouveaux joueurs de Dota 2.', 'Pedagogie, Connaissance Dota 2', 80.00, '6 heures', 2, '2025-11-23 15:45:53', '2025-11-23', '2025-12-08', 'En cours', 1),
(5, 'Event Moderateur', 'Minecraft', 'Evenement', 'Moyen', 'Moderez un tournoi Minecraft de 2 jours avec plus de 100 participants.', 'Moderation, Impartialite, Connaissance Minecraft', 250.00, '16 heures', 3, '2025-11-23 15:45:53', '2025-11-28', '2025-12-28', 'Ouverte', 1);

-- --------------------------------------------------------

--
-- Structure de la table `partenaires`
--

CREATE TABLE `partenaires` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `statut` enum('actif','inactif','suspendu') DEFAULT 'inactif',
  `description` text DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `site_web` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `partenaires`
--

INSERT INTO `partenaires` (`id`, `nom`, `logo`, `type`, `statut`, `description`, `email`, `telephone`, `site_web`, `created_at`, `updated_at`) VALUES
(1, 'PlayStation Store', 'https://example.com/ps-logo.png', 'Gaming', 'actif', 'Plateforme officielle de jeux PlayStation', 'contact@playstation.com', '+33123456789', 'https://store.playstation.com', '2025-11-23 15:45:53', '2025-11-23 15:45:53'),
(2, 'Xbox Game Pass', 'https://example.com/xbox-logo.png', 'Gaming', 'actif', 'Service d abonnement Xbox avec jeux illimites', 'contact@xbox.com', '+33123456790', 'https://www.xbox.com/gamepass', '2025-11-23 15:45:53', '2025-11-23 15:45:53'),
(3, 'Steam', 'https://example.com/steam-logo.png', 'Gaming', 'actif', 'Plateforme de distribution de jeux PC', 'contact@steampowered.com', '+33123456791', 'https://steampowered.com', '2025-11-23 15:45:53', '2025-11-23 15:45:53'),
(4, 'Epic Games', 'https://example.com/epic-logo.png', 'Gaming', 'actif', 'Plateforme de jeux et moteur de jeu', 'contact@epicgames.com', '+33123456792', 'https://www.epicgames.com', '2025-11-23 15:45:53', '2025-11-23 15:45:53'),
(5, 'Nintendo eShop', 'https://example.com/nintendo-logo.png', 'Gaming', 'inactif', 'Boutique officielle Nintendo', 'contact@nintendo.com', '+33123456793', 'https://www.nintendo.com', '2025-11-23 15:45:53', '2025-11-23 15:45:53');

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
  `quantite` int(11) NOT NULL DEFAULT 1,
  `montant_total` decimal(10,2) DEFAULT NULL,
  `mode_paiement` varchar(50) DEFAULT NULL,
  `reference_paiement` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `participation`
--

INSERT INTO `participation` (`id_participation`, `id_evenement`, `id_volontaire`, `date_participation`, `statut`, `quantite`, `montant_total`, `mode_paiement`, `reference_paiement`) VALUES
(10, 14, 2, '2025-11-25', 'acceptée', 1, NULL, NULL, NULL),
(11, 16, 3, '2025-11-26', 'en attente', 1, NULL, NULL, NULL),
(12, 17, 4, '2025-11-27', 'acceptée', 1, 40.00, 'Carte bancaire', 'PAY-2FFF5467'),
(13, 15, 5, '2025-12-02', 'en attente', 1, NULL, NULL, NULL),
(14, 14, 5, '2025-12-09', 'en attente', 1, NULL, NULL, NULL);

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
  `utilisateur_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `priorite` enum('Basse','Moyenne','Haute','Urgente') DEFAULT 'Moyenne',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `reclamation`
--

INSERT INTO `reclamation` (`id`, `sujet`, `description`, `email`, `date_creation`, `statut`, `reponse`, `utilisateur_id`, `product_id`, `priorite`, `updated_at`) VALUES
(1, 'Produit defectueux', 'Le jeu recu ne fonctionne pas correctement', 'jean.dupont@email.com', '2025-11-23 15:45:53', 'Traite', NULL, 2, 1, 'Haute', '2025-11-23 15:45:53'),
(2, 'Probleme de livraison', 'Ma commande n a pas ete livree a la bonne adresse', 'marie.martin@email.com', '2025-11-23 15:45:53', 'En cours', NULL, 3, 5, 'Moyenne', '2025-11-23 15:45:53'),
(3, 'Question sur les frais', 'Pourquoi des frais de port supplementaires?', 'admin@engage.com', '2025-11-23 15:45:53', 'Traite', NULL, 1, NULL, 'Basse', '2025-11-24 15:20:01'),
(4, 'ad', 'azd', 'azdazd@azd.com', '2025-11-24 15:21:42', 'Non traite', NULL, NULL, NULL, 'Moyenne', '2025-11-24 15:21:42'),
(5, 'azd', '', 'azdazd@azd.com', '2025-11-24 15:51:29', 'Non traite', NULL, NULL, NULL, 'Moyenne', '2025-11-24 15:51:29'),
(6, 'azd', '', 'azdazd@azd.com', '2025-11-24 15:51:35', 'Non traite', NULL, NULL, NULL, 'Moyenne', '2025-11-24 15:51:35'),
(8, '', '', 'azdazd@azd.com', '2025-11-24 15:54:39', 'Non traite', NULL, NULL, NULL, 'Moyenne', '2025-11-24 15:54:39'),
(9, '', '', 'azdazd@azd.com', '2025-11-24 15:59:19', 'Non traite', NULL, NULL, NULL, 'Moyenne', '2025-11-24 15:59:19'),
(10, '', '', 'azdazd@azd.com', '2025-11-24 16:02:52', 'Non traite', NULL, NULL, NULL, 'Moyenne', '2025-11-24 16:02:52'),
(11, '', '', 'azdazd@azd.com', '2025-11-24 16:04:50', 'Non traite', NULL, NULL, NULL, 'Moyenne', '2025-11-24 16:04:50'),
(12, '', '', 'azdazd@azd.com', '2025-11-24 16:07:35', 'Non traite', NULL, NULL, NULL, 'Moyenne', '2025-11-24 16:07:35'),
(13, '', '', 'azdazd@azd.com', '2025-11-24 16:07:35', 'Non traite', NULL, NULL, NULL, 'Moyenne', '2025-11-24 16:07:35'),
(14, '', '', 'sghaiersana069@gmail.com', '2025-11-24 18:51:48', 'Non traite', NULL, NULL, NULL, 'Moyenne', '2025-11-24 18:51:48'),
(15, 'test test test test ', 'test test test test test eazdazdazd', 'azdazd@azd.com', '2025-11-24 19:57:42', 'Non traite', NULL, NULL, NULL, 'Moyenne', '2025-11-24 19:57:42');

-- --------------------------------------------------------

--
-- Structure de la table `response`
--

CREATE TABLE `response` (
  `id` int(11) NOT NULL,
  `reclamation_id` int(11) NOT NULL,
  `contenu` longtext NOT NULL,
  `date_response` timestamp NOT NULL DEFAULT current_timestamp(),
  `admin_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `response`
--

INSERT INTO `response` (`id`, `reclamation_id`, `contenu`, `date_response`, `admin_id`) VALUES
(0, 1, 'azdazd', '2025-11-24 20:24:12', NULL),
(1, 1, 'Merci de votre signalement. Un remboursement a ete emis.', '2025-11-23 15:45:53', 1),
(2, 1, 'Le colis de remplacement est en route.', '2025-11-23 15:45:53', 1),
(3, 3, 'zed', '2025-11-24 15:20:01', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `store_items`
--

CREATE TABLE `store_items` (
  `id` int(11) NOT NULL,
  `partenaire_id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `categorie` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `plateforme` varchar(100) DEFAULT NULL,
  `age_minimum` int(11) DEFAULT 0,
  `likes_count` int(11) DEFAULT 0,
  `views_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `store_items`
--

INSERT INTO `store_items` (`id`, `partenaire_id`, `nom`, `prix`, `stock`, `categorie`, `image`, `description`, `plateforme`, `age_minimum`, `likes_count`, `views_count`, `created_at`, `updated_at`) VALUES
(1, 1, 'God of War Ragnarok', 69.99, 15, 'Action-Adventure', 'https://example.com/god-of-war.jpg', 'L epique conclusion de la saga God of War. Kratos et Atreus affrontent les forces de la nature.', 'PlayStation 5', 18, 245, 1501, '2025-11-23 15:45:53', '2025-11-24 13:52:49'),
(2, 1, 'Spider-Man 2', 69.99, 20, 'Action-Adventure', 'https://example.com/spiderman2.jpg', 'Marvel s Spider-Man 2 sur PlayStation 5 avec des super-vilains emblematiques.', 'PlayStation 5', 16, 189, 1200, '2025-11-23 15:45:53', '2025-11-23 15:45:53'),
(3, 2, 'Halo Infinite', 59.99, 10, 'Shooter', 'https://example.com/halo.jpg', 'Le retour du legendaire Halo avec un multijoueur revolutionnaire.', 'Xbox Series X/S', 16, 156, 980, '2025-11-23 15:45:53', '2025-11-23 15:45:53'),
(4, 2, 'Forza Motorsport 5', 49.99, 25, 'Racing', 'https://example.com/forza5.jpg', 'Course automobile de simulation avec des voitures du monde entier.', 'Xbox Series X/S', 3, 167, 850, '2025-11-23 15:45:53', '2025-11-23 15:45:53'),
(5, 3, 'Counter-Strike 2', 0.00, 9999, 'Shooter', 'https://example.com/cs2.jpg', 'Le jeu de tir tactique competitif qui a revolutionne l esport.', 'PC', 16, 478, 2500, '2025-11-23 15:45:53', '2025-11-23 15:45:53'),
(6, 3, 'Dota 2', 0.00, 9999, 'MOBA', 'https://example.com/dota2.jpg', 'Le jeu de strategie en temps reel le plus populaire au monde.', 'PC', 13, 345, 1800, '2025-11-23 15:45:53', '2025-11-23 15:45:53'),
(7, 4, 'Fortnite Battle Royale', 0.00, 9999, 'Battle Royale', 'https://example.com/fortnite.jpg', 'Le jeu de survie et construction multijoueur le plus populaire.', 'PC/Console', 13, 512, 3500, '2025-11-23 15:45:53', '2025-11-23 15:45:53'),
(8, 4, 'Unreal Engine 5', 0.00, 9999, 'Development', 'https://example.com/ue5.jpg', 'Moteur de jeu de pointe pour la creation de jeux et contenu 3D.', 'PC', 0, 234, 1100, '2025-11-23 15:45:53', '2025-11-23 15:45:53'),
(9, 5, 'The Legend of Zelda: Tears of the Kingdom', 69.99, 5, 'Action-Adventure', 'https://example.com/zelda.jpg', 'La suite tant attendue de Breath of the Wild sur Nintendo Switch.', 'Nintendo Switch', 12, 423, 2200, '2025-11-23 15:45:53', '2025-11-23 15:45:53'),
(10, 5, 'Mario Kart 8 Deluxe', 59.99, 8, 'Racing', 'https://example.com/mario-kart.jpg', 'Le jeu de course le plus populaire sur Nintendo Switch.', 'Nintendo Switch', 3, 389, 1900, '2025-11-23 15:45:53', '2025-11-23 15:45:53');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id_utilisateur` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `gamer_tag` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id_utilisateur`, `nom`, `prenom`, `email`, `gamer_tag`) VALUES
(1, 'youssef', 'daadaa', 'youssef@gmail.com', 'djoo'),
(2, 'mahmoud', 'mkaddem', 'mahmoud@gmail.com', 'maha'),
(3, 'hdj', 'ghgfdtjyj', 'dyrj@gmail.com', ''),
(4, 'talbi', 'anas', 'anastalbi@gmail.com', ''),
(5, 'daadaa', 'youussef', 'djoyouyou55@gmail.com', '');

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `v_active_partners`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `v_active_partners` (
`id` int(11)
,`nom` varchar(255)
,`logo` varchar(255)
,`type` varchar(100)
,`description` text
,`email` varchar(255)
,`telephone` varchar(20)
,`site_web` varchar(255)
,`product_count` bigint(21)
);

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `v_store_items_with_partner`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `v_store_items_with_partner` (
`id` int(11)
,`nom` varchar(255)
,`prix` decimal(10,2)
,`stock` int(11)
,`categorie` varchar(100)
,`image` varchar(255)
,`description` longtext
,`plateforme` varchar(100)
,`age_minimum` int(11)
,`likes_count` int(11)
,`views_count` int(11)
,`created_at` timestamp
,`partenaire_id` int(11)
,`partenaire_nom` varchar(255)
,`partenaire_logo` varchar(255)
,`partenaire_statut` enum('actif','inactif','suspendu')
);

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `v_top_products`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `v_top_products` (
`id` int(11)
,`nom` varchar(255)
,`prix` decimal(10,2)
,`stock` int(11)
,`categorie` varchar(100)
,`image` varchar(255)
,`likes_count` int(11)
,`views_count` int(11)
,`popularity_score` decimal(13,1)
);

-- --------------------------------------------------------

--
-- Structure de la vue `v_active_partners`
--
DROP TABLE IF EXISTS `v_active_partners`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_active_partners`  AS SELECT `partenaires`.`id` AS `id`, `partenaires`.`nom` AS `nom`, `partenaires`.`logo` AS `logo`, `partenaires`.`type` AS `type`, `partenaires`.`description` AS `description`, `partenaires`.`email` AS `email`, `partenaires`.`telephone` AS `telephone`, `partenaires`.`site_web` AS `site_web`, (select count(0) from `store_items` where `store_items`.`partenaire_id` = `partenaires`.`id`) AS `product_count` FROM `partenaires` WHERE `partenaires`.`statut` = 'actif' ;

-- --------------------------------------------------------

--
-- Structure de la vue `v_store_items_with_partner`
--
DROP TABLE IF EXISTS `v_store_items_with_partner`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_store_items_with_partner`  AS SELECT `s`.`id` AS `id`, `s`.`nom` AS `nom`, `s`.`prix` AS `prix`, `s`.`stock` AS `stock`, `s`.`categorie` AS `categorie`, `s`.`image` AS `image`, `s`.`description` AS `description`, `s`.`plateforme` AS `plateforme`, `s`.`age_minimum` AS `age_minimum`, `s`.`likes_count` AS `likes_count`, `s`.`views_count` AS `views_count`, `s`.`created_at` AS `created_at`, `p`.`id` AS `partenaire_id`, `p`.`nom` AS `partenaire_nom`, `p`.`logo` AS `partenaire_logo`, `p`.`statut` AS `partenaire_statut` FROM (`store_items` `s` left join `partenaires` `p` on(`s`.`partenaire_id` = `p`.`id`)) WHERE `p`.`statut` = 'actif' ;

-- --------------------------------------------------------

--
-- Structure de la vue `v_top_products`
--
DROP TABLE IF EXISTS `v_top_products`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_top_products`  AS SELECT `store_items`.`id` AS `id`, `store_items`.`nom` AS `nom`, `store_items`.`prix` AS `prix`, `store_items`.`stock` AS `stock`, `store_items`.`categorie` AS `categorie`, `store_items`.`image` AS `image`, `store_items`.`likes_count` AS `likes_count`, `store_items`.`views_count` AS `views_count`, `store_items`.`likes_count`+ `store_items`.`views_count` * 0.1 AS `popularity_score` FROM `store_items` ORDER BY `store_items`.`likes_count`+ `store_items`.`views_count` * 0.1 DESC ;

--
-- Index pour les tables déchargées
--

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
  ADD PRIMARY KEY (`id_evenement`);

--
-- Index pour la table `item_comments`
--
ALTER TABLE `item_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_store_item_id` (`store_item_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Index pour la table `missions`
--
ALTER TABLE `missions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_statut` (`statut`),
  ADD KEY `idx_jeu` (`jeu`),
  ADD KEY `idx_date_creation` (`date_creation`),
  ADD KEY `idx_createur_id` (`createur_id`);
ALTER TABLE `missions` ADD FULLTEXT KEY `ft_titre_description` (`titre`,`description`);

--
-- Index pour la table `partenaires`
--
ALTER TABLE `partenaires`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_statut` (`statut`),
  ADD KEY `idx_nom` (`nom`),
  ADD KEY `idx_created_at` (`created_at`);
ALTER TABLE `partenaires` ADD FULLTEXT KEY `ft_nom_description` (`nom`,`description`);

--
-- Index pour la table `participation`
--
ALTER TABLE `participation`
  ADD PRIMARY KEY (`id_participation`),
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
  ADD KEY `idx_partenaire_id` (`partenaire_id`),
  ADD KEY `idx_categorie` (`categorie`),
  ADD KEY `idx_prix` (`prix`),
  ADD KEY `idx_stock` (`stock`),
  ADD KEY `idx_created_at` (`created_at`);
ALTER TABLE `store_items` ADD FULLTEXT KEY `ft_nom_description` (`nom`,`description`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id_utilisateur`);
ALTER TABLE `utilisateur` ADD FULLTEXT KEY `ft_prenom_nom` (`prenom`,`nom`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `candidatures`
--
ALTER TABLE `candidatures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `evenement`
--
ALTER TABLE `evenement`
  MODIFY `id_evenement` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `item_comments`
--
ALTER TABLE `item_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `missions`
--
ALTER TABLE `missions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `partenaires`
--
ALTER TABLE `partenaires`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `participation`
--
ALTER TABLE `participation`
  MODIFY `id_participation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `reclamation`
--
ALTER TABLE `reclamation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `participation`
--
ALTER TABLE `participation`
  ADD CONSTRAINT `fk_participation_evenement` FOREIGN KEY (`id_evenement`) REFERENCES `evenement` (`id_evenement`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_participation_volontaire` FOREIGN KEY (`id_volontaire`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
