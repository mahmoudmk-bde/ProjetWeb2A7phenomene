<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Modifier Partenaire - Engage Backoffice</title>
    <link rel="icon" href="<?php echo BASE_URL; ?>view/frontoffice/assets/img/favicon.png" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/backoffice/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/backoffice/assets/css/all.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/backoffice/assets/css/custom-backoffice.css" />
</head>

<body>
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3>
                <img src="<?php echo BASE_URL; ?>view/backoffice/assets/img/logo.png" alt="logo" style="height: 40px;" /> 
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
                    <li class="active">
                        <a href="?controller=AdminPartenaire&action=index">
                            <i class="fas fa-handshake"></i> Partenaires
                        </a>
                    </li>
                    <li>
                        <a href="?controller=AdminStore&action=index">
                            <i class="fas fa-store"></i> Store Items
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
                <h2><i class="fas fa-edit text-primary"></i> Modifier Partenaire</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb" style="background: transparent; padding: 0;">
                        <li class="breadcrumb-item"><a href="?controller=AdminPartenaire&action=index" style="color: var(--primary-color);">Partenaires</a></li>
                        <li class="breadcrumb-item active" style="color: var(--text-muted);">Modifier #<?= $this->partenaire->id ?></li>
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

            <!-- Form Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Informations du Partenaire</h5>
                </div>
                <div class="card-body">
                    <form action="?controller=AdminPartenaire&action=update" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= $this->partenaire->id ?>">
                        
                        <div class="row">
                            <!-- Nom -->
                            <div class="col-md-6 mb-3">
                                <label for="nom" class="form-label">
                                    <i class="fas fa-building"></i> Nom du Partenaire <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="nom" 
                                       name="nom" 
                                       value="<?= htmlspecialchars($this->partenaire->nom) ?>" 
                                       required>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope"></i> Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       value="<?= htmlspecialchars($this->partenaire->email) ?>" 
                                       required>
                            </div>

                            <!-- Type -->
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">
                                    <i class="fas fa-tag"></i> Type <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="type" name="type" required>
                                    <option value="">Sélectionner un type</option>
                                    <option value="sponsor" <?= $this->partenaire->type === 'sponsor' ? 'selected' : '' ?>>Sponsor</option>
                                    <option value="testeur" <?= $this->partenaire->type === 'testeur' ? 'selected' : '' ?>>Testeur</option>
                                    <option value="vendeur" <?= $this->partenaire->type === 'vendeur' ? 'selected' : '' ?>>Vendeur</option>
                                </select>
                            </div>

                            <!-- Statut -->
                            <div class="col-md-6 mb-3">
                                <label for="statut" class="form-label">
                                    <i class="fas fa-circle"></i> Statut
                                </label>
                                <select class="form-control" id="statut" name="statut">
                                    <option value="en_attente" <?= $this->partenaire->statut === 'en_attente' ? 'selected' : '' ?>>En attente</option>
                                    <option value="actif" <?= $this->partenaire->statut === 'actif' ? 'selected' : '' ?>>Actif</option>
                                    <option value="inactif" <?= $this->partenaire->statut === 'inactif' ? 'selected' : '' ?>>Inactif</option>
                                </select>
                            </div>

                            <!-- Téléphone -->
                            <div class="col-md-6 mb-3">
                                <label for="telephone" class="form-label">
                                    <i class="fas fa-phone"></i> Téléphone
                                </label>
                                <input type="tel" 
                                       class="form-control" 
                                       id="telephone" 
                                       name="telephone" 
                                       value="<?= htmlspecialchars($this->partenaire->telephone ?? '') ?>"
                                       placeholder="+216 XX XXX XXX">
                            </div>

                            <!-- Site Web -->
                            <div class="col-md-6 mb-3">
                                <label for="site_web" class="form-label">
                                    <i class="fas fa-globe"></i> Site Web
                                </label>
                                <input type="url" 
                                       class="form-control" 
                                       id="site_web" 
                                       name="site_web" 
                                       value="<?= htmlspecialchars($this->partenaire->site_web ?? '') ?>" 
                                       placeholder="https://example.com">
                            </div>

                            <!-- Logo -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-image"></i> Logo
                                </label>
                                
                                <?php if ($this->partenaire->logo): ?>
                                    <div class="mb-3">
                                        <p style="color: var(--text-muted);">Logo actuel :</p>
                                        <img src="<?php echo BASE_URL . htmlspecialchars($this->partenaire->logo); ?>" 
                                             alt="Logo actuel" 
                                             class="partner-logo"
                                             style="max-width: 150px;">
                                        <p class="mt-2" style="color: var(--text-muted); font-size: 13px;">
                                            <i class="fas fa-info-circle"></i> 
                                            Uploadez un nouveau fichier pour remplacer ce logo
                                        </p>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="custom-file">
                                    <input type="file" 
                                           class="custom-file-input" 
                                           id="logo" 
                                           name="logo" 
                                           accept="image/*">
                                    <label class="custom-file-label" for="logo">
                                        <?= $this->partenaire->logo ? 'Changer le logo...' : 'Choisir un fichier...' ?>
                                    </label>
                                </div>
                                <small class="form-text" style="color: var(--text-muted);">
                                    Formats acceptés: JPG, PNG, GIF, WEBP (Max: 2MB)
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
                                          rows="4"
                                          placeholder="Décrivez le partenaire, ses activités, sa mission..."><?= htmlspecialchars($this->partenaire->description ?? '') ?></textarea>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="?controller=AdminPartenaire&action=index" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Retour à la liste
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer les modifications
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
    
    <script>
        // Custom file input label
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName || 'Choisir un fichier...');
        });
    </script>
</body>
</html>
