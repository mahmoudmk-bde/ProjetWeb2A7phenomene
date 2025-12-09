<?php
session_start();
// Use forward slashes which PHP handles correctly on Windows
// From view/backoffice/reclamation/ we need to go up 3 levels to reach project root
$base_dir = str_replace('\\', '/', __DIR__) . '/../../../';
require_once $base_dir . 'controller/ReclamationController.php';
require_once $base_dir . 'controller/ResponseController.php';

$recCtrl = new ReclamationController();
$respCtrl = new ResponseController();
$list = $recCtrl->listReclamations();

// Message de succès après suppression (GET) ou après envoi de réponse (session flash)
$successMessage = null;
if (isset($_GET['deleted']) && $_GET['deleted'] == 1) {
    $successMessage = 'Réclamation supprimée avec succès !';
}
if (isset($_SESSION['flash_success'])) {
    $successMessage = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Réclamations - ENGAGE Admin</title>
    
    <link rel="stylesheet" href="../assets/css/custom-backoffice.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        body {
            background: var(--secondary-color);
            padding: 20px;
            margin: 0;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .stats-header {
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
        
        
        .table-container {
            background: var(--accent-color);
            padding: 25px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        .table-modern {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
            color: var(--text-color);
        }
        
        .table-modern thead th {
            background: linear-gradient(45deg, var(--primary-color), var(--primary-light));
            border: none;
            color: white;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.85rem;
            padding: 15px;
            text-align: center;
            letter-spacing: 0.5px;
        }
        
        .table-modern tbody tr {
            background: var(--secondary-color);
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .table-modern tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 74, 87, 0.3);
        }
        
        .table-modern td {
            padding: 15px;
            border: none;
            text-align: center;
            vertical-align: middle;
            color: var(--text-color);
        }
        
        .email-info {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .email-info i {
            color: var(--primary-color);
        }
        
        .badge {
            border-radius: 15px;
            padding: 6px 12px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
            text-transform: capitalize;
        }
        
        .badge-success { background: var(--success); color: white; }
        .badge-warning { background: var(--warning); color: #212529; }
        .badge-danger { background: var(--danger); color: white; }
        .badge-info { background: var(--info); color: white; }
        
        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn-view, .btn-edit, .btn-delete {
            padding: 8px 15px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .btn-view {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
        }
        
        .btn-edit {
            background: linear-gradient(45deg, #ffc107, #e0a800);
            color: #212529;
        }
        
        .btn-delete {
            background: linear-gradient(45deg, #dc3545, #c82333);
            color: white;
        }
        
        .btn-view:hover, .btn-edit:hover, .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }
        
        .empty-state i {
            font-size: 4rem;
            color: var(--primary-color);
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .empty-state h3 {
            color: var(--text-color);
            margin-bottom: 10px;
        }
        
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
        
        @media (max-width: 768px) {
            
            .table-modern {
                font-size: 0.85rem;
            }
            
            .table-modern thead th,
            .table-modern td {
                padding: 10px 5px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn-view, .btn-edit, .btn-delete {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="stats-header">
            <h2 class="section-title">
            <i class="fas fa-exclamation-circle"></i> Gestion des Réclamations
        </h2>
    </div>

        <!-- Message de succès -->
        <?php if ($successMessage): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?= htmlspecialchars($successMessage) ?></span>
            </div>
        <?php endif; ?>

        <!-- Tableau des réclamations -->
    <div class="table-container">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Sujet</th>
                    <th>Email</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Réponses</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($list)): ?>
                <?php foreach ($list as $rec): ?>
                    <tr>
                        <td><?= htmlspecialchars($rec['id']) ?></td>
                            <td><?= htmlspecialchars($rec['sujet'] ?? 'N/A') ?></td>
                        <td class="email-info">
                            <i class="fas fa-envelope"></i>
                                <span><?= htmlspecialchars($rec['email'] ?? 'N/A') ?></span>
                        </td>
                        <td><?= date('d/m/Y à H:i', strtotime($rec['date_creation'] ?? 'now')) ?></td>
                        <td>
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
                        </td>
                        <td>
                            <?php
                                $responses = $respCtrl->getResponses($rec['id']);
                                $countResp = $responses ? count($responses) : 0;
                            ?>
                            <span class="badge badge-info"><?= $countResp ?></span>
                        </td>
                        <td class="action-buttons">
                            <a class="btn-view" href="response.php?id=<?= $rec['id'] ?>">
                                <i class="fas fa-reply"></i> Répondre
                            </a>
                            <a class="btn-edit" href="details.php?id=<?= $rec['id'] ?>">
                                <i class="fas fa-eye"></i> Voir
                            </a>
                            <a class="btn-delete" href="delete.php?id=<?= $rec['id'] ?>"
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réclamation ?')">
                                <i class="fas fa-trash"></i> Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <h3>Aucune réclamation trouvée</h3>
                            <p>Les réclamations apparaîtront ici dès que les utilisateurs en soumettront.</p>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
