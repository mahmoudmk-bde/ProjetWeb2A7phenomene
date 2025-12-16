<?php
/**
 * Database Auto-Migration System
 * This file runs all necessary database schema updates automatically
 * Include this file at the start of any page that needs database features
 */

require_once __DIR__ . '/db_config.php';

class DatabaseMigrations {
    private static $pdo = null;
    private static $executed = false;

    public static function run() {
        if (self::$executed) {
            return; // Already ran in this request
        }

        try {
            self::$pdo = config::getConnexion();
            self::$executed = true;

            // Run all migrations
            self::migrateReclamation();
            self::migrateEventFeedback();
            self::migrateLikes();
            self::migrateParticipation();
            self::migrateUtilisateur();
        } catch (Exception $e) {
            error_log('Migration error: ' . $e->getMessage());
        }
    }

    /**
     * Reclamation table migrations
     */
    private static function migrateReclamation() {
        $migrations = [
            "ALTER TABLE reclamation ADD COLUMN IF NOT EXISTS category VARCHAR(50) DEFAULT 'general'",
            "ALTER TABLE reclamation ADD COLUMN IF NOT EXISTS department VARCHAR(100) DEFAULT 'General Support'",
            "CREATE INDEX IF NOT EXISTS idx_reclamation_category ON reclamation(category)",
            "CREATE INDEX IF NOT EXISTS idx_reclamation_priority ON reclamation(priorite)",
            "CREATE TABLE IF NOT EXISTS reclamation_tags (
                id INT AUTO_INCREMENT PRIMARY KEY,
                reclamation_id INT NOT NULL,
                tag VARCHAR(64) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_reclamation_tag (reclamation_id, tag),
                INDEX idx_tag (tag)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        ];
        
        foreach ($migrations as $sql) {
            try {
                self::$pdo->exec($sql);
            } catch (PDOException $e) {
                // Column/table may already exist
            }
        }
    }

    /**
     * Event feedback table migrations
     */
    private static function migrateEventFeedback() {
        $migrations = [
            "CREATE TABLE IF NOT EXISTS event_feedback (
                id INT(11) NOT NULL AUTO_INCREMENT,
                id_event INT(11) NOT NULL,
                id_utilisateur INT(11) NOT NULL,
                rating TINYINT(1) NOT NULL DEFAULT 5,
                commentaire LONGTEXT DEFAULT NULL,
                date_feedback TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY unique_event_user (id_event, id_utilisateur),
                KEY idx_event (id_event),
                KEY idx_user (id_utilisateur)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        ];

        foreach ($migrations as $sql) {
            try {
                self::$pdo->exec($sql);
            } catch (PDOException $e) {
                // Table may already exist
            }
        }
    }

    /**
     * Likes table migrations
     */
    private static function migrateLikes() {
        $migrations = [
            "CREATE TABLE IF NOT EXISTS likes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                mission_id INT NOT NULL,
                user_id INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_like (mission_id, user_id),
                INDEX idx_mission (mission_id),
                INDEX idx_user (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        ];

        foreach ($migrations as $sql) {
            try {
                self::$pdo->exec($sql);
            } catch (PDOException $e) {
                // Table may already exist
            }
        }
    }

    /**
     * Participation table migrations
     */
    private static function migrateParticipation() {
        // Check if columns exist before adding
        try {
            $stmt = self::$pdo->query("SHOW COLUMNS FROM participation LIKE 'views'");
            if ($stmt->rowCount() == 0) {
                self::$pdo->exec("ALTER TABLE participation ADD COLUMN views INT DEFAULT 0");
            }
        } catch (PDOException $e) {
            // Column may already exist or table doesn't exist
        }
    }

    /**
     * Utilisateur table migrations
     */
    private static function migrateUtilisateur() {
        // Ensure img column exists for profile photos
        try {
            $stmt = self::$pdo->query("SHOW COLUMNS FROM utilisateur LIKE 'img'");
            if ($stmt->rowCount() == 0) {
                self::$pdo->exec("ALTER TABLE utilisateur ADD COLUMN img VARCHAR(255) DEFAULT NULL");
            }
        } catch (PDOException $e) {
            // Column may already exist
        }

        // Ensure face descriptor column exists for facial recognition
        try {
            $stmt = self::$pdo->query("SHOW COLUMNS FROM utilisateur LIKE 'face'");
            if ($stmt->rowCount() == 0) {
                self::$pdo->exec("ALTER TABLE utilisateur ADD COLUMN face TEXT DEFAULT NULL COMMENT 'Face.js descriptor for facial recognition'");
            }
        } catch (PDOException $e) {
            // Column may already exist
        }

        // Ensure auth column exists for 2FA
        try {
            $stmt = self::$pdo->query("SHOW COLUMNS FROM utilisateur LIKE 'auth'");
            if ($stmt->rowCount() == 0) {
                self::$pdo->exec("ALTER TABLE utilisateur ADD COLUMN auth VARCHAR(20) DEFAULT 'desactive'");
            }
        } catch (PDOException $e) {
            // Column may already exist
        }

        // Ensure security question columns exist
        try {
            $stmt = self::$pdo->query("SHOW COLUMNS FROM utilisateur LIKE 'q1'");
            if ($stmt->rowCount() == 0) {
                self::$pdo->exec("ALTER TABLE utilisateur ADD COLUMN q1 VARCHAR(255) DEFAULT NULL");
            }
        } catch (PDOException $e) {
            // Column may already exist
        }

        try {
            $stmt = self::$pdo->query("SHOW COLUMNS FROM utilisateur LIKE 'rp1'");
            if ($stmt->rowCount() == 0) {
                self::$pdo->exec("ALTER TABLE utilisateur ADD COLUMN rp1 VARCHAR(255) DEFAULT NULL");
            }
        } catch (PDOException $e) {
            // Column may already exist
        }

        try {
            $stmt = self::$pdo->query("SHOW COLUMNS FROM utilisateur LIKE 'q2'");
            if ($stmt->rowCount() == 0) {
                self::$pdo->exec("ALTER TABLE utilisateur ADD COLUMN q2 VARCHAR(255) DEFAULT NULL");
            }
        } catch (PDOException $e) {
            // Column may already exist
        }

        try {
            $stmt = self::$pdo->query("SHOW COLUMNS FROM utilisateur LIKE 'rp2'");
            if ($stmt->rowCount() == 0) {
                self::$pdo->exec("ALTER TABLE utilisateur ADD COLUMN rp2 VARCHAR(255) DEFAULT NULL");
            }
        } catch (PDOException $e) {
            // Column may already exist
        }
    }
}

// Auto-run migrations when this file is included
DatabaseMigrations::run();
