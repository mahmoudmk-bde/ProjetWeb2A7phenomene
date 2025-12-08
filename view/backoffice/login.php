<?php
session_start();
require_once __DIR__ . '/../../controller/utilisateurcontroller.php';

$utilCtrl = new UtilisateurController();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail = trim($_POST['mail'] ?? '');
    $mdp = trim($_POST['mdp'] ?? '');

    if ($mail === '' || $mdp === '') {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        $user = $utilCtrl->login($mail, $mdp);
        if ($user) {
            // Only allow admin type
            if (isset($user['typee']) && strtolower($user['typee']) === 'admin') {
                // Set session and redirect to dashboard
                $_SESSION['user_id'] = $user['id_util'];
                $_SESSION['user_name'] = trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?: 'Admin';
                $_SESSION['user_type'] = 'admin';

                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Accès réservé aux administrateurs.';
            }
        } else {
            $error = 'Email ou mot de passe incorrect.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backoffice - Connexion Admin</title>
    <link rel="stylesheet" href="assets/css/custom-backoffice.css">
    <style>
        body { background: #0f1114; color:#fff; }
        .login-box { max-width:420px; margin:80px auto; background:#0b0c0f; padding:28px; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.6); }
        .login-box h2 { margin-bottom:18px; }
        .form-control { background:#111218; border:1px solid #222; color:#fff; }
        .btn { background: linear-gradient(45deg,#ff4a57,#ff6b6b); border:none; }
        .alert { background: rgba(220,53,69,0.08); border:1px solid rgba(220,53,69,0.2); color:#ffb3b8; padding:10px; border-radius:6px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h2>Connexion Admin</h2>
            <?php if ($error): ?>
                <div class="alert"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label for="mail" class="form-label">Email</label>
                    <input type="email" name="mail" id="mail" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="mdp" class="form-label">Mot de passe</label>
                    <input type="password" name="mdp" id="mdp" class="form-control" required>
                </div>
                <button class="btn btn-primary w-100" type="submit">Se connecter</button>
            </form>
        </div>
    </div>
</body>
</html>
