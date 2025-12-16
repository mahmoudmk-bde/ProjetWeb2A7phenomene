<?php
// OAUTH CONFIGURATION
// Remplacez les valeurs ci-dessous par vos propres clés API

$oauth_config = [
    'google' => [
        'client_id'     => 'VOTRE_GOOGLE_CLIENT_ID',
        'client_secret' => 'VOTRE_GOOGLE_CLIENT_SECRET',
        'redirect_uri'  => 'http://localhost/tache%20utilisateur/controller/social_auth.php?provider=google'
    ],
    'facebook' => [
        'app_id'        => 'VOTRE_FACEBOOK_APP_ID',
        'app_secret'    => 'VOTRE_FACEBOOK_APP_SECRET',
        'redirect_uri'  => 'http://localhost/tache%20utilisateur/controller/social_auth.php?provider=facebook'
    ],
    'twitter' => [
        'api_key'       => 'VOTRE_TWITTER_API_KEY',
        'api_secret'    => 'VOTRE_TWITTER_API_SECRET',
        'redirect_uri'  => 'http://localhost/tache%20utilisateur/controller/social_auth.php?provider=twitter'
    ]
];
?>
