<?php
// migrate_cvs.php
// Utility script to normalize/migrate CV files and DB paths.
// Usage (CLI): php migrate_cvs.php
// It will:
// - Ensure canonical directory assets/uploads/cv exists
// - Move files from view/frontoffice/assets/uploads/cv to assets/uploads/cv if present
// - Update DB entries replacing 'view/frontoffice/assets/uploads/cv/' with 'assets/uploads/cv/'
// - Report actions

require_once __DIR__ . '/db_config.php';

$pdo = config::getConnexion();
$projectRoot = realpath(__DIR__);
$srcDir = $projectRoot . '/view/frontoffice/assets/uploads/cv';
$dstDir = $projectRoot . '/assets/uploads/cv';

echo "Project root: $projectRoot\n";
echo "Source dir: $srcDir\n";
echo "Destination dir: $dstDir\n";

// Ensure destination exists
if (!is_dir($dstDir)) {
    if (!mkdir($dstDir, 0755, true)) {
        echo "Unable to create destination dir: $dstDir\n";
        exit(1);
    }
}

// Move files from src to dst
$moved = 0;
if (is_dir($srcDir)) {
    $files = scandir($srcDir);
    foreach ($files as $f) {
        if ($f === '.' || $f === '..') continue;
        $src = $srcDir . '/' . $f;
        $dst = $dstDir . '/' . $f;
        if (is_file($src)) {
            if (!file_exists($dst)) {
                if (rename($src, $dst)) {
                    echo "Moved $f -> assets/uploads/cv/$f\n";
                    $moved++;
                } else {
                    echo "Failed to move $f\n";
                }
            } else {
                echo "Destination already has $f, skipping move.\n";
            }
        }
    }
} else {
    echo "Source dir does not exist, skipping file moves.\n";
}

// Update DB: replace view/frontoffice path with canonical assets path
$search = 'view/frontoffice/assets/uploads/cv/';
$replace = 'assets/uploads/cv/';

$stmt = $pdo->prepare("SELECT id, cv FROM candidatures WHERE cv LIKE :like");
$stmt->execute([':like' => '%' . $search . '%']);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$updated = 0;
foreach ($rows as $r) {
    $old = $r['cv'];
    $new = str_replace($search, $replace, $old);
    $u = $pdo->prepare("UPDATE candidatures SET cv = :cv WHERE id = :id");
    $u->execute([':cv' => $new, ':id' => $r['id']]);
    echo "Updated DB id={$r['id']}: $old -> $new\n";
    $updated++;
}

// Additionally, optional: if records have NULL cv but a file with name matching id exists, skip (not implemented)

echo "Done. Moved files: $moved. DB rows updated: $updated\n";
