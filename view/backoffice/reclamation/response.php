<?php
session_start();

// Use forward slashes which PHP handles correctly on Windows
$base_dir = str_replace('\\', '/', __DIR__) . '/../../../';
require_once $base_dir . 'controller/ReclamationController.php';
require_once $base_dir . 'controller/ResponseController.php';

if (!isset($_GET['id']) || !$_GET['id']) {
    header('Location: listReclamation.php'); 
    exit;
}

$id = intval($_GET['id']);
$recCtrl = new ReclamationController();
$rec = $recCtrl->getReclamation($id);

if (!$rec) { 
    header('Location: listReclamation.php'); 
    exit; 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['response']) && !empty(trim($_POST['response']))) {
    $respCtrl = new ResponseController();
    $respCtrl->addResponse($id, trim($_POST['response']));
    header('Location: listReclamation.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Répondre à la réclamation #<?= $rec['id'] ?> - ENGAGE Admin</title>
    
    <link rel="stylesheet" href="../assets/css/custom-backoffice.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        body {
            background: var(--secondary-color);
            padding: 20px;
            margin: 0;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .page-header {
            margin-bottom: 30px;
        }
        
        .page-header h2 {
            color: var(--text-color);
            font-size: 1.8rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .page-header h2 i {
            color: var(--primary-color);
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
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
            padding: 25px;
        }
        
        .reclamation-preview {
            background: var(--secondary-color);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid var(--primary-color);
        }
        
        .reclamation-preview p {
            margin-bottom: 15px;
            color: var(--text-color);
        }
        
        .reclamation-preview p:last-child {
            margin-bottom: 0;
        }
        
        .reclamation-preview strong {
            color: var(--primary-color);
            display: inline-block;
            min-width: 120px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            color: var(--text-color);
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .form-group textarea {
            width: 100%;
            background: var(--secondary-color);
            border: 1px solid var(--border-color);
            color: var(--text-color);
            padding: 15px;
            border-radius: 8px;
            font-size: 0.95rem;
            resize: vertical;
            min-height: 150px;
            font-family: inherit;
        }
        
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(255, 74, 87, 0.25);
        }
        
        .form-group small {
            color: var(--text-muted);
            font-size: 0.85rem;
            display: block;
            margin-top: 5px;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 25px;
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
            font-size: 0.95rem;
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
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        .info-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .info-item {
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-item strong {
            color: var(--text-muted);
            font-weight: 600;
        }
        
        .info-item span {
            color: var(--text-color);
        }
        
        .badge {
            border-radius: 15px;
            padding: 6px 12px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }
        
        .badge-success { background: var(--success); color: white; }
        .badge-warning { background: var(--warning); color: #212529; }
        .badge-danger { background: var(--danger); color: white; }
        .badge-info { background: var(--info); color: white; }
        
        @media (max-width: 968px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h2>
                <i class="fas fa-reply"></i> Répondre à la réclamation #<?= htmlspecialchars($rec['id']) ?>
            </h2>
        </div>

        <div class="content-grid">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-comment-dots"></i> Formulaire de réponse</h3>
                </div>
                <div class="card-body">
                    <div class="reclamation-preview">
                        <p><strong>Sujet:</strong> <?= htmlspecialchars($rec['sujet'] ?? 'N/A') ?></p>
                        <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($rec['description'] ?? 'N/A')) ?></p>
                        <p><strong>Email client:</strong> <?= htmlspecialchars($rec['email'] ?? 'N/A') ?></p>
                    </div>

                    <form method="POST" id="responseForm">
                        <div class="form-group">
                            <label for="response">Votre réponse</label>
                            <textarea id="response" name="response" rows="8" placeholder="Tapez votre réponse ici..."></textarea>
                            <small>Soyez courtois et professionnel dans votre réponse</small>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Envoyer la réponse
                            </button>
                            <a href="listReclamation.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Retour à la liste
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-info-circle"></i> Informations</h3>
                </div>
                <div class="card-body">
                    <ul class="info-list">
                        <li class="info-item">
                            <strong>ID Réclamation</strong>
                            <span>#<?= htmlspecialchars($rec['id']) ?></span>
                        </li>
                        <li class="info-item">
                            <strong>Statut</strong>
                            <?php
                                $statusClass = 'badge-info';
                                if ($rec['statut'] === 'Traite' || $rec['statut'] === 'Résolu') {
                                    $statusClass = 'badge-success';
                                } elseif ($rec['statut'] === 'En cours' || $rec['statut'] === 'En attente') {
                                    $statusClass = 'badge-warning';
                                } elseif ($rec['statut'] === 'Rejeté') {
                                    $statusClass = 'badge-danger';
                                }
                            ?>
                            <span class="badge <?= $statusClass ?>"><?= htmlspecialchars($rec['statut']) ?></span>
                        </li>
                        <li class="info-item">
                            <strong>Créée le</strong>
                            <span><?= date('d/m/Y à H:i', strtotime($rec['date_creation'] ?? 'now')) ?></span>
                        </li>
                        <li class="info-item">
                            <strong>Email</strong>
                            <span><?= htmlspecialchars($rec['email'] ?? 'N/A') ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
