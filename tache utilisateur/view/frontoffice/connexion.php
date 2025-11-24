<?php
session_start();
include '../../controller/utilisateurcontroller.php';
require_once __DIR__ . '/../../Model/utilisateur.php';

$error = "";
$utilisateurc = new utilisateurcontroller();

if (isset($_POST["username"]) && isset($_POST["password"])) {
    if (!empty($_POST["username"]) && !empty($_POST["password"])) {
        
        $username = $_POST["username"];
        $mdp = $_POST["password"];
        $sql = "";
        
        // Vérifier si c'est un email
        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            if ($utilisateurc->emailExists($username)) {
                $sql = "SELECT * FROM utilisateur WHERE mail = :identifiant AND mdp = :mdp";
            } else {
                $error = "Email non trouvé";
            }
        } 
        // Vérifier si c'est un numéro
        else if (is_numeric($username)) {
            if ($utilisateurc->numExists($username)) {
                $sql = "SELECT * FROM utilisateur WHERE num = :identifiant AND mdp = :mdp";
            } else {
                $error = "Numéro de téléphone non trouvé";
            }
        } else {
            $error = "Veuillez entrer un email ou un numéro de téléphone valide";
        }
        
        // verification s'il nya pas d'erreur
        if (!empty($sql) && empty($error)) {
            $db = config::getConnexion();
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':identifiant' => $username,
                ':mdp' => $mdp
            ]);
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Connexion réussie
                $_SESSION['user_id'] = $user['id_util'];
                $_SESSION['user_name'] = $user['prenom'] . ' ' . $user['nom'];
                $_SESSION['user_type'] = $user['typee'];
                $_SESSION['user_email'] = $user['mail'];
                
                // ouvrir index.html
                if ($user['typee'] === 'admin') {
                    header('Location: http://localhost/tache%20utilisateur/view/backoffice/admin.php');
                } else {
                    header('Location: index1.php');
                }
                exit();
            } else {
                $error = "Mot de passe incorrect";
            }
        }
    } else {
        $error = "Veuillez remplir tous les champs";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="assets/css/connexion.css">
</head>
<body>
  <div class="login-container">
    <h1>Se connecter</h1>

    <?php if (!empty($error)): ?>
        <div class="error-message" style="color: red; margin-bottom: 15px; text-align: center;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="post">
      <input type="text" name="username" id="nom" placeholder="Adresse e-mail ou numéro tél " required 
             value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">

      <input type="password" name="password" placeholder="Mot de passe" required>

      <a href="mdp.php" class="forgot">Mot de passe oubliée?</a>

      <input type="submit" value="Se connecter" class="btn">
    </form>

    <p class="signup">INSCRIVEZ-VOUS</p>
    <a href="inscription.php" class="signup-link">S'INSCRIRE</a>
  </div>
</body>
</html>