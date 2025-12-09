<?php
/**
 * Script pour insérer une liste de mots interdits dans la base de données
 * Usage: Ouvrez ce fichier dans votre navigateur
 */

require_once __DIR__ . '/db_config.php';
require_once __DIR__ . '/controller/BadWordController.php';

$badWordController = new BadWordController();

// Liste de mots interdits pour les tests
$badWords = [
    // Mots français courants
    'merde', 'con', 'connard', 'connasse', 'salope', 'pute', 'putain',
    'enculé', 'enculer', 'bite', 'couilles', 'chier', 'chié', 'bordel',
    'crétin', 'idiot', 'imbécile', 'stupide', 'débile', 'abruti', 'salaud',
    'fils de pute', 'fdp', 'pd', 'pédé', 'tapette', 'pédale',
    
    // Mots anglais courants
    'fuck', 'fucking', 'shit', 'damn', 'bitch', 'asshole', 'bastard',
    'crap', 'hell', 'stupid', 'idiot', 'dumb', 'retard', 'moron',
    
    // Variantes avec caractères spéciaux
    'm3rd3', 'c0n', 'f*ck', 'sh!t', 'b!tch', '@sshole',
    
    // Insultes
    'nazi', 'hitler', 'imbécile', 'crétin',
    
    // Mots offensants supplémentaires
    'merdique', 'connerie', 'saloperie', 'putasserie',
    'enfoiré', 'enfoirée', 'salaud', 'salaude',
    'connard', 'connasse', 'trou du cul', 'trouduc',
    'foutre', 'foutre', 'niquer', 'nique',
    'branleur', 'branleuse', 'branlette',
    'suce', 'sucer', 'sucette',
    'bite', 'bites', 'chibre',
    'couille', 'couilles', 'burnes',
    'chatte', 'chattes', 'chat',
    'cul', 'culs', 'fion',
    'pisse', 'pisser', 'piss',
    'pète', 'péter', 'pet',
    'caca', 'caca', 'merde',
    'chier', 'chié', 'chiasse',
    'bordel', 'bordel de merde',
    'putain de', 'putain',
    'saloperie', 'saloperie',
    'enculer', 'enculé', 'enculée',
    'niquer', 'nique', 'niqué',
    'foutre', 'foutre', 'foutu',
    'merdique', 'merdique',
    'connerie', 'conneries',
    'con', 'cons', 'connard',
    'connasse', 'connasses',
    'salope', 'salopes',
    'pute', 'putes',
    'putain', 'putains',
    'bitch', 'bitches',
    'fuck', 'fucking', 'fucked',
    'shit', 'shits', 'shitty',
    'damn', 'damned',
    'asshole', 'assholes',
    'bastard', 'bastards',
    'crap', 'craps',
    'hell', 'hells',
    'stupid', 'stupids',
    'idiot', 'idiots',
    'dumb', 'dumber',
    'retard', 'retards',
    'moron', 'morons',
    'imbécile', 'imbéciles',
    'crétin', 'crétins',
    'débile', 'débiles',
    'abruti', 'abrutis',
    'salaud', 'salauds',
    'salaude', 'salaudes',
    'fils de pute', 'fdp',
    'pd', 'pédé', 'pédés',
    'tapette', 'tapettes',
    'pédale', 'pédales',
    'nazi', 'nazis',
    'hitler', 'hitlers',
    'm3rd3', 'c0n', 'f*ck', 'sh!t', 'b!tch', '@sshole'
];

echo "<h2>Insertion des mots interdits</h2>";
echo "<pre>";

$inserted = 0;
$skipped = 0;
$errors = 0;

foreach ($badWords as $word) {
    $word = trim(strtolower($word));
    if (empty($word)) {
        continue;
    }
    
    if ($badWordController->addBadWord($word)) {
        echo "✅ Ajouté: $word\n";
        $inserted++;
    } else {
        // Vérifier si c'est une duplication
        $allWords = $badWordController->getAllBadWords();
        $exists = false;
        foreach ($allWords as $existing) {
            if (strtolower($existing['word']) === $word) {
                $exists = true;
                break;
            }
        }
        
        if ($exists) {
            echo "⚠️  Déjà présent: $word\n";
            $skipped++;
        } else {
            echo "❌ Erreur: $word\n";
            $errors++;
        }
    }
}

echo "\n";
echo "═══════════════════════════════════════\n";
echo "Résumé:\n";
echo "✅ Mots ajoutés: $inserted\n";
echo "⚠️  Mots déjà présents: $skipped\n";
echo "❌ Erreurs: $errors\n";
echo "═══════════════════════════════════════\n";

// Afficher tous les mots interdits actuels
echo "\n📋 Liste complète des mots interdits dans la base:\n";
$allWords = $badWordController->getAllBadWords();
foreach ($allWords as $word) {
    echo "  - " . htmlspecialchars($word['word']) . "\n";
}

echo "</pre>";
echo "<p><a href='javascript:history.back()'>← Retour</a></p>";

