<?php
session_start();

// Use forward slashes which PHP handles correctly on Windows
$base_dir = str_replace('\\', '/', __DIR__) . '/../../../';
require_once $base_dir . 'controller/ReclamationController.php';
require_once $base_dir . 'controller/ResponseController.php';
require_once $base_dir . 'db_config.php';

if (!isset($_GET['id']) || !$_GET['id']) {
    header('Location: listReclamation.php'); 
    exit;
}

$id = intval($_GET['id']);
$recCtrl = new ReclamationController();
$respCtrl = new ResponseController();

// Get admin ID
$adminId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 1;

// Handle status update (reject)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reject') {
    // Update status to 'En cours' (showing it's being processed)
    if ($recCtrl->updateStatus($id, 'En cours')) {
        // Create a rejection response notification
        $rejectionMessage = "Votre réclamation a été rejetée après examen.";
        if (!empty($_POST['rejection_reason'])) {
            $rejectionMessage = htmlspecialchars($_POST['rejection_reason']);
        }
        
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("INSERT INTO response (reclamation_id, contenu, date_response, admin_id) 
                               VALUES (:rec_id, :content, NOW(), :admin_id)");
        $stmt->execute([
            ':rec_id' => $id,
            ':content' => '[REJECTION] ' . $rejectionMessage,
            ':admin_id' => $adminId
        ]);
        
        $_SESSION['success_message'] = 'La réclamation a été rejetée avec succès et une notification a été envoyée à l\'utilisateur.';
        header('Location: details.php?id=' . $id);
        exit;
    } else {
        $_SESSION['error_message'] = 'Erreur lors du rejet de la réclamation.';
    }
}

// Handle status update (resolve)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'resolve') {
    if ($recCtrl->updateStatus($id, 'Traite')) {
        $_SESSION['success_message'] = 'La réclamation a été marquée comme traitée.';
        header('Location: details.php?id=' . $id);
        exit;
    } else {
        $_SESSION['error_message'] = 'Erreur lors de la résolution de la réclamation.';
    }
}


$rec = $recCtrl->getReclamation($id);
if (!$rec) { 
    header('Location: listReclamation.php'); 
    exit; 
}

$responses = $respCtrl->getResponses($id);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la réclamation #<?= $rec['id'] ?> - ENGAGE Admin</title>
    
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .page-header h2 {
            color: var(--text-color);
            font-size: 1.8rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
        }
        
        .page-header h2 i {
            color: var(--primary-color);
        }
        
        .header-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
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
        
        .btn-danger {
            background: linear-gradient(45deg, #dc3545, #c82333);
            color: white;
        }
        
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
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
        
        .detail-item {
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .detail-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .detail-item strong {
            color: var(--primary-color);
            display: block;
            margin-bottom: 8px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .detail-item p, .detail-item span {
            color: var(--text-color);
            font-size: 1rem;
            line-height: 1.6;
            margin: 0;
        }
        
        .detail-item .description {
            white-space: pre-wrap;
            word-wrap: break-word;
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
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease;
        }
        
        .alert-success {
            background: rgba(40, 167, 69, 0.2);
            border: 1px solid var(--success);
            color: var(--success);
        }
        
        .alert-danger {
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid var(--danger);
            color: var(--danger);
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .status-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }
        
        .btn-success {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }
        
        .btn-warning {
            background: linear-gradient(45deg, #ffc107, #e0a800);
            color: #212529;
        }
        
        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 193, 7, 0.4);
        }
        
        .responses-section {
            margin-top: 20px;
        }
        
        .response-item {
            background: var(--secondary-color);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid var(--primary-color);
        }
        
        .response-item:last-child {
            margin-bottom: 0;
        }
        
        .response-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .response-date {
            color: var(--text-muted);
            font-size: 0.85rem;
        }
        
        .response-content {
            color: var(--text-color);
            line-height: 1.6;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        
        .no-responses {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-muted);
        }
        
        .no-responses i {
            font-size: 3rem;
            color: var(--primary-color);
            opacity: 0.5;
            margin-bottom: 15px;
        }
        
        @media (max-width: 968px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .header-actions {
                width: 100%;
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
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <span><?= htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?></span>
                    </div>
                <?php endif; ?>
        
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?= htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?></span>
                    </div>
                <?php endif; ?>
        
        <div class="page-header">
            <h2>
                <i class="fas fa-eye"></i> Détails de la réclamation #<?= htmlspecialchars($rec['id']) ?>
            </h2>
            <div class="header-actions">
                <a href="response.php?id=<?= $rec['id'] ?>" class="btn btn-primary">
                    <i class="fas fa-reply"></i> Répondre
                </a>
                <a href="listReclamation.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>

        <div class="content-grid">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-file-alt"></i> Informations de la réclamation</h3>
                </div>
                <div class="card-body">
                    <div class="detail-item">
                        <strong>Sujet</strong>
                        <p><?= htmlspecialchars($rec['sujet'] ?? 'N/A') ?></p>
                    </div>
                    
                    <div class="detail-item">
                        <strong>Description</strong>
                        <p class="description"><?= nl2br(htmlspecialchars($rec['description'] ?? 'N/A')) ?></p>
                    </div>
                    
                    <div class="detail-item">
                        <strong>Email du client</strong>
                        <p><?= htmlspecialchars($rec['email'] ?? 'N/A') ?></p>
                    </div>
                    
                    <div class="detail-item">
                        <strong>Date de création</strong>
                        <p><?= date('d/m/Y à H:i', strtotime($rec['date_creation'] ?? 'now')) ?></p>
                    </div>
                    
                    <div class="responses-section">
                        <h4 style="color: var(--text-color); margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-comments"></i> Réponses (<?= count($responses) ?>)
                        </h4>
                        
                        <?php if (!empty($responses)): ?>
                            <?php foreach ($responses as $response): ?>
                                <div class="response-item">
                                    <div class="response-header">
                                        <span class="response-date">
                                            <i class="fas fa-clock"></i> <?= date('d/m/Y à H:i', strtotime($response['date_response'])) ?>
                                        </span>
                                    </div>
                                    <div class="response-content">
                                        <?= nl2br(htmlspecialchars($response['contenu'])) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-responses">
                                <i class="fas fa-inbox"></i>
                                <p>Aucune réponse pour le moment</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-info-circle"></i> Statut</h3>
                </div>
                <div class="card-body">
                    <ul class="info-list">
                        <li class="info-item">
                            <strong>ID</strong>
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
                            <strong>Total réponses</strong>
                            <span><?= count($responses) ?></span>
                        </li>
                    </ul>
                    
                    <?php if ($rec['statut'] !== 'En cours' && $rec['statut'] !== 'Traite'): ?>
                    <div class="status-actions">
                        <form method="POST" style="flex: 1;" onsubmit="return confirm('Êtes-vous sûr de vouloir marquer cette réclamation comme résolue ?');">
                            <input type="hidden" name="action" value="resolve">
                            <button type="submit" class="btn btn-success" style="width: 100%; justify-content: center;">
                                <i class="fas fa-check"></i> Marquer comme résolue
                            </button>
                        </form>
                    </div>
                    
                    <!-- Reject form with reason -->
                    <div style="margin-top: 15px;">
                        <form method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir rejeter cette réclamation ? L\'utilisateur en sera notifié.');">
                            <input type="hidden" name="action" value="reject">
                            <div style="margin-bottom: 10px;">
                                <label for="rejection_reason" style="display: block; margin-bottom: 5px; font-weight: 600; color: var(--text-color);">
                                    Raison du rejet (optionnel)
                                </label>
                                <textarea 
                                    id="rejection_reason"
                                    name="rejection_reason" 
                                    placeholder="Expliquez pourquoi cette réclamation est rejetée..." 
                                    style="width: 100%; min-height: 80px; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px; background: var(--secondary-color); color: var(--text-color); font-family: inherit; resize: vertical;">
                                </textarea>
                            </div>
                            <button type="submit" class="btn btn-warning" style="width: 100%; justify-content: center;">
                                <i class="fas fa-times"></i> Rejeter la réclamation
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>
                    
                    <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-color);">
                        <a href="delete.php?id=<?= $rec['id'] ?>" 
                           class="btn btn-danger" 
                           style="width: 100%; justify-content: center;"
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réclamation ? Cette action est irréversible.');">
                            <i class="fas fa-trash"></i> Supprimer la réclamation
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

