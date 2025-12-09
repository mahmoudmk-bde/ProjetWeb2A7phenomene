<?php
/**
 * Script de test rapide pour insérer quelques mots interdits
 * Usage: Ouvrez ce fichier dans votre navigateur
 */

require_once __DIR__ . '/db_config.php';
require_once __DIR__ . '/controller/BadWordController.php';

$badWordController = new BadWordController();

// Liste courte pour les tests rapides
$testWords = [
    'merde',
    'con',
    'connard',
    'salope',
    'pute',
    'fuck',
    'shit',
    'damn',
    'idiot',
    'stupide',
    'bordel',
    'putain',
    'bitch',
    'asshole',
    'crétin',
    'imbécile'
];

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Test Bad Words</title>";
echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;}";
echo ".success{color:green;} .warning{color:orange;} .error{color:red;}";
echo "pre{background:white;padding:15px;border-radius:5px;}</style></head><body>";
echo "<h2>🧪 Test - Insertion de mots interdits</h2>";
echo "<pre>";

$inserted = 0;
$skipped = 0;

foreach ($testWords as $word) {
    if ($badWordController->addBadWord($word)) {
        echo "<span class='success'>✅</span> Ajouté: <strong>$word</strong>\n";
        $inserted++;
    } else {
        echo "<span class='warning'>⚠️</span> Déjà présent: <strong>$word</strong>\n";
        $skipped++;
    }
}

echo "\n═══════════════════════════════════════\n";
echo "Résumé:\n";
echo "<span class='success'>✅ Mots ajoutés: $inserted</span>\n";
echo "<span class='warning'>⚠️ Mots déjà présents: $skipped</span>\n";
echo "═══════════════════════════════════════\n";

// Afficher tous les mots interdits
echo "\n📋 Liste complète des mots interdits:\n";
$allWords = $badWordController->getAllBadWords();
if (empty($allWords)) {
    echo "  (Aucun mot interdit dans la base)\n";
} else {
    foreach ($allWords as $word) {
        echo "  • " . htmlspecialchars($word['word']) . "\n";
    }
    echo "\nTotal: " . count($allWords) . " mots interdits\n";
}

echo "</pre>";
echo "<p><strong>💡 Pour tester:</strong></p>";
echo "<ul>";
echo "<li>Essayez de soumettre un commentaire de feedback contenant un de ces mots</li>";
echo "<li>Vous devriez être banni automatiquement pendant 3 jours</li>";
echo "<li>Exemple de commentaire à tester: \"Cette mission est vraiment <strong>merde</strong>!\"</li>";
echo "</ul>";
echo "<p><a href='javascript:history.back()'>← Retour</a> | ";
echo "<a href='insert_bad_words.php'>📝 Insérer la liste complète</a></p>";
echo "</body></html>";

