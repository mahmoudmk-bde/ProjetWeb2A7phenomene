<?php
session_start();
require_once __DIR__ . '/../config/oauth_config.php';
require_once __DIR__ . '/utilisateurcontroller.php';

// Fonction helper pour les requêtes cURL
function http_request($url, $post_data = null, $headers = []) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if ($post_data) {
        curl_setopt($ch, CURLOPT_POST, true);
        if (is_array($post_data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        }
    }
    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    // Désactiver la vérification SSL pour localhost (développement uniquement)
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        die('Curl error: ' . curl_error($ch));
    }
    
    curl_close($ch);
    return json_decode($response, true);
}

$provider = $_GET['provider'] ?? null;
$code = $_GET['code'] ?? null;
$error = $_GET['error'] ?? null;

if (!$provider) {
    die("Provider manquant.");
}

// 1. Initialiser le login (Redirection vers le provider)
if (!$code) {
    // Si pas de credential, on lance le flow
    if ($provider === 'google') {
        $params = [
            'client_id'     => $oauth_config['google']['client_id'],
            'redirect_uri'  => $oauth_config['google']['redirect_uri'],
            'response_type' => 'code',
            'scope'         => 'email profile',
            'access_type'   => 'online'
        ];
        header('Location: https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params));
        exit;
    } 
    elseif ($provider === 'facebook') {
        $params = [
            'client_id'     => $oauth_config['facebook']['app_id'],
            'redirect_uri'  => $oauth_config['facebook']['redirect_uri'],
            'state'         => 'fb_auth_state',
            'scope'         => 'email,public_profile'
        ];
        header('Location: https://www.facebook.com/v12.0/dialog/oauth?' . http_build_query($params));
        exit;
    }
    elseif ($provider === 'twitter') {
        // Twitter OAuth 2.0 (PKCE ou simple confidential client)
        // Note: Twitter demande 'state' et 'code_challenge' pour plus de sécu, on fait au plus simple pour demo
        $_SESSION['twitter_state'] = bin2hex(random_bytes(16));
        $params = [
            'response_type' => 'code',
            'client_id'     => $oauth_config['twitter']['api_key'], // Twitter utilise Client ID ici
            'redirect_uri'  => $oauth_config['twitter']['redirect_uri'],
            'scope'         => 'tweet.read users.read offline.access',
            'state'         => $_SESSION['twitter_state'],
            'code_challenge'=> 'challenge', // Simplification, en prod utiliser vrai PKCE
            'code_challenge_method' => 'plain'
        ];
        header('Location: https://twitter.com/i/oauth2/authorize?' . http_build_query($params));
        exit;
    }
} 
// 2. Callback (Retour du provider avec le code)
else {
    $user_info = null;

    // GOOGLE CALLBACK
    if ($provider === 'google') {
        // Echanger le code contre un token
        $token_url = 'https://oauth2.googleapis.com/token';
        $token_data = [
            'code'          => $code,
            'client_id'     => $oauth_config['google']['client_id'],
            'client_secret' => $oauth_config['google']['client_secret'],
            'redirect_uri'  => $oauth_config['google']['redirect_uri'],
            'grant_type'    => 'authorization_code'
        ];
        
        $response = http_request($token_url, $token_data);
        
        if (isset($response['access_token'])) {
            // Récupérer les infos utilisateur
            $user_info_url = 'https://www.googleapis.com/oauth2/v1/userinfo?alt=json&access_token=' . $response['access_token'];
            $google_user = http_request($user_info_url);
            
            if ($google_user) {
                $user_info = [
                    'uid'       => $google_user['id'],
                    'email'     => $google_user['email'],
                    'first_name'=> $google_user['given_name'] ?? 'User',
                    'last_name' => $google_user['family_name'] ?? 'Google',
                    'picture'   => $google_user['picture'] ?? null
                ];
            }
        }
    }
    // FACEBOOK CALLBACK
    elseif ($provider === 'facebook') {
        // Echanger le code contre un token
        $token_url = 'https://graph.facebook.com/v12.0/oauth/access_token';
        $token_data = [
            'client_id'     => $oauth_config['facebook']['app_id'],
            'client_secret' => $oauth_config['facebook']['app_secret'],
            'redirect_uri'  => $oauth_config['facebook']['redirect_uri'],
            'code'          => $code
        ];
        
        $response = http_request($token_url, $token_data); // Note: GET request pour FB token
        
        if (isset($response['access_token'])) {
            // Récupérer les infos
            $user_info_url = 'https://graph.facebook.com/me?fields=id,name,email,first_name,last_name,picture&access_token=' . $response['access_token'];
            $fb_user = http_request($user_info_url);
            
            if ($fb_user) {
                $user_info = [
                    'uid'       => $fb_user['id'],
                    'email'     => $fb_user['email'] ?? $fb_user['id'] . '@facebook.com', // Fallback si pas d'email
                    'first_name'=> $fb_user['first_name'] ?? explode(' ', $fb_user['name'])[0],
                    'last_name' => $fb_user['last_name'] ?? 'Facebook',
                    'picture'   => $fb_user['picture']['data']['url'] ?? null
                ];
            }
        }
    }
    // TWITTER CALLBACK
    elseif ($provider === 'twitter') {
        $token_url = 'https://api.twitter.com/2/oauth2/token';
        $token_data = [
            'code'          => $code,
            'grant_type'    => 'authorization_code',
            'client_id'     => $oauth_config['twitter']['api_key'],
            'redirect_uri'  => $oauth_config['twitter']['redirect_uri'],
            'code_verifier' => 'challenge'
        ];
        
        // Basic Auth Header pour Twitter
        $auth_header = 'Basic ' . base64_encode($oauth_config['twitter']['api_key'] . ':' . $oauth_config['twitter']['api_secret']);
        $headers = [
            'Authorization: ' . $auth_header,
            'Content-Type: application/x-www-form-urlencoded'
        ];
        
        $response = http_request($token_url, $token_data, $headers);
        
        if (isset($response['access_token'])) {
            $user_info_url = 'https://api.twitter.com/2/users/me?user.fields=profile_image_url';
            $headers = ['Authorization: Bearer ' . $response['access_token']];
            $t_user = http_request($user_info_url, null, $headers);
            
            if (isset($t_user['data'])) {
                $data = $t_user['data'];
                // Twitter API v2 ne donne pas l'email facilement, on utilise un fake ou on demande permission spéciale
                $user_info = [
                    'uid'       => $data['id'],
                    'email'     => $data['username'] . '@twitter.com', 
                    'first_name'=> $data['name'],
                    'last_name' => '',
                    'picture'   => $data['profile_image_url'] ?? null
                ];
            }
        }
    }

    // TRAITEMENT FINAL
    if ($user_info) {
        $controller = new UtilisateurController();
        $user = $controller->loginOrRegisterSocial(
            $provider, 
            $user_info['uid'], 
            $user_info['email'], 
            $user_info['first_name'], 
            $user_info['last_name'], 
            $user_info['picture']
        );
        
        if ($user) {
            // Connexion réussie
            $_SESSION['user_id'] = $user['id_util'];
            $_SESSION['user_name'] = $user['prenom'] . ' ' . $user['nom'];
            $_SESSION['user_email'] = $user['mail'];
            $_SESSION['user_type'] = $user['typee'];
            $_SESSION['profile_picture'] = $user['img'];
            
            // Redirection
            if ($user['typee'] === 'admin') {
                header('Location: ../view/backoffice/admin.php');
            } else {
                header('Location: ../view/frontoffice/index1.php');
            }
            exit;
        } else {
            die("Erreur lors de la création/connexion de l'utilisateur.");
        }
    } else {
        echo "<pre>"; print_r($_GET); echo "</pre>";
        echo "<pre>"; print_r($response ?? 'No response'); echo "</pre>";
        die("Erreur d'authentification sociale. Veuillez vérifier vos clés API dans config/oauth_config.php.");
    }
}
?>
