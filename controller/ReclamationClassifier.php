<?php
/**
 * Smart Reclamation Classifier
 * Automatically detects category, priority, and suggested department
 * Based on rule-based NLP (expandable to ML/AI later)
 */
class ReclamationClassifier {
    
    // Category keywords mapping
    private static $categoryKeywords = [
        'payment' => [
            'keywords' => ['payment', 'paiement', 'payé', 'argent', 'money', 'charged', 'deducted', 'card', 'carte', 'transaction', 'facture', 'invoice', 'remboursement', 'refund', 'prix', 'price', 'tnd', 'dinar'],
            'weight' => 3
        ],
        'delivery' => [
            'keywords' => ['livraison', 'delivery', 'colis', 'package', 'shipping', 'reçu', 'received', 'not arrived', 'arrivé', 'retard', 'late', 'delayed'],
            'weight' => 3
        ],
        'technical' => [
            'keywords' => ['bug', 'error', 'erreur', 'crash', 'broken', 'cassé', 'ne fonctionne pas', 'not working', 'doesn\'t work', 'issue', 'problème technique', 'technical', 'code', 'système', 'system', 'login', 'connexion', 'password', 'mot de passe'],
            'weight' => 3
        ],
        'event' => [
            'keywords' => ['événement', 'event', 'participation', 'inscription', 'registration', 'date', 'heure', 'time', 'lieu', 'location', 'annulé', 'cancelled'],
            'weight' => 2
        ],
        'mission' => [
            'keywords' => ['mission', 'task', 'tâche', 'assignment', 'difficulté', 'difficulty', 'jeu', 'game', 'niveau', 'level'],
            'weight' => 2
        ],
        'store' => [
            'keywords' => ['store', 'boutique', 'produit', 'product', 'article', 'item', 'achat', 'purchase', 'commande', 'order'],
            'weight' => 2
        ],
        'partner' => [
            'keywords' => ['partenaire', 'partner', 'collaboration', 'organisation', 'sponsor', 'esport', 'esports'],
            'weight' => 2
        ],
        'hr' => [
            'keywords' => ['hr', 'rh', 'ressources humaines', 'human resources', 'staff', 'équipe', 'team', 'manager', 'admin', 'utilisateur', 'user', 'compte', 'account'],
            'weight' => 2
        ],
        'feedback' => [
            'keywords' => ['feedback', 'avis', 'commentaire', 'comment', 'suggestion', 'amélioration', 'improvement', 'rating', 'note'],
            'weight' => 1
        ]
    ];
    
    // Priority keywords mapping
    private static $priorityKeywords = [
        'urgent' => ['urgent', 'critical', 'critique', 'emergency', 'urgence', 'asap', 'immédiatement', 'immediately', 'bloqué', 'blocked', 'can\'t', 'ne peux pas', 'impossible', 'broken', 'cassé', 'perdu', 'lost'],
        'high' => ['important', 'high', 'élevé', 'serious', 'sérieux', 'problème', 'problem', 'issue', 'error', 'erreur', 'bug'],
        'medium' => ['question', 'demande', 'request', 'help', 'aide', 'besoin', 'need'],
        'low' => ['suggestion', 'idea', 'idée', 'amélioration', 'improvement', 'feedback', 'avis', 'maybe', 'peut-être']
    ];
    
    // Department mapping based on category
    private static $departmentMapping = [
        'payment' => 'Finance',
        'delivery' => 'Logistics',
        'technical' => 'IT Support',
        'event' => 'Event Management',
        'mission' => 'Mission Coordination',
        'store' => 'Sales & Commerce',
        'partner' => 'Partnership',
        'hr' => 'Human Resources',
        'feedback' => 'Customer Relations'
    ];
    
    /**
     * Main classification method
     * @param string $subject - The reclamation subject
     * @param string $description - The reclamation description
     * @return array ['category' => string, 'priority' => string, 'department' => string, 'confidence' => float]
     */
    public static function classify(string $subject, string $description): array {
        $text = strtolower($subject . ' ' . $description);
        
        $category = self::detectCategory($text);
        $priority = self::detectPriority($text);
        $department = self::getDepartment($category);
        $confidence = self::calculateConfidence($text, $category);
        
        return [
            'category' => $category,
            'priority' => $priority,
            'department' => $department,
            'confidence' => $confidence,
            'category_label' => self::getCategoryLabel($category),
            'priority_label' => self::getPriorityLabel($priority)
        ];
    }
    
    /**
     * Detect category from text
     */
    private static function detectCategory(string $text): string {
        $scores = [];
        
        foreach (self::$categoryKeywords as $category => $data) {
            $score = 0;
            foreach ($data['keywords'] as $keyword) {
                if (stripos($text, $keyword) !== false) {
                    $score += $data['weight'];
                }
            }
            $scores[$category] = $score;
        }
        
        arsort($scores);
        $topCategory = array_key_first($scores);
        
        // If no match found, default to 'general'
        if ($scores[$topCategory] == 0) {
            return 'general';
        }
        
        return $topCategory;
    }
    
    /**
     * Detect priority from text
     */
    private static function detectPriority(string $text): string {
        // Check from highest to lowest priority
        foreach (['urgent', 'high', 'medium', 'low'] as $priority) {
            foreach (self::$priorityKeywords[$priority] as $keyword) {
                if (stripos($text, $keyword) !== false) {
                    return $priority;
                }
            }
        }
        
        // Default priority
        return 'medium';
    }
    
    /**
     * Get suggested department based on category
     */
    private static function getDepartment(string $category): string {
        return self::$departmentMapping[$category] ?? 'General Support';
    }
    
    /**
     * Calculate confidence score (0-100)
     */
    private static function calculateConfidence(string $text, string $category): float {
        if ($category === 'general') {
            return 30.0;
        }
        
        $matchCount = 0;
        $totalKeywords = count(self::$categoryKeywords[$category]['keywords']);
        
        foreach (self::$categoryKeywords[$category]['keywords'] as $keyword) {
            if (stripos($text, $keyword) !== false) {
                $matchCount++;
            }
        }
        
        // Confidence based on keyword match ratio
        $confidence = ($matchCount / $totalKeywords) * 100;
        return min(95.0, max(40.0, $confidence * 1.5)); // Scale between 40-95%
    }
    
    /**
     * Get human-readable category label
     */
    private static function getCategoryLabel(string $category): string {
        $labels = [
            'payment' => 'Paiement',
            'delivery' => 'Livraison',
            'technical' => 'Technique',
            'event' => 'Événement',
            'mission' => 'Mission',
            'store' => 'Boutique',
            'partner' => 'Partenaire',
            'hr' => 'RH',
            'feedback' => 'Feedback',
            'general' => 'Général'
        ];
        return $labels[$category] ?? ucfirst($category);
    }
    
    /**
     * Get human-readable priority label
     */
    private static function getPriorityLabel(string $priority): string {
        $labels = [
            'urgent' => 'Urgent',
            'high' => 'Élevée',
            'medium' => 'Moyenne',
            'low' => 'Basse'
        ];
        return $labels[$priority] ?? ucfirst($priority);
    }
    
    /**
     * Get all available categories for form dropdowns
     */
    public static function getCategories(): array {
        return array_keys(self::$categoryKeywords);
    }
    
    /**
     * Get all available priorities
     */
    public static function getPriorities(): array {
        return ['urgent', 'high', 'medium', 'low'];
    }
}
