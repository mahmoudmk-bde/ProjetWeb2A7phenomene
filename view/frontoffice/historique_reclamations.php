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
        .timeline-item { background: rgba(255,255,255,0.03); border-radius:8px; padding:18px; position:relative; box-shadow: 0 6px 18px rgba(0,0,0,0.35); }
        .timeline-marker { width:8px; height:8px; background:var(--primary); border-radius:50%; position:absolute; left:-18px; top:18px; }
        .timeline-date { color: var(--primary); font-weight:700; font-size:0.9rem; }
        .timeline-title { font-weight:700; margin-top:6px; color: #fff; }
        .timeline-description { margin-top:10px; color: var(--text-light); }
            .responses { margin-top: 8px; }
            .response-card { background: rgba(255,255,255,0.02); padding:10px; border-left:3px solid var(--primary); margin-bottom:8px; border-radius:6px; }

        /* Highlight / focus styles for a specific reclamation */
        .reclamation-highlight {
            box-shadow: 0 8px 30px rgba(255,74,87,0.12), 0 0 0 4px rgba(255,74,87,0.06) inset;
            transform: translateY(-4px);
            border-radius: 10px;
            transition: box-shadow 300ms ease, transform 300ms ease;
        }
        @keyframes highlightPulse {
            0% { box-shadow: 0 8px 30px rgba(255,74,87,0.12); }
            50% { box-shadow: 0 12px 40px rgba(255,74,87,0.18); }
            100% { box-shadow: 0 8px 30px rgba(255,74,87,0.12); }
        }
        .reclamation-highlight.animate {
            animation: highlightPulse 1.8s ease-in-out 0s 2;
        }
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
                    <div id="reclamation-<?php echo htmlspecialchars($reclamation['id']); ?>" class="timeline-item">
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
                                    <span style="font-size:0.85rem; color: #ddd;">
                                        <strong>Statut:</strong> <?php echo htmlspecialchars($reclamation['statut']); ?>
                                    </span>
                                    <span style="font-size:0.85rem; color: #ddd;">
                                        <i class="fas fa-flag"></i> Priorité: <?php echo htmlspecialchars($reclamation['priorite']); ?>
                                    </span>
                                </div>
                                <p><?php echo nl2br(htmlspecialchars($reclamation['description'])); ?></p>

                                <?php
                                    // Fetch responses for this reclamation
                                    $responses = $responseController->getResponses($reclamation['id']);
                                ?>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // If reclamation_id provided in query string, scroll to and highlight it
    function getQueryParam(name) {
        const params = new URLSearchParams(window.location.search);
        return params.get(name);
    }

    const rid = getQueryParam('reclamation_id');
    if (rid) {
        const el = document.getElementById('reclamation-' + rid);
        if (el) {
            // Smooth scroll to element, center it
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            // Add highlight class
            el.classList.add('reclamation-highlight', 'animate');
            // Remove animate after it finishes, but keep subtle highlight for a few seconds
            setTimeout(() => el.classList.remove('animate'), 4000);
            // Remove highlight after 8 seconds
            setTimeout(() => el.classList.remove('reclamation-highlight'), 8000);
        }
    }
});
</script>
</body>
</html>
