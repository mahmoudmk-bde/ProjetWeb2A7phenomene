<?php
session_start();

require_once str_replace('\\', '/', __DIR__) . '/../../../controller/condidaturecontroller.php';

$condC = new condidaturecontroller();

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: listecondidature.php');
    exit;
}

$c = $condC->getCandidatureById($id);

if (!$c) {
    header('Location: listecondidature.php?error=Candidature introuvable');
    exit;
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['statut'])) {
    $condC->updateCandidatureStatus($id, $_POST['statut']);
    header('Location: listecondidature.php?updated=1');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Candidature #<?= htmlspecialchars($c['id']) ?> - ENGAGE Admin</title>
    
    <link rel="stylesheet" href="../assets/css/custom-backoffice.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        body {
            background: var(--secondary-color);
            padding: 20px;
            margin: 0;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .section-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-color);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-title i {
            color: var(--primary-color);
        }
        
        .card {
            background: var(--accent-color);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        
        .card-header {
            background: linear-gradient(45deg, var(--primary-color), var(--primary-light));
            color: white;
            padding: 20px;
            font-weight: 700;
        }
        
        .card-header h3 {
            margin: 0;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .card-body {
            padding: 30px;
        }
        
        .info-section {
            background: var(--secondary-color);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid var(--primary-color);
        }
        
        .info-item {
            margin-bottom: 15px;
            color: var(--text-color);
        }
        
        .info-item:last-child {
            margin-bottom: 0;
        }
        
        .info-item strong {
            color: var(--primary-color);
            display: inline-block;
            min-width: 150px;
            font-weight: 600;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            color: var(--text-color);
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 1rem;
        }
        
        .form-group label i {
            color: var(--primary-color);
            margin-right: 8px;
        }
        
        .form-control {
            width: 100%;
            background: var(--secondary-color);
            border: 1px solid var(--border-color);
            color: var(--text-color);
            padding: 12px 15px;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(255, 74, 87, 0.25);
        }
        
        .form-control:disabled {
            background: rgba(255, 255, 255, 0.05);
            cursor: not-allowed;
            opacity: 0.7;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid var(--border-color);
        }
        
        .btn {
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, var(--primary-color), var(--primary-light));
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 74, 87, 0.4);
        }
        
        .btn-secondary {
            background: var(--secondary-color);
            color: var(--text-color);
            border: 1px solid var(--border-color);
        }
        
        .btn-secondary:hover {
            background: var(--accent-color);
            transform: translateY(-2px);
        }
<<<<<<< HEAD

        /* Small button variant */
        .btn-sm {
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: inherit;
            background: transparent;
            border: 1px solid var(--border-color);
        }

        .btn-sm:hover {
            background: rgba(255,255,255,0.03);
            transform: translateY(-1px);
        }

        .btn-view {
            background: linear-gradient(90deg, rgba(0,123,255,0.12), rgba(0,123,255,0.06));
            color: #007bff;
            border-color: rgba(0,123,255,0.18);
        }
=======
>>>>>>> 6c1d02106b76736dc7ce843a7cf4f48a05d1ee1c
        
        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: capitalize;
        }
        
        .badge-en_attente {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid #ffc107;
        }
        
        .badge-acceptee {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid #28a745;
        }
        
        .badge-refusee {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 1px solid #dc3545;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .card-body {
                padding: 20px;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h2 class="section-title">
                <i class="fas fa-edit"></i> Modifier Candidature #<?= htmlspecialchars($c['id']) ?>
            </h2>
            <a href="listecondidature.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-user-check"></i> Informations de la Candidature</h3>
            </div>
            <div class="card-body">
                <!-- Informations de la candidature -->
                <div class="info-section">
                    <div class="info-item">
                        <strong><i class="fas fa-id-badge"></i> ID Candidature:</strong>
                        #<?= htmlspecialchars($c['id']) ?>
                    </div>
                    <div class="info-item">
                        <strong><i class="fas fa-user"></i> Volontaire:</strong>
                        <?= htmlspecialchars($c['pseudo_gaming'] ?? 'N/A') ?>
                    </div>
                    <div class="info-item">
                        <strong><i class="fas fa-briefcase"></i> Mission:</strong>
                        <?= htmlspecialchars($c['mission_titre'] ?? 'Mission #' . $c['mission_id']) ?>
                    </div>
                    <div class="info-item">
                        <strong><i class="fas fa-envelope"></i> Email:</strong>
                        <?= htmlspecialchars($c['email'] ?? 'N/A') ?>
                    </div>
                    <div class="info-item">
                        <strong><i class="fas fa-chart-line"></i> Niveau d'expérience:</strong>
                        <?= htmlspecialchars($c['niveau_experience'] ?? 'N/A') ?>
                    </div>
                    <div class="info-item">
                        <strong><i class="fas fa-calendar"></i> Disponibilités:</strong>
                        <?= htmlspecialchars($c['disponibilites'] ?? 'N/A') ?>
                    </div>
<<<<<<< HEAD
                    <?php if (isset($c['cv']) && !empty($c['cv'])):
                        // Normaliser le chemin stocké en DB et construire une URL depuis la racine du projet
                        $cv_rel = str_replace('\\', '/', $c['cv']); // remplace backslashes Windows
                        $cv_rel = ltrim($cv_rel, '/');
                        // Détecter dynamiquement la racine web du projet pour construire une URL correcte
                        // $_SERVER['DOCUMENT_ROOT'] ex: C:/xampp/htdocs
                        $docRoot = str_replace('\\', '/', rtrim($_SERVER['DOCUMENT_ROOT'], '/'));
                        $viewDir = str_replace('\\', '/', realpath(__DIR__ . '/../../../'));
                        $projectWebRoot = str_replace($docRoot, '', $viewDir);
                        $projectWebRoot = '/' . trim($projectWebRoot, '/');
                        if ($projectWebRoot === '/') { $projectWebRoot = ''; }
                        $cv_url = $projectWebRoot . '/' . $cv_rel;
                        $cv_url = '/' . ltrim($cv_url, '/');
                        // Utiliser l'endpoint CV pour affichage/suppression sûr
                        $cv_view_link = '/' . ltrim($projectWebRoot, '/') . '/cv.php?id=' . urlencode($c['id']) . '&mode=view';
                        $cv_download_link = '/' . ltrim($projectWebRoot, '/') . '/cv.php?id=' . urlencode($c['id']) . '&mode=download';
                    ?>
                    <div class="info-item">
                        <strong><i class="fas fa-file-pdf"></i> CV:</strong>
                        <a href="<?= htmlspecialchars($cv_view_link) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-view" style="padding: 6px 9px; font-size: 0.9rem; margin-left: 8px;">
                            <i class="fas fa-eye"></i> Voir le CV
                        </a>
                        <a href="<?= htmlspecialchars($cv_download_link) ?>" class="btn btn-sm" style="padding: 4px 8px; font-size: 0.9rem; margin-left: 6px;">
                            <i class="fas fa-download"></i> Télécharger
                        </a>
                    </div>
                    <?php endif; ?>
=======
>>>>>>> 6c1d02106b76736dc7ce843a7cf4f48a05d1ee1c
                    <div class="info-item">
                        <strong><i class="fas fa-info-circle"></i> Statut actuel:</strong>
                        <span class="badge badge-<?= $c['statut'] ?>">
                            <?php
                            switch($c['statut']) {
                                case 'en_attente':
                                    echo '⏳ En Attente';
                                    break;
                                case 'acceptee':
                                    echo '✅ Acceptée';
                                    break;
                                case 'rejetee':
                                    echo '❌ Refusée';
                                    break;
                                default:
                                    echo htmlspecialchars($c['statut']);
                            }
                            ?>
                        </span>
                    </div>
                    <?php if (isset($c['date_candidature'])): ?>
                    <div class="info-item">
                        <strong><i class="fas fa-clock"></i> Date de candidature:</strong>
                        <?= date('d/m/Y à H:i', strtotime($c['date_candidature'])) ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Formulaire de modification -->
                <form method="POST" action="">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($c['id']) ?>">

                    <div class="form-group">
                        <label for="statut">
                            <i class="fas fa-toggle-on"></i> Statut de la candidature
                        </label>
                        <select name="statut" id="statut" class="form-control" required>
                            <option value="en_attente" <?= ($c['statut'] == 'en_attente' || $c['statut'] == 'en attente') ? 'selected' : '' ?>>
                                ⏳ En attente
                            </option>
                            <option value="acceptee" <?= ($c['statut'] == 'acceptee' || $c['statut'] == 'accepte') ? 'selected' : '' ?>>
                                ✅ Acceptée
                            </option>
                            <option value="rejetee" <?= ($c['statut'] == 'rejetee' || $c['statut'] == 'refusee' || $c['statut'] == 'refuse') ? 'selected' : '' ?>>
                                ❌ Refusée
                            </option>
        </select>
                        <small style="color: var(--text-color); opacity: 0.7; margin-top: 5px; display: block;">
                            Sélectionnez le nouveau statut pour cette candidature
                        </small>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer les modifications
                        </button>
                        <a href="listecondidature.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    </div>
    </form>
</div>
        </div>
    </div>
</body>
</html>
