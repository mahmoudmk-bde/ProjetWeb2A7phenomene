<?php
include '../../../controller/utilisateurcontroller.php';
$utilisateurc = new utilisateurcontroller();
$error = "";
$success = "";

if(isset($_GET['id'])) {
    $id_util = $_GET['id'];
    
    // nekhou les info mel base bch t'affichehom
    $utilisateur = $utilisateurc->showUtilisateur($id_util);
    
    if(!$utilisateur) {
        $error = "Utilisateur non trouvé";
    }
    
    // Si confirmation de suppression
    if(isset($_POST['confirm_delete'])) {
        try {
            $utilisateurc->deleteUtilisateur($id_util);
            $success = "Utilisateur supprimé avec succès!";
            header("refresh:2;url=listeutil.php");
        } catch (Exception $e) {
            $error = "Erreur lors de la suppression: " . $e->getMessage();
        }
    }
    
    // Sinon
    if(isset($_POST['cancel'])) {
        header("Location: listeutil.php");
        exit();
    }
} else {
    $error = "ID utilisateur non spécifié";
    header("Location: listeutil.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Supprimer Utilisateur - Backoffice</title>
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
            max-width: 600px; /* Smaller width for delete confirmation */
        }
    </style>
</head>
<body>
    
    <div id="content">
        <div class="container">
            <header class="header">
                <h1>Supprimer l'Utilisateur</h1>
            </header>

            <div class="confirmation-container">
                <div class="confirmation-card">
                    <?php if($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                        <a href="listeutil.php" class="btn btn-primary">Retour</a>
                    <?php elseif($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                        <p>Redirection vers le tableau de bord...</p>
                    <?php elseif(isset($utilisateur) && $utilisateur): ?>
                        <div class="confirmation-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        
                        <h2 class="text-danger">Confirmation de suppression</h2>
                        
                        <div class="alert alert-warning">
                            <strong>Attention!</strong> Vous êtes sur le point de supprimer définitivement cet utilisateur. Cette action est irréversible.
                        </div>
                        
                        <div class="user-info" style="background: var(--accent-color); padding: 20px; border-radius: 8px; margin: 20px 0;">
                            <h4>Informations de l'utilisateur :</h4>
                            <p><strong>ID :</strong> #<?php echo $utilisateur['id_util']; ?></p>
                            <p><strong>Nom complet :</strong> <?php echo htmlspecialchars($utilisateur['prenom']) . ' ' . htmlspecialchars($utilisateur['nom']); ?></p>
                            <p><strong>Email :</strong> <?php echo htmlspecialchars($utilisateur['mail']); ?></p>
                            <p><strong>Téléphone :</strong> <?php echo $utilisateur['num']; ?></p>
                            <p><strong>Type :</strong> 
                                <span class="badge <?php echo $utilisateur['typee'] == 'admin' ? 'badge-admin' : 'badge-user'; ?>">
                                    <?php echo htmlspecialchars($utilisateur['typee']); ?>
                                </span>
                            </p>
                        </div>
                        
                        <form method="POST">
                            <div class="confirmation-actions">
                                <button type="submit" name="confirm_delete" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Confirmer la suppression
                                </button>
                                <button type="submit" name="cancel" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Annuler
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>