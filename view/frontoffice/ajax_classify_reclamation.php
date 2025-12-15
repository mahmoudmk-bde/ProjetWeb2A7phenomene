<?php
/**
 * AJAX Endpoint for Real-Time AI Classification
 * Returns classification without saving to database
 */
header('Content-Type: application/json');

require_once __DIR__ . '/../../controller/ReclamationClassifier.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = $_POST['subject'] ?? '';
    $description = $_POST['description'] ?? '';
    
    if (empty($subject) && empty($description)) {
        echo json_encode([
            'error' => 'Subject or description required'
        ]);
        exit;
    }
    
    try {
        $classification = ReclamationClassifier::classify($subject, $description);
        echo json_encode($classification);
    } catch (Exception $e) {
        echo json_encode([
            'error' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'error' => 'POST request required'
    ]);
}
