<?php
// cv.php - sert un CV en mode 'view' (inline) ou 'download' (attachment)
// Usage: /cv.php?id=123&mode=view|download

require_once __DIR__ . '/db_config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$mode = isset($_GET['mode']) && $_GET['mode'] === 'download' ? 'download' : 'view';

if ($id <= 0) {
    http_response_code(400);
    echo "Invalid id";
    exit;
}

try {
    $pdo = config::getConnexion();
    $stmt = $pdo->prepare('SELECT cv FROM candidatures WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row || empty($row['cv'])) {
        http_response_code(404);
        echo "CV not found for this candidature.";
        exit;
    }

    // Normaliser le chemin enregistré
    $cv_rel = str_replace('\\', '/', $row['cv']);
    $cv_rel = ltrim($cv_rel, '/');

    // Chemin physique attendu - utiliser la racine du projet
    $publicBase = rtrim(str_replace('\\', '/', realpath(__DIR__)), '/');
    
    // Option debug (affiche les chemins résolus) — activer avec ?debug=1
    $debug = isset($_GET['debug']) && $_GET['debug'] == '1';
    
    // Construire tous les chemins possibles à essayer
    $possiblePaths = [];
    
    // 1. Chemin direct tel qu'enregistré dans la DB
    $possiblePaths[] = $publicBase . '/' . $cv_rel;
    
    // 2. Si le chemin contient déjà le chemin complet, l'utiliser tel quel
    if (strpos($cv_rel, 'view/frontoffice/assets/uploads/cv/') !== false) {
        $possiblePaths[] = $publicBase . '/' . $cv_rel;
    }
    
    // 3. Si le chemin commence par "assets/", essayer tel quel
    if (strpos($cv_rel, 'assets/') === 0) {
        $possiblePaths[] = $publicBase . '/' . $cv_rel;
    }
    
    // 4. Si le chemin commence par "view/", essayer tel quel
    if (strpos($cv_rel, 'view/') === 0) {
        $possiblePaths[] = $publicBase . '/' . $cv_rel;
    }
    
    // 5. Extraire juste le nom du fichier et essayer dans les deux emplacements possibles
    $filename = basename($cv_rel);
    $possiblePaths[] = $publicBase . '/view/frontoffice/assets/uploads/cv/' . $filename;
    $possiblePaths[] = $publicBase . '/assets/uploads/cv/' . $filename;
    
    // 6. Si le chemin contient juste le nom du fichier, essayer les deux emplacements
    if ($cv_rel === $filename || strpos($cv_rel, '/') === false) {
        $possiblePaths[] = $publicBase . '/view/frontoffice/assets/uploads/cv/' . $cv_rel;
        $possiblePaths[] = $publicBase . '/assets/uploads/cv/' . $cv_rel;
    }
    
    // Supprimer les doublons
    $possiblePaths = array_unique($possiblePaths);
    
    $realFile = false;
    $triedPaths = [];
    foreach ($possiblePaths as $path) {
        $path = str_replace('\\', '/', $path);
        $triedPaths[] = $path;
        $resolved = realpath($path);
        if ($resolved !== false && is_file($resolved) && is_readable($resolved)) {
            $realFile = str_replace('\\', '/', $resolved);
            break;
        }
    }

    if ($debug) {
        header('Content-Type: text/plain; charset=utf-8');
        echo "=== CV Debug Info ===\n";
        echo "CV path from DB: " . htmlspecialchars($row['cv']) . "\n";
        echo "cv_rel (normalized): " . htmlspecialchars($cv_rel) . "\n";
        echo "publicBase: " . htmlspecialchars($publicBase) . "\n";
        echo "filename extracted: " . htmlspecialchars($filename) . "\n";
        echo "\nPossible paths tried:\n";
        foreach ($triedPaths as $i => $p) {
            $exists = file_exists($p);
            $resolved = realpath($p);
            echo "  " . ($i + 1) . ". " . $p . "\n";
            echo "     -> exists: " . ($exists ? "YES" : "NO");
            if ($resolved !== false) {
                echo " | resolved: " . $resolved;
                echo " | is_file: " . (is_file($resolved) ? "YES" : "NO");
                echo " | readable: " . (is_readable($resolved) ? "YES" : "NO");
            }
            echo "\n";
        }
        echo "\nrealFile (final): " . var_export($realFile, true) . "\n";
        if ($realFile === false) {
            echo "\nERROR: File not found after trying all paths!\n";
        }
        exit;
    }

    // Vérifier que le fichier existe
    if ($realFile === false || !is_file($realFile) || !is_readable($realFile)) {
        http_response_code(404);
        echo "File not found. CV path in DB: " . htmlspecialchars($row['cv']) . ". Add ?debug=1 to URL for details.";
        exit;
    }

    // Sécurité : s'assurer que le fichier réside bien sous un dossier autorisé
    // Autoriser les fichiers dans assets/uploads/cv ou view/frontoffice/assets/uploads/cv
    $allowedPatterns = [
        '/assets/uploads/cv/',
        '/view/frontoffice/assets/uploads/cv/'
    ];
    
    $allowedMatch = false;
    $normalizedRealFile = strtolower($realFile);
    foreach ($allowedPatterns as $pattern) {
        if (strpos($normalizedRealFile, strtolower($pattern)) !== false) {
            $allowedMatch = true;
            break;
        }
    }
    
    // Si le chemin contient "uploads/cv", on l'autorise aussi (plus flexible)
    if (!$allowedMatch && strpos($normalizedRealFile, 'uploads/cv') !== false) {
        $allowedMatch = true;
    }

    if (!$allowedMatch) {
        http_response_code(403);
        if ($debug) {
            echo "\nAccess denied. File path does not match allowed patterns.\n";
            echo "Allowed patterns: " . implode(', ', $allowedPatterns) . "\n";
            echo "File path: " . $realFile . "\n";
        } else {
            echo "Access denied.";
        }
        exit;
    }

    // Détecter le mime-type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $realFile);
    finfo_close($finfo);

    $basename = basename($realFile);
    $filesize = filesize($realFile);

    // Headers
    header('Content-Type: ' . $mime);
    header('Content-Length: ' . $filesize);
    header('X-Content-Type-Options: nosniff');

    if ($mode === 'download') {
        header('Content-Disposition: attachment; filename="' . rawurldecode($basename) . '"');
    } else {
        // View inline when the browser can display it (pdf, images), otherwise fallback to attachment
        $inlineTypes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/gif',
            'text/plain'
        ];
        if (in_array($mime, $inlineTypes)) {
            header('Content-Disposition: inline; filename="' . rawurldecode($basename) . '"');
        } else {
            header('Content-Disposition: attachment; filename="' . rawurldecode($basename) . '"');
        }
    }

    // Stream the file
    $fp = fopen($realFile, 'rb');
    if ($fp) {
        while (!feof($fp)) {
            echo fread($fp, 8192);
            flush();
        }
        fclose($fp);
        exit;
    } else {
        http_response_code(500);
        echo "Unable to open file.";
        exit;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo "Server error: " . htmlspecialchars($e->getMessage());
    exit;
}

