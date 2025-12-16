<?php
/**
 * Migration runner to add AI classification columns to reclamation table
 */
require_once __DIR__ . '/db_config.php';

try {
    $pdo = config::getConnexion();
    
    echo "Running migration: Add reclamation classification columns...\n";
    
    // Add category column
    $pdo->exec("ALTER TABLE reclamation ADD COLUMN IF NOT EXISTS category VARCHAR(50) DEFAULT 'general'");
    echo "✓ Added category column\n";
    
    // Add department column
    $pdo->exec("ALTER TABLE reclamation ADD COLUMN IF NOT EXISTS department VARCHAR(100) DEFAULT 'General Support'");
    echo "✓ Added department column\n";
    
    // Add indexes
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_reclamation_category ON reclamation(category)");
    echo "✓ Added category index\n";
    
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_reclamation_priority ON reclamation(priorite)");
    echo "✓ Added priority index\n";
    
    echo "\n✅ Migration completed successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
