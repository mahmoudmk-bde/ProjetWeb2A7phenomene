-- Table pour stocker les likes des missions
CREATE TABLE IF NOT EXISTS `likes_missions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mission_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `date_like` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_like` (`mission_id`, `utilisateur_id`),
  KEY `fk_like_mission` (`mission_id`),
  KEY `fk_like_utilisateur` (`utilisateur_id`),
  CONSTRAINT `fk_like_mission` FOREIGN KEY (`mission_id`) REFERENCES `missions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_like_utilisateur` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

