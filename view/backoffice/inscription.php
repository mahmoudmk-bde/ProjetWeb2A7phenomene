<?php
include '../../controller/utilisateurcontroller.php';
require_once __DIR__ . '/../../model/utilisateur.php';

$error = "";
$utilisateurc = new utilisateurcontroller();

    if (
        isset($_POST["prenom"]) && isset($_POST["nom"]) && isset($_POST["dt_naiss"]) && isset($_POST["mail"]) && isset($_POST["num"]) && isset($_POST["mdp"])
    ) {
    if (
      !empty($_POST["prenom"]) && !empty($_POST["nom"]) && !empty($_POST["dt_naiss"]) && !empty($_POST["mail"]) && !empty($_POST["num"]) && !empty($_POST["mdp"])
      ) {
        
        // Vérifier si l'email existe déjà
        if ($utilisateurc->emailExists($_POST['mail'])) {
            $error = "Cette adresse email est déjà utilisée";
        }
        // Vérifier si le numéro existe déjà
        else if ($utilisateurc->numExists($_POST['num'])) {
            $error = "Ce numéro de téléphone est déjà utilisé";
        }
        else {
           $u = new utilisateur(
    $_POST['prenom'],
    $_POST['nom'],
    $_POST['dt_naiss'],
    $_POST['mail'],
    $_POST['num'],
    $_POST['mdp'],
    "admin", // Hardcoded type
    "", // q1 (empty for admin via backoffice)
    "", // rp1
    "", // q2
    "", // rp2
);

$utilisateurc->addUtilisateur($u);

            // AJOUT
            header('Location: connexion.php');
            exit;
        }
    } else {
        $error = "Missing information";
    }
}
?>*
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inscription - ENGAGE</title>
  <link rel="stylesheet" href="assets/css/inscri.css">
  <script src="assets/js/inscription.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="particles-container" id="particles"></div>
  
  <div class="login-container">
    <h1>Inscription</h1>

    <!-- Afficher le message d'erreur -->
    <?php if (!empty($error)): ?>
        <div style="background: #ffebee; color: #c62828; padding: 10px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #ffcdd2; text-align: center;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="post" id="inscriptionForm">
  <div class="form-row">
    <div class="form-group">
      <input type="text" name="prenom" id="prenom" placeholder="Prénom" > <!-- pr → prenom -->
      <div class="error-message" id="error-prenom"></div>
    </div>
    <div class="form-group">
      <input type="text" name="nom" id="nom" placeholder="Nom" >
      <div class="error-message" id="error-nom"></div>
    </div>
  </div>

  <div class="form-group">
    <input type="date" name="dt_naiss" id="dt_naiss" placeholder="Date de naissance" > <!-- dt → dt_naiss -->
    <div class="error-message" id="error-dt_naiss"></div>
  </div>

  <div class="form-group">
    <input type="email" name="mail" id="mail" placeholder="Adresse email" >
    <div class="error-message" id="error-mail"></div>
  </div>

  <div class="form-group">
    <input type="tel" name="num" id="num" placeholder="Numéro de mobile"> <!-- tel → num -->
    <div class="error-message" id="error-num"></div>
  </div>

      <!-- Champ mot de passe avec œil -->
      <div class="form-group">
        <div class="password-container">
          <input type="password" name="mdp" id="mdp" placeholder="Mot de passe" >
          <button type="button" class="password-toggle" onclick="togglePassword('mdp')">
            <i class="far fa-eye"></i>
          </button>
        </div>
        <div class="error-message" id="error-mdp"></div>
      </div>
      <!-- Champ confirmation mot de passe avec œil -->
      <div class="form-group">
        <div class="password-container">
          <input type="password" name="cmdp" id="cmdp" placeholder="Confirmation du mot de passe" >
          <button type="button" class="password-toggle" onclick="togglePassword('cmdp')">
            <i class="far fa-eye"></i>
          </button>
        </div>
        <div class="error-message" id="error-cmdp"></div>
      </div>
      <div class="form-group">
    <div class="error-message" id="error-vo"></div>
</div>

      <!-- MODIFICATION: Retirer le lien autour du bouton et utiliser type="submit" -->
      <input type="submit" class="btn btn-inscription" value="S'INSCRIRE">
    </form>
    <p class="signup">Vous avez déjà un compte ?</p>
    <a href="connexion.php" class="btn btn-connexion">SE CONNECTER</a>
  </div>
</body>
</html>