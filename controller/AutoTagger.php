<?php
/**
 * AutoTagger: AI-assisted tag extraction for reclamations
 * - Tries external AI API first (if configured)
 * - Falls back to keyword rules
 * - Returns array of normalized tags (lowercase, kebab-style, no spaces)
 */
class AutoTagger {
    /**
     * Attempt to fetch tags via external AI API.
     * Env: AI_TAG_API_URL, AI_TAG_API_KEY
     */
    private static function aiExtractTags(string $text): ?array {
        $url = getenv('AI_TAG_API_URL') ?: null;
        $key = getenv('AI_TAG_API_KEY') ?: null;
        if (!$url || !$key) return null;

        try {
            $payload = json_encode(['text' => $text, 'max_tags' => 6]);
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $key
                ],
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_TIMEOUT => 5
            ]);
            $resp = curl_exec($ch);
            if ($resp === false) return null;
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($code < 200 || $code >= 300) return null;
            $data = json_decode($resp, true);
            $tags = isset($data['tags']) && is_array($data['tags']) ? $data['tags'] : [];
            return self::normalizeTags($tags);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /** Normalize tags: lowercase, trim, spaces->dash, remove non-alnum-dash */
    public static function normalizeTag(string $tag): string {
        $t = strtolower(trim($tag));
        $t = preg_replace('/\s+/', '-', $t);
        $t = preg_replace('/[^a-z0-9\-]/', '', $t);
        return $t;
    }

    public static function normalizeTags(array $tags): array {
        $out = [];
        foreach ($tags as $t) {
            $n = self::normalizeTag((string)$t);
            if ($n !== '' && !in_array($n, $out, true)) $out[] = $n;
        }
        return $out;
    }

    /** Keyword-based fallback rules */
    private static function ruleTags(string $text): array {
        $t = strtolower($text);
        $tags = [];

        // Payment / refund / delivery
        if (preg_match('/refund|rembours|remboursement/', $t)) $tags[] = 'refund';
        if (preg_match('/payment|paiement|facture|invoice|card|carte|transaction|charged|deducted|prix|price|tnd|dinar/', $t)) $tags[] = 'payment';
        if (preg_match('/delay|retard|late|delayed/', $t)) $tags[] = 'delay';
        if (preg_match('/delivery|livraison|shipping|colis|package/', $t)) $tags[] = 'delivery';

        // Technical
        if (preg_match('/bug|error|erreur|crash|issue|ne fonctionne pas|not working|system|système|login|connexion|password|mot de passe/', $t)) {
            $tags[] = 'bug';
            $tags[] = 'technical';
        }
        if (preg_match('/system|système|server|serveur|database|db|sql/', $t)) $tags[] = 'system-error';

        // Sentiment / priority cues
        if (preg_match('/angry|furious|rage|insatisf|mécontent|colère/', $t)) $tags[] = 'angry-user';
        if (preg_match('/urgent|immédiat|asap|dès que possible|au plus vite/', $t)) $tags[] = 'urgent';

        // Data quality
        if (preg_match('/missing|absent|manquant|incomplet|lack/', $t)) $tags[] = 'missing-info';
        if (preg_match('/duplicate|doublon|dupliqué/', $t)) $tags[] = 'duplicate';

        // Domain-specific
        if (preg_match('/partner|partenaire|sponsor|organisation|esport|esports/', $t)) $tags[] = 'partner';
        if (preg_match('/mission|tâche|assignment|participation|inscription|event|événement/', $t)) $tags[] = 'mission-event';

        return self::normalizeTags($tags);
    }

    /** Main entry: returns array of tags */
    public static function extractTags(string $text): array {
        // Prefer external AI
        $ai = self::aiExtractTags($text);
        if (is_array($ai) && count($ai) > 0) return $ai;
        // Fallback rules
        return self::ruleTags($text);
    }
}

?>