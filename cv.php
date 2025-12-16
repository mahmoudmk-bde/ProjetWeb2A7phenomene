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

    // Définir la racine du projet (cv.php est à la racine du projet)
    $projectRoot = __DIR__; // cv.php est dans ProjetWeb2A7phenomene/

    // Mode debug
    $debug = isset($_GET['debug']) && $_GET['debug'] == '1';

    // Liste des chemins possibles à essayer (du plus spécifique au plus général)
    $possiblePaths = [];

    // 1. Chemin tel qu'enregistré dans la DB (relatif)
    $possiblePaths[] = $projectRoot . '/' . $cv_rel;

    // 2. Chemin absolu si déjà stocké en absolu (peu probable)
    $possiblePaths[] = $cv_rel;

    // 3. Si le chemin contient 'view/frontoffice/', essayer sans ce préfixe
    if (strpos($cv_rel, 'view/frontoffice/') === 0) {
        $withoutView = substr($cv_rel, strlen('view/frontoffice/'));
        $possiblePaths[] = $projectRoot . '/' . $withoutView;
    }

    // 4. Si le chemin commence par 'assets/', essayer directement
    if (strpos($cv_rel, 'assets/') === 0) {
        $possiblePaths[] = $projectRoot . '/' . $cv_rel;
        // Essayer aussi depuis la racine web
        $possiblePaths[] = $_SERVER['DOCUMENT_ROOT'] . '/' . $cv_rel;
    }

    // 5. Extraire juste le nom du fichier et chercher dans les dossiers communs
    $filename = basename($cv_rel);
    $possiblePaths[] = $projectRoot . '/assets/uploads/cv/' . $filename;
    $possiblePaths[] = $projectRoot . '/view/frontoffice/assets/uploads/cv/' . $filename;
    $possiblePaths[] = $_SERVER['DOCUMENT_ROOT'] . '/assets/uploads/cv/' . $filename;
    $possiblePaths[] = $_SERVER['DOCUMENT_ROOT'] . '/view/frontoffice/assets/uploads/cv/' . $filename;

    // 6. Chemin relatif depuis la racine web
    $possiblePaths[] = $_SERVER['DOCUMENT_ROOT'] . '/' . $cv_rel;

    // Nettoyer et dédupliquer les chemins
    $possiblePaths = array_map(function ($path) {
        return str_replace('\\', '/', $path);
    }, $possiblePaths);
    $possiblePaths = array_unique($possiblePaths);

    if ($debug) {
        header('Content-Type: text/plain; charset=utf-8');
        echo "=== CV Debug Info ===\n";
        echo "CV path from DB: " . htmlspecialchars($row['cv']) . "\n";
        echo "cv_rel (normalized): " . htmlspecialchars($cv_rel) . "\n";
        echo "projectRoot: " . htmlspecialchars($projectRoot) . "\n";
        echo "DOCUMENT_ROOT: " . htmlspecialchars($_SERVER['DOCUMENT_ROOT']) . "\n";
        echo "\nPossible paths tried:\n";
    }

    $realFile = false;
    $triedPaths = [];
    foreach ($possiblePaths as $path) {
        $triedPaths[] = $path;
        $resolved = realpath($path);

        if ($debug) {
            $exists = file_exists($path);
            echo "  - " . $path . "\n";
            echo "    -> exists: " . ($exists ? "YES" : "NO");
            if ($resolved !== false) {
                echo " | resolved: " . $resolved;
                echo " | is_file: " . (is_file($resolved) ? "YES" : "NO");
                echo " | readable: " . (is_readable($resolved) ? "YES" : "NO");
            }
            echo "\n";
        }

        if ($resolved !== false && is_file($resolved) && is_readable($resolved)) {
            $realFile = $resolved;
            if ($debug)
                echo "    *** SELECTED THIS PATH ***\n";
            break;
        }
    }

    if ($debug) {
        echo "\nrealFile (final): " . var_export($realFile, true) . "\n";
        exit;
    }

    // Vérifier que le fichier existe
    if ($realFile === false) {
        http_response_code(404);
        echo "File not found. CV path in DB: " . htmlspecialchars($row['cv']) . ".\n";
        echo "Add ?debug=1 to URL for details.\n";
        echo "Tried paths:\n";
        foreach ($triedPaths as $i => $path) {
            echo "- " . htmlspecialchars($path) . "\n";
        }
        exit;
    }

    // Sécurité : vérifier que le fichier est dans un dossier autorisé
    $allowedDirs = [
        strtolower(realpath($projectRoot . '/assets/uploads/cv')),
        strtolower(realpath($projectRoot . '/view/frontoffice/assets/uploads/cv')),
        strtolower(realpath($_SERVER['DOCUMENT_ROOT'] . '/assets/uploads/cv')),
        strtolower(realpath($_SERVER['DOCUMENT_ROOT'] . '/view/frontoffice/assets/uploads/cv'))
    ];

    $allowed = false;
    $fileDir = strtolower(dirname($realFile));
    foreach ($allowedDirs as $allowedDir) {
        if ($allowedDir && strpos($fileDir, $allowedDir) === 0) {
            $allowed = true;
            break;
        }
    }

    if (!$allowed) {
        http_response_code(403);
        echo "Access denied. File not in allowed directory.";
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
        // View inline when the browser can display it
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
    readfile($realFile);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo "Server error: " . htmlspecialchars($e->getMessage());
    exit;
}
