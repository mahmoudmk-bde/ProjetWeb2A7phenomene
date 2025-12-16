-- Add feedback columns to participation table for event feedback
ALTER TABLE `participation` 
ADD COLUMN `rating` TINYINT(1) DEFAULT NULL COMMENT 'Rating from 1 to 5' AFTER `reference_paiement`,
ADD COLUMN `commentaire` LONGTEXT DEFAULT NULL AFTER `rating`,
ADD COLUMN `date_feedback` TIMESTAMP DEFAULT NULL AFTER `commentaire`;
