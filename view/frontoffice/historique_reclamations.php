<?php
session_start();

// Require controller to fetch user's reclamations
require_once __DIR__ . '/../../controller/ReclamationController.php';
require_once __DIR__ . '/../../controller/utilisateurcontroller.php';
require_once __DIR__ . '/../../controller/ResponseController.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$reclamationController = new ReclamationController();
$responseController = new ResponseController();
$utilCtrl = new UtilisateurController();

$reclamations = $reclamationController->getReclamationsByUser($user_id);

$headerShowUserMenu = true; // instruct header to show dropdown
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des Réclamations - ENGAGE</title>
    <link rel="icon" href="assets/img/favicon.png">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/all.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/custom-frontoffice.css">
    <style>
        :root {
            --primary: #ff4a57;
            --primary-light: #ff6b6b;
            --dark: #1f2235;
            --dark-light: #2d325a;
            --text: #ffffff;
            --text-light: rgba(255,255,255,0.8);
        }

        .body_bg {
            background: linear-gradient(135deg, var(--dark) 0%, var(--dark-light) 100%);
            min-height: 100vh;
        }

        .user-menu {
            position: relative;
            display: inline-block;
        }
        
        .user-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            min-width: 220px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            border-radius: 12px;
            z-index: 1000;
            margin-top: 10px;
            overflow: hidden;
        }

        .user-dropdown.show {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        .user-dropdown a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            text-decoration: none;
            color: #333;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .user-dropdown a:hover {
            background: #f8f9fa;
            color: var(--primary);
            transform: translateX(5px);
        }

        .user-dropdown a:last-child {
            border-bottom: none;
            color: #dc3545;
        }

        .user-dropdown a:last-child:hover {
            background: #dc3545;
            color: white;
        }

        .user-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
            cursor: pointer;
            padding: 8px 16px;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .user-wrapper:hover {
            background: rgba(255,255,255,0.1);
        }

        .user-name {
            font-weight: 600;
            font-size: 14px;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid rgba(255,255,255,0.3);
            transition: all 0.3s ease;
        }

        .user-avatar:hover {
            border-color: rgba(255,255,255,0.6);
            transform: scale(1.05);
        }

        .user-avatar i {
            color: white;
            font-size: 20px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .timeline { display:flex; flex-direction:column; gap:18px; }
        .timeline-item { background: rgba(255,255,255,0.03); border-radius:8px; padding:18px; position:relative; box-shadow: 0 6px 18px rgba(0,0,0,0.35); border:1px solid rgba(255,255,255,0.05); }
        .timeline-item.pending { border-color: rgba(255,199,0,0.35); box-shadow: 0 6px 24px rgba(255,199,0,0.22); }
        .timeline-marker { width:8px; height:8px; background:var(--primary); border-radius:50%; position:absolute; left:-18px; top:18px; }
        .timeline-date { color: var(--primary); font-weight:700; font-size:0.9rem; }
        .timeline-title { font-weight:700; margin-top:6px; color: #fff; }
        .timeline-description { margin-top:10px; color: var(--text-light); }
            .responses { margin-top: 8px; }
            .response-card { background: rgba(255,255,255,0.02); padding:10px; border-left:3px solid var(--primary); margin-bottom:8px; border-radius:6px; }
        .status-chip { display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border-radius:20px; font-size:0.85rem; font-weight:700; letter-spacing:0.01em; }
        .status-answered { background: rgba(56,213,152,0.18); color:#38d998; border:1px solid rgba(56,213,152,0.35); }
        .status-pending { background: rgba(255,199,0,0.18); color:#ffd166; border:1px solid rgba(255,199,0,0.4); }
        .status-progress { background: rgba(82,156,255,0.18); color:#92c5ff; border:1px solid rgba(82,156,255,0.35); }
    </style>
</head>
<body class="body_bg">

<?php include __DIR__ . '/header_common.php'; ?>

<section class="profile-section" style="padding: 100px 0;">
    <div class="container">
        <div class="section-title">
            <h2>📝 Historique de mes Réclamations</h2>
            <p>Suivez les réclamations que vous avez envoyées à la plateforme</p>
        </div>

        <div class="candidatures-container">
        <?php if (!empty($reclamations)): ?>
            <div class="timeline">
                <?php foreach ($reclamations as $reclamation): ?>
                    <?php
                        $responses = $responseController->getResponses($reclamation['id']);
                        $hasResponses = !empty($responses);
                        $statut = $reclamation['statut'] ?? 'Non traite';
                        $statusClass = 'status-pending';
                        $statusIcon = '⏳';
                        if ($statut === 'Traite' || $hasResponses) {
                            $statusClass = 'status-answered';
                            $statusIcon = '✔️';
                        } elseif ($statut === 'En cours') {
                            $statusClass = 'status-progress';
                            $statusIcon = '🔄';
                        }
                        $itemClass = ($statut === 'Non traite' && !$hasResponses) ? ' pending' : '';
                    ?>
                    <div class="timeline-item<?= $itemClass; ?>" id="rec-<?= (int)$reclamation['id']; ?>">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <div class="timeline-date">
                                <?php echo date('d/m/Y à H:i', strtotime($reclamation['date_creation'])); ?>
                            </div>
                            <div class="timeline-title">
                                <?php echo htmlspecialchars($reclamation['sujet']); ?>
                            </div>
                            <div class="timeline-description">
                                <div style="display:flex; gap:12px; align-items:center; margin-bottom:8px;">
                                    <span class="status-chip <?= $statusClass; ?>"><?php echo $statusIcon; ?> <?php echo htmlspecialchars($statut); ?></span>
                                    <span style="font-size:0.85rem; color: #ddd;">
                                        <i class="fas fa-flag"></i> Priorité: <?php echo htmlspecialchars($reclamation['priorite']); ?>
                                    </span>
                                </div>
                                <p><?php echo nl2br(htmlspecialchars($reclamation['description'])); ?></p>

                                <?php if (!empty($responses)): ?>
                                    <div class="responses" style="margin-top:12px;">
                                        <?php foreach ($responses as $resp): ?>
                                            <?php
                                                $adminName = 'Administrateur';
                                                if (!empty($resp['admin_id'])) {
                                                    $adminInfo = $utilCtrl->showUtilisateur($resp['admin_id']);
                                                    if ($adminInfo) {
                                                        $adminName = trim(($adminInfo['prenom'] ?? '') . ' ' . ($adminInfo['nom'] ?? '')) ?: $adminName;
                                                    }
                                                }
                                            ?>
                                            <div class="response-card" style="background: rgba(255,255,255,0.02); padding:10px; border-left:3px solid var(--primary); margin-bottom:8px; border-radius:6px;">
                                                <div style="font-size:0.85rem; color:var(--text-light); margin-bottom:6px;">
                                                    <strong><?php echo htmlspecialchars($adminName); ?></strong>
                                                    <span style="color:rgba(255,255,255,0.6); font-weight:600; margin-left:8px;">• <?php echo date('d/m/Y H:i', strtotime($resp['date_response'])); ?></span>
                                                </div>
                                                <div style="color:var(--text-light); font-size:0.95rem;">
                                                    <?php echo nl2br(htmlspecialchars($resp['contenu'])); ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-candidatures">
                <i class="fas fa-history"></i>
                <h3>Aucune réclamation pour le moment</h3>
                <p>Envoyez une réclamation depuis la page Réclamation pour qu'elle apparaisse ici.</p>
            </div>
        <?php endif; ?>
    </div>

        <footer class="footer-area" style="margin-top:40px;">
            <div class="container text-center text-white">
                <p style="margin:0; padding:30px 0;">
                    © 2025 ENGAGE Platform – Développé par les phenomenes
                </p>
            </div>
        </footer>
    </div>
</section>

<script src="assets/js/jquery-1.12.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
