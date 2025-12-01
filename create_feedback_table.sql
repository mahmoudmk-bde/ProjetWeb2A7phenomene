-- Create feedback table for mission ratings and comments
CREATE TABLE IF NOT EXISTS `feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_mission` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL DEFAULT 5 COMMENT 'Rating from 1 to 5',
  `commentaire` longtext DEFAULT NULL,
  `date_feedback` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_mission_feedback` (`id_mission`, `id_utilisateur`),
  KEY `idx_mission` (`id_mission`),
  KEY `idx_utilisateur` (`id_utilisateur`),
  KEY `idx_rating` (`rating`),
  KEY `idx_date_feedback` (`date_feedback`),
  CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`id_mission`) REFERENCES `missions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_util`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

