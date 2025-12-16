<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
$ordersAssetsBase = (defined('BASE_URL') ? BASE_URL : '') . 'view/backoffice/assets/ordersotrepartenairesassets/';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Nouveau Jeu - Store Engage Backoffice</title>
    <link rel="icon" href="<?php echo BASE_URL; ?>view/frontoffice/assets/img/favicon.png" />
    <link rel="stylesheet" href="<?php echo $ordersAssetsBase; ?>css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?php echo $ordersAssetsBase; ?>css/all.css" />
    <link rel="stylesheet" href="<?php echo $ordersAssetsBase; ?>css/custom-backoffice.css" />
</head>

<body>
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3>
                <img src="<?php echo $ordersAssetsBase; ?>img/logo.png" alt="logo" style="height: 120px;" /> 
                ENGAGE
            </h3>
        </div>
        <ul class="list-unstyled components">
            <li>
                <a href="<?php echo BASE_URL; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            </li>
            <li class="active">
                <a href="#gamificationSubmenu" data-toggle="collapse" aria-expanded="true">
                    <i class="fas fa-gamepad"></i> Gamification
                </a>
                <ul class="collapse show list-unstyled" id="gamificationSubmenu">
                    <li>
                        <a href="?controller=AdminPartenaire&action=index">
                            <i class="fas fa-handshake"></i> Partenaires
                        </a>
                    </li>
                    <li class="active">
                        <a href="?controller=AdminStore&action=index">
                            <i class="fas fa-store"></i> Store Items
                        </a>
                    </li>
                    <li>
                        <a href="?controller=AdminOrder&action=index">
                            <i class="fas fa-shopping-cart"></i> Commandes
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>

    <!-- Page Content -->
    <div id="content">
        <div class="container-fluid">
            <!-- Header -->
            <div class="mb-4">
                <h2><i class="fas fa-plus-circle text-primary"></i> Ajouter un Jeu au Store</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb" style="background: transparent; padding: 0;">
                        <li class="breadcrumb-item"><a href="?controller=AdminStore&action=index" style="color: var(--primary-color);">Store</a></li>
                        <li class="breadcrumb-item active" style="color: var(--text-muted);">Nouveau Jeu</li>
                    </ol>
                </nav>
            </div>

            <!-- Errors -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <h5><i class="fas fa-exclamation-triangle"></i> Erreurs de validation</h5>
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <!-- Info Box -->
            <div class="card" style="background: var(--accent-color); border-left: 4px solid var(--primary-color);">
                <div class="card-body">
                    <h6 style="color: var(--primary-color); margin-bottom: 10px;">
                        <i class="fas fa-info-circle"></i> Information importante
                    </h6>
                    <p style="color: var(--text-muted); margin: 0; font-size: 14px;">
                        Assurez-vous que le partenaire est bien <strong>actif</strong> avant d'ajouter un jeu. 
                        Les jeux seront visibles dans le store public une fois créés.
                    </p>
                </div>
            </div>

            <!-- Form Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-gamepad"></i> Informations du Jeu</h5>
                </div>
                <div class="card-body">
                    <form action="../router.php?controller=AdminStore&action=store" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <!-- Nom du jeu -->
                            <div class="col-md-6 mb-3">
                                <label for="nom" class="form-label">
                                    <i class="fas fa-gamepad"></i> Nom du Jeu <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="nom" 
                                       name="nom" 
                                       value="<?= htmlspecialchars($old['nom'] ?? '') ?>" 
                                       
                                       placeholder="Ex: Call of Duty: Modern Warfare">
                                <small class="form-text" style="color: var(--text-muted);">Le nom complet du jeu vidéo</small>
                            </div>

                            <!-- Partenaire -->
                            <div class="col-md-6 mb-3">
                                <label for="partenaire_id" class="form-label">
                                    <i class="fas fa-handshake"></i> Partenaire <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="partenaire_id" name="partenaire_id">
                                    <option value="">Sélectionner un partenaire</option>
                                    <?php foreach ($partenaires as $partenaire): ?>
                                        <option value="<?= $partenaire['id'] ?>" 
                                                <?= ($old['partenaire_id'] ?? '') == $partenaire['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($partenaire['nom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="form-text" style="color: var(--text-muted);">Qui fournit ce jeu ?</small>
                            </div>

                            <!-- Prix -->
                            <div class="col-md-4 mb-3">
                                <label for="prix" class="form-label">
                                    <i class="fas fa-dollar-sign"></i> Prix <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control" 
                                           id="prix" 
                                           name="prix" 
                                           value="<?= htmlspecialchars($old['prix'] ?? '') ?>" 
                                           step="0.01" 
                                           min="0" 
                                           
                                           placeholder="59.99">
                                    <div class="input-group-append">
                                        <span class="input-group-text">DT</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Stock -->
                            <div class="col-md-4 mb-3">
                                <label for="stock" class="form-label">
                                    <i class="fas fa-box"></i> Stock <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control" 
                                       id="stock" 
                                       name="stock" 
                                       value="<?= htmlspecialchars($old['stock'] ?? '0') ?>" 
                                       min="0" 
                                       
                                       placeholder="100">
                                <small class="form-text" style="color: var(--text-muted);">Quantité disponible</small>
                            </div>

                            <!-- Âge minimum -->
                            <div class="col-md-4 mb-3">
                                <label for="age_minimum" class="form-label">
                                    <i class="fas fa-child"></i> Âge minimum
                                </label>
                                <select class="form-control" id="age_minimum" name="age_minimum">
                                    <option value="3" <?= ($old['age_minimum'] ?? '3') == '3' ? 'selected' : '' ?>>3+</option>
                                    <option value="7" <?= ($old['age_minimum'] ?? '') == '7' ? 'selected' : '' ?>>7+</option>
                                    <option value="12" <?= ($old['age_minimum'] ?? '') == '12' ? 'selected' : '' ?>>12+</option>
                                    <option value="16" <?= ($old['age_minimum'] ?? '') == '16' ? 'selected' : '' ?>>16+</option>
                                    <option value="18" <?= ($old['age_minimum'] ?? '') == '18' ? 'selected' : '' ?>>18+</option>
                                </select>
                            </div>

                            <!-- Catégorie -->
                            <div class="col-md-6 mb-3">
                                <label for="categorie" class="form-label">
                                    <i class="fas fa-tag"></i> Catégorie <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="categorie" name="categorie">
                                    <option value="">Sélectionner une catégorie</option>
                                    <option value="action" <?= ($old['categorie'] ?? '') === 'action' ? 'selected' : '' ?>>Action</option>
                                    <option value="aventure" <?= ($old['categorie'] ?? '') === 'aventure' ? 'selected' : '' ?>>Aventure</option>
                                    <option value="sport" <?= ($old['categorie'] ?? '') === 'sport' ? 'selected' : '' ?>>Sport</option>
                                    <option value="strategie" <?= ($old['categorie'] ?? '') === 'strategie' ? 'selected' : '' ?>>Stratégie</option>
                                    <option value="simulation" <?= ($old['categorie'] ?? '') === 'simulation' ? 'selected' : '' ?>>Simulation</option>
                                    <option value="rpg" <?= ($old['categorie'] ?? '') === 'rpg' ? 'selected' : '' ?>>RPG</option>
                                    <option value="educatif" <?= ($old['categorie'] ?? '') === 'educatif' ? 'selected' : '' ?>>Éducatif</option>
                                </select>
                            </div>

                            <!-- Plateforme -->
                            <div class="col-md-6 mb-3">
                                <label for="plateforme" class="form-label">
                                    <i class="fas fa-desktop"></i> Plateforme
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="plateforme" 
                                       name="plateforme" 
                                       value="<?= htmlspecialchars($old['plateforme'] ?? '') ?>"
                                       placeholder="Ex: PC, PS5, Xbox Series X">
                                <small class="form-text" style="color: var(--text-muted);">Séparez par des virgules si plusieurs</small>
                            </div>

                            <!-- Image du jeu -->
                            <div class="col-md-12 mb-3">
                                <label for="image" class="form-label">
                                    <i class="fas fa-image"></i> Image du Jeu
                                </label>
                                <div class="custom-file">
                                    <input type="file" 
                                           class="custom-file-input" 
                                           id="image" 
                                           name="image" 
                                           accept="image/*">
                                    <label class="custom-file-label" for="image">Choisir une image...</label>
                                </div>
                                <small class="form-text" style="color: var(--text-muted);">
                                    Formats acceptés: JPG, PNG, GIF, WEBP (Max: 5MB). Recommandé: 800x600px
                                </small>
                            </div>

                            <!-- Description -->
                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left"></i> Description
                                </label>
                                <textarea class="form-control" 
                                          id="description" 
                                          name="description" 
                                          rows="5"
                                          placeholder="Décrivez le jeu, son gameplay, son histoire..."><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                                <small class="form-text" style="color: var(--text-muted);">
                                    Une bonne description aide les utilisateurs à mieux comprendre le jeu
                                </small>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="../router.php?controller=AdminStore&action=index" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer le jeu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="<?php echo BASE_URL; ?>view/frontoffice/assets/js/jquery-1.12.1.min.js"></script>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/assets/js/popper.min.js"></script>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/assets/js/bootstrap.min.js"></script>
    <script src="<?php echo $ordersAssetsBase; ?>js/partenaire-form.js"></script>
    
    <script>
        // Custom file input label
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName || 'Choisir une image...');
        });
    </script>
</body>
</html>
