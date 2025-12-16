<?php
include '../../../controller/utilisateurcontroller.php';

$utilisateurc = new utilisateurcontroller();
$error = "";
$success = "";

//tejbed les info li bch tmodifehom
if(isset($_GET['id'])) {
    $id_util = $_GET['id'];
    $utilisateur = $utilisateurc->showUtilisateur($id_util);
    
    if(!$utilisateur) {
        $error = "Utilisateur non trouvé";
    }
} else {
    $error = "ID utilisateur non spécifié";
}

if(isset($_POST['submit'])) {
    // Validation des données
    if(empty($_POST['prenom']) || empty($_POST['nom']) || empty($_POST['dt_naiss']) || 
       empty($_POST['mail']) || empty($_POST['num']) || empty($_POST['typee'])) {
        $error = "Tous les champs sont obligatoires";
    } else {
        try {
            // Vérification de l'existance de l'email
            if($utilisateurc->emailExists($_POST['mail']) && $_POST['mail'] != $utilisateur['mail']) {
                $error = "Cet email est déjà utilisé par un autre utilisateur";
            } else {
                $updatedUtilisateur = new Utilisateur(
                    $_POST['prenom'],
                    $_POST['nom'],
                    $_POST['dt_naiss'],
                    $_POST['mail'],
                    $_POST['num'],
                    // ken mdp tbadlch khali l kdim
                    !empty($_POST['mdp']) ? password_hash($_POST['mdp'], PASSWORD_DEFAULT) : $utilisateur['mdp'],
                    $_POST['typee'],
                    $utilisateur['q1'] ?? '', 
                    $utilisateur['rp1'] ?? '', 
                    $utilisateur['q2'] ?? '', 
                    $utilisateur['rp2'] ?? '',
                    $utilisateur['img'] ?? null
                );
                
                $utilisateurc->updateUtilisateur($updatedUtilisateur, $id_util);
                $success = "Utilisateur modifié avec succès!";
                
                // maj des donnees
                $utilisateur = $utilisateurc->showUtilisateur($id_util);
            }
        } catch (Exception $e) {
            $error = "Erreur: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Utilisateur - Backoffice</title>
    <link rel="icon" href="img/favicon.png" />
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        /* Override admin.css for standalone page */
        #content {
            width: 100% !important;
            margin-left: 0 !important;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            width: 100%;
            max-width: 800px;
        }
    </style>
</head>
<body>

    
    <div id="content">
        <div class="container">
            <header class="header">
                <h1>Modifier l'Utilisateur</h1>
                
            </header>

            <div class="form-container">
                <h2 class="mb-4">Modifier les informations de l'utilisateur</h2>
                
                <?php if($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <?php if(isset($utilisateur) && $utilisateur): ?>
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prénom *</label>
                            <input type="text" name="prenom" class="form-control" 
                                   value="<?php echo htmlspecialchars($utilisateur['prenom']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom *</label>
                            <input type="text" name="nom" class="form-control" 
                                   value="<?php echo htmlspecialchars($utilisateur['nom']); ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Date de Naissance *</label>
                        <input type="date" name="dt_naiss" class="form-control" 
                               value="<?php echo date('Y-m-d', strtotime($utilisateur['dt_naiss'])); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" name="mail" class="form-control" 
                               value="<?php echo htmlspecialchars($utilisateur['mail']); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Téléphone *</label>
                        <input type="tel" name="num" class="form-control" 
                               value="<?php echo $utilisateur['num']; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mot de passe (laisser vide pour ne pas modifier)</label>
                        <input type="password" name="mdp" class="form-control" placeholder="Nouveau mot de passe">
                        <small class="text-info">Si vous ne souhaitez pas modifier le mot de passe, laissez ce champ vide.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Type *</label>
                        <select name="typee" class="form-control">
                            <option value="">Sélectionnez un type</option>
                            <option value="admin" <?php echo ($utilisateur['typee'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                            <option value="user" <?php echo ($utilisateur['typee'] == 'user') ? 'selected' : ''; ?>>Utilisateur</option>
                        </select>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" name="submit" class="btn btn-primary">Modifier l'utilisateur</button>
                        <a href="listeutil.php" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>