<?php
session_start();
// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

// Inclure les controllers
include '../../controller/utilisateurcontroller.php';
include '../../controller/condidaturecontroller.php';
include '../../controller/ReclamationController.php';

$utilisateurController = new UtilisateurController();
$condidatureController = new CondidatureController();
$reclamationController = new ReclamationController();

$user_id = $_SESSION['user_id'];
$current_user = $utilisateurController->showUtilisateur($user_id);

// S'assurer que les infos de session de base existent même si la session est ancienne
if ($current_user) {
    if (!isset($_SESSION['user_name'])) {
        $_SESSION['user_name'] = ($current_user['prenom'] ?? '') . ' ' . ($current_user['nom'] ?? '');
    }
    if (!isset($_SESSION['user_type'])) {
        // Valeur par défaut "user" si le type n'est pas défini
        $_SESSION['user_type'] = $current_user['typee'] ?? 'user';
    }
}

// Variables pratiques pour éviter les "undefined array key"
$sessionUserName = $_SESSION['user_name'] ?? (($current_user['prenom'] ?? '') . ' ' . ($current_user['nom'] ?? ''));
$sessionUserName = trim($sessionUserName) !== '' ? $sessionUserName : 'Utilisateur';
$sessionUserType = $_SESSION['user_type'] ?? ($current_user['typee'] ?? 'user');

// Récupérer les candidatures de l'utilisateur
$candidatures = $condidatureController->getCandidaturesByUser($user_id);
// Récupérer les réclamations de l'utilisateur
$reclamations = $reclamationController->getReclamationsByUser($user_id);

// Compter les statuts
$stats = [
    'en_attente' => 0,
    'accepte' => 0,
    'refuse' => 0,
    'total' => count($candidatures)
];

foreach ($candidatures as $candidature) {
    $statut = $candidature['statut'] ?? 'en_attente';
    
    // Mapper les valeurs de la base de données aux clés du tableau stats
    switch ($statut) {
        case 'en_attente':
            $stats['en_attente']++;
            break;
        case 'acceptee':
        case 'accepte':
            $stats['accepte']++;
            break;
        case 'rejetee':
        case 'refuse':
        case 'refusee':
            $stats['refuse']++;
            break;
        default:
            // Pour les autres statuts (annulee, etc.), on peut les ignorer ou les compter séparément
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ENGAGE - Mon Espace</title>
    <link rel="icon" href="assets/img/favicon.png">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/all.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        :root {
            --primary: #ff4a57;
            --primary-light: #ff6b6b;
            --dark: #1f2235;
            --dark-light: #2d325a;
            --text: #ffffff;
            --text-light: rgba(255,255,255,0.8);
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
        }

        .body_bg {
            background: linear-gradient(135deg, var(--dark) 0%, var(--dark-light) 100%);
            min-height: 100vh;
        }

        /* Header Styles */
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
            color: var(--danger);
        }
        
        .user-dropdown a:last-child:hover {
            background: var(--danger);
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

        /* Dashboard Styles */
        .welcome-section {
            text-align: center;
            padding: 80px 0 40px;
            background: linear-gradient(135deg, rgba(255,74,87,0.1) 0%, rgba(45,50,90,0.3) 100%);
            border-radius: 0 0 30px 30px;
            margin-bottom: 40px;
        }

        .welcome-title {
            font-size: 3rem;
            margin-bottom: 15px;
            color: white;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .welcome-subtitle {
            font-size: 1.3rem;
            color: var(--text-light);
            margin-bottom: 30px;
        }

        .user-badge {
            display: inline-block;
            background: rgba(255,74,87,0.2);
            color: var(--primary);
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            border: 2px solid var(--primary);
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
        }

        .stat-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }

        .stat-card.en_attente::before { background: var(--warning); }
        .stat-card.accepte::before { background: var(--success); }
        .stat-card.refuse::before { background: var(--danger); }
        .stat-card.total::before { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }

        .stat-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.8;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 10px;
            background: linear-gradient(135deg, var(--text) 0%, var(--text-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-label {
            color: var(--text-light);
            font-size: 1.1rem;
            font-weight: 500;
        }

        /* Quick Actions */
        .quick-actions {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
            margin: 40px 0;
        }

        .action-btn {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 18px 35px;
            border-radius: 15px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 5px 20px rgba(255, 74, 87, 0.3);
        }

        .action-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(255, 74, 87, 0.4);
            color: white;
            text-decoration: none;
        }

        .action-btn.secondary {
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
            box-shadow: none;
        }

        .action-btn.secondary:hover {
            background: var(--primary);
            color: white;
        }

        /* Candidatures Section */
        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title h2 {
            font-size: 2.5rem;
            color: white;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .section-title p {
            color: var(--text-light);
            font-size: 1.2rem;
        }

        .candidatures-container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 50px;
        }

        .candidature-card {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
            position: relative;
        }

        .candidature-card:hover {
            background: rgba(255, 255, 255, 0.07);
            transform: translateX(10px);
        }

        .candidature-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .mission-title {
            font-size: 1.4rem;
            color: white;
            font-weight: 600;
            margin: 0;
        }

        .statut-badge {
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .statut-en_attente {
            background: rgba(255, 193, 7, 0.2);
            color: var(--warning);
            border: 1px solid var(--warning);
        }

        .statut-accepte {
            background: rgba(40, 167, 69, 0.2);
            color: var(--success);
            border: 1px solid var(--success);
        }

        .statut-refuse {
            background: rgba(220, 53, 69, 0.2);
            color: var(--danger);
            border: 1px solid var(--danger);
        }

        .candidature-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-light);
        }

        .info-item i {
            color: var(--primary);
            width: 20px;
        }

        .candidature-date {
            color: var(--text-light);
            font-size: 0.9rem;
            text-align: right;
        }

        .no-candidatures {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-light);
        }

        .no-candidatures i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .no-candidatures h3 {
            color: white;
            margin-bottom: 15px;
        }

        /* Historique Section */
        .timeline {
            position: relative;
            padding: 20px 0;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--primary);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 30px;
            padding-left: 50px;
        }

        .timeline-marker {
            position: absolute;
            left: 12px;
            top: 0;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: var(--primary);
            border: 3px solid var(--dark);
        }

        .timeline-content {
            background: rgba(255, 255, 255, 0.03);
            padding: 20px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .timeline-date {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 5px;
        }

        .timeline-title {
            color: white;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .timeline-description {
            color: var(--text-light);
            font-size: 0.9rem;
        }

        /* Filtres */
        .filters {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--text-light);
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-btn.active,
        .filter-btn:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.6s ease;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .welcome-title {
                font-size: 2rem;
            }
            
            .quick-actions {
                flex-direction: column;
                align-items: center;
            }
            
            .action-btn {
                width: 100%;
                justify-content: center;
            }
            
            .candidature-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .candidature-date {
                text-align: left;
            }
        }
    </style>
</head>

<body>
<div class="body_bg">
    
    <?php $headerShowUserMenu = true; include 'header_common.php'; ?>

    <!-- Section de bienvenue -->
    <section class="welcome-section fade-in">
        <div class="container">
            <h1 class="welcome-title">Bon retour, <?php echo htmlspecialchars($sessionUserName); ?> ! 🎮</h1>
            <p class="welcome-subtitle">
                <?php 
                if ($sessionUserType === 'volontaire') {
                    echo "Suivez vos candidatures et découvrez de nouvelles missions passionnantes";
                } elseif ($sessionUserType === 'organisation') {
                    echo "Gérez vos missions et connectez-vous avec des volontaires talentueux";
                } else {
                    echo "Votre hub central pour l'aventure ENGAGE";
                }
                ?>
            </p>
            <div class="user-badge">
                <i class="fas fa-star me-2"></i>
                <?php echo ucfirst(htmlspecialchars($sessionUserType)); ?> ENGAGE
            </div>
        </div>
    </section>

    <!-- Statistiques des candidatures -->
    <section class="container fade-in">
        <div class="stats-grid">
            <div class="stat-card total">
                <div class="stat-icon">📊</div>
                <div class="stat-number"><?php echo $stats['total']; ?></div>
                <div class="stat-label">Candidatures Total</div>
            </div>
            <div class="stat-card en_attente">
                <div class="stat-icon">⏳</div>
                <div class="stat-number"><?php echo $stats['en_attente']; ?></div>
                <div class="stat-label">En Attente</div>
            </div>
            <div class="stat-card accepte">
                <div class="stat-icon">✅</div>
                <div class="stat-number"><?php echo $stats['accepte']; ?></div>
                <div class="stat-label">Acceptées</div>
            </div>
            <div class="stat-card refuse">
                <div class="stat-icon">❌</div>
                <div class="stat-number"><?php echo $stats['refuse']; ?></div>
                <div class="stat-label">Refusées</div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="quick-actions">
            <?php if ($sessionUserType === 'volontaire'): ?>
                <a href="missionlist.php" class="action-btn">
                    <i class="fas fa-search me-2"></i>Explorer les Missions
                </a>
                <a href="profile.php" class="action-btn secondary">
                    <i class="fas fa-user me-2"></i>Mon Profil
                </a>
                <a href="#historique" class="action-btn secondary">
                    <i class="fas fa-history me-2"></i>Mon Historique
                </a>
            <?php elseif ($sessionUserType === 'organisation'): ?>
                <a href="#" class="action-btn">
                    <i class="fas fa-plus me-2"></i>Créer une Mission
                </a>
                <a href="#" class="action-btn secondary">
                    <i class="fas fa-list me-2"></i>Mes Missions
                </a>
                <a href="profile.php" class="action-btn secondary">
                    <i class="fas fa-user me-2"></i>Mon Profil
                </a>
            <?php else: ?>
                <a href="missionlist.php" class="action-btn">
                    <i class="fas fa-search me-2"></i>Explorer les Missions
                </a>
                
            <?php endif; ?>
        </div>
    </section>

    <!-- Section Suivi des Candidatures -->
    <section class="container fade-in">
        <div class="section-title">
            <h2>📋 Mes Candidatures</h2>
            <p>Suivez l'état de vos postulations aux missions</p>
        </div>

        <!-- Filtres -->
        <div class="filters">
            <button class="filter-btn active" data-filter="all">Toutes (<?php echo $stats['total']; ?>)</button>
            <button class="filter-btn" data-filter="en_attente">En attente (<?php echo $stats['en_attente']; ?>)</button>
            <button class="filter-btn" data-filter="accepte">Acceptées (<?php echo $stats['accepte']; ?>)</button>
            <button class="filter-btn" data-filter="refuse">Refusées (<?php echo $stats['refuse']; ?>)</button>
        </div>

        <div class="candidatures-container">
            <?php if (!empty($candidatures)): ?>
                <?php foreach ($candidatures as $candidature): ?>
                    <div class="candidature-card" data-statut="<?php echo $candidature['statut']; ?>">
                        <div class="candidature-header">
                            <h3 class="mission-title"><?php echo htmlspecialchars($candidature['titre_mission'] ?? 'Mission #' . $candidature['id_mission']); ?></h3>
                            <span class="statut-badge statut-<?php echo $candidature['statut']; ?>">
                                <?php 
                                switch($candidature['statut']) {
                                    case 'en_attente': echo '⏳ En Attente'; break;
                                    case 'acceptee':
                                    case 'accepte': echo '✅ Acceptée'; break;
                                    case 'rejetee':
                                    case 'refuse':
                                    case 'refusee': echo '❌ Refusée'; break;
                                    default: echo htmlspecialchars($candidature['statut']);
                                }
                                ?>
                            </span>
                        </div>
                        
                        <div class="candidature-info">
                            <div class="info-item">
                                <i class="fas fa-gamepad"></i>
                                <span>Pseudo: <?php echo htmlspecialchars($candidature['pseudo_gaming']); ?></span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-chart-line"></i>
                                <span>Niveau: <?php echo htmlspecialchars($candidature['niveau_experience']); ?></span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-envelope"></i>
                                <span><?php echo htmlspecialchars($candidature['email']); ?></span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-calendar"></i>
                                <span>Disponibilités: <?php echo htmlspecialchars($candidature['disponibilites']); ?></span>
                            </div>
                        </div>

                        <div class="candidature-date">
                            <i class="fas fa-calendar me-2"></i>
                            Postulée le <?php echo date('d/m/Y à H:i', strtotime($candidature['date_candidature'] ?? 'now')); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-candidatures">
                    <i class="fas fa-file-alt"></i>
                    <h3>Aucune candidature pour le moment</h3>
                    <p>Explorez les missions disponibles et postulez à celles qui vous intéressent !</p>
                    <a href="missionlist.php" class="action-btn mt-3">
                        <i class="fas fa-search me-2"></i>Découvrir les Missions
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Section Historique des Candidatures -->
    <section id="historique" class="container fade-in" style="margin-top: 80px;">
        <div class="section-title">
            <h2>📈 Historique de mes Candidatures</h2>
            <p>Retracez l'évolution de vos participations aux missions</p>
        </div>

        <div class="candidatures-container">
            <?php if (!empty($candidatures)): ?>
                <div class="timeline">
                    <?php foreach ($candidatures as $candidature): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <div class="timeline-date">
                                    <?php echo date('d/m/Y à H:i', strtotime($candidature['date_candidature'] ?? 'now')); ?>
                                </div>
                                <div class="timeline-title">
                                    <?php echo htmlspecialchars($candidature['titre_mission'] ?? 'Mission #' . $candidature['id_mission']); ?>
                                </div>
                                <div class="timeline-description">
                                    <div style="display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 10px;">
                                        <span class="statut-badge statut-<?php echo $candidature['statut']; ?>" style="font-size: 0.8rem;">
                                            <?php 
                                            switch($candidature['statut']) {
                                                case 'en_attente': echo '⏳ En Attente'; break;
                                                case 'acceptee':
                                                case 'accepte': echo '✅ Acceptée'; break;
                                                case 'rejetee':
                                                case 'refuse':
                                                case 'refusee': echo '❌ Refusée'; break;
                                                default: echo htmlspecialchars($candidature['statut']);
                                            }
                                            ?>
                                        </span>
                                        <span style="color: var(--text-light);">
                                            <i class="fas fa-gamepad me-1"></i>
                                            <?php echo htmlspecialchars($candidature['pseudo_gaming']); ?>
                                        </span>
                                        <span style="color: var(--text-light);">
                                            <i class="fas fa-chart-line me-1"></i>
                                            <?php echo htmlspecialchars($candidature['niveau_experience']); ?>
                                        </span>
                                    </div>
                                    <p><strong>Disponibilités:</strong> <?php echo htmlspecialchars($candidature['disponibilites']); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-candidatures">
                    <i class="fas fa-history"></i>
                    <h3>Aucun historique de candidature</h3>
                    <p>Votre historique apparaîtra ici après vos premières candidatures.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Historique des Réclamations moved to separate page -->
    <section id="historique-reclamations" class="container fade-in" style="margin-top: 80px;">
        <div class="section-title">
            <h2>📝 Historique de mes Réclamations</h2>
            <p>La section Historique des réclamations a été déplacée vers une page dédiée.</p>
            <p><a href="historique_reclamations.php" class="action-btn">Voir mon historique des réclamations</a></p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-area">
        <div class="container text-center text-white">
            <p style="margin:0; padding:30px 0;">
                © 2025 ENGAGE Platform – Développé par les phenomenes
            </p>
        </div>
    </footer>

</div>

<script src="assets/js/jquery-1.12.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>

<script>
    // Fonction pour le menu utilisateur
    function toggleUserMenu() {
        const dropdown = document.getElementById('userDropdown');
        dropdown.classList.toggle('show');
    }
    
    document.addEventListener('click', function(event) {
        const userMenu = document.querySelector('.user-menu');
        const dropdown = document.getElementById('userDropdown');
        
        if (!userMenu.contains(event.target)) {
            dropdown.classList.remove('show');
        }
    });

    // Filtrage des candidatures
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Mettre à jour les boutons actifs
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Filtrer les candidatures
            document.querySelectorAll('.candidature-card').forEach(card => {
                if (filter === 'all' || card.getAttribute('data-statut') === filter) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // Animation au scroll
    document.addEventListener('DOMContentLoaded', function() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observer les éléments à animer
        document.querySelectorAll('.fade-in').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(el);
        });
    });
</script>

</body>
</html>