<?php
require_once __DIR__ . '/db_config.php';

try {
    $conn = config::getConnexion();
    
    // Check if column already exists
    $sql = "SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'evenement' 
            AND COLUMN_NAME = 'vues'";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ((int)$row['cnt'] > 0) {
        echo "✓ Column 'vues' already exists in 'evenement' table.<br>";
    } else {
        // Add the column
        $alterSQL = "ALTER TABLE evenement ADD COLUMN vues INT NOT NULL DEFAULT 0";
        $conn->exec($alterSQL);
        echo "✓ Successfully added 'vues' column to 'evenement' table with default value 0.<br>";
    }
    
    // Also add other missing columns if needed
    $checkTheme = "SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA = DATABASE() 
                   AND TABLE_NAME = 'evenement' 
                   AND COLUMN_NAME = 'theme'";
    
    $stmt2 = $conn->prepare($checkTheme);
    $stmt2->execute();
    $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
    
    if ((int)$row2['cnt'] == 0) {
        $conn->exec("ALTER TABLE evenement ADD COLUMN theme VARCHAR(100) DEFAULT 'evenement'");
        echo "✓ Successfully added 'theme' column to 'evenement' table.<br>";
    } else {
        echo "✓ Column 'theme' already exists in 'evenement' table.<br>";
    }
    
    // Check for heure_evenement
    $checkTime = "SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS 
                  WHERE TABLE_SCHEMA = DATABASE() 
                  AND TABLE_NAME = 'evenement' 
                  AND COLUMN_NAME = 'heure_evenement'";
    
    $stmt3 = $conn->prepare($checkTime);
    $stmt3->execute();
    $row3 = $stmt3->fetch(PDO::FETCH_ASSOC);
    
    if ((int)$row3['cnt'] == 0) {
        $conn->exec("ALTER TABLE evenement ADD COLUMN heure_evenement TIME DEFAULT NULL");
        echo "✓ Successfully added 'heure_evenement' column to 'evenement' table.<br>";
    } else {
        echo "✓ Column 'heure_evenement' already exists in 'evenement' table.<br>";
    }

    // Check for duree_minutes
    $checkDuration = "SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS 
                      WHERE TABLE_SCHEMA = DATABASE() 
                      AND TABLE_NAME = 'evenement' 
                      AND COLUMN_NAME = 'duree_minutes'";
    
    $stmt4 = $conn->prepare($checkDuration);
    $stmt4->execute();
    $row4 = $stmt4->fetch(PDO::FETCH_ASSOC);
    
    if ((int)$row4['cnt'] == 0) {
        $conn->exec("ALTER TABLE evenement ADD COLUMN duree_minutes INT DEFAULT NULL");
        echo "✓ Successfully added 'duree_minutes' column to 'evenement' table.<br>";
    } else {
        echo "✓ Column 'duree_minutes' already exists in 'evenement' table.<br>";
    }

    // Check for type_evenement
    $checkType = "SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS 
                  WHERE TABLE_SCHEMA = DATABASE() 
                  AND TABLE_NAME = 'evenement' 
                  AND COLUMN_NAME = 'type_evenement'";
    
    $stmt5 = $conn->prepare($checkType);
    $stmt5->execute();
    $row5 = $stmt5->fetch(PDO::FETCH_ASSOC);
    
    if ((int)$row5['cnt'] == 0) {
        $conn->exec("ALTER TABLE evenement ADD COLUMN type_evenement VARCHAR(50) DEFAULT 'gratuit'");
        echo "✓ Successfully added 'type_evenement' column to 'evenement' table.<br>";
    } else {
        echo "✓ Column 'type_evenement' already exists in 'evenement' table.<br>";
    }

    // Check for prix
    $checkPrice = "SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA = DATABASE() 
                   AND TABLE_NAME = 'evenement' 
                   AND COLUMN_NAME = 'prix'";
    
    $stmt6 = $conn->prepare($checkPrice);
    $stmt6->execute();
    $row6 = $stmt6->fetch(PDO::FETCH_ASSOC);
    
    if ((int)$row6['cnt'] == 0) {
        $conn->exec("ALTER TABLE evenement ADD COLUMN prix DECIMAL(10,2) DEFAULT NULL");
        echo "✓ Successfully added 'prix' column to 'evenement' table.<br>";
    } else {
        echo "✓ Column 'prix' already exists in 'evenement' table.<br>";
    }
    
    echo "<hr><strong style='color: green;'>✓ All required columns are now present in the 'evenement' table!</strong>";
    
} catch (PDOException $e) {
    echo "<strong style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</strong>";
}
?>
