<?php
// Include Controller
require_once __DIR__ . '/../../../controller/utilisateurcontroller.php';

$utilisateurC = new UtilisateurController();
$utilisateurs = $utilisateurC->listUtilisateurs();

// Initialiser $allUsers avec un tableau vide par défaut
$allUsers = [];

if ($utilisateurs) {
    // Vérifier si c'est un objet PDOStatement et récupérer les données
    if ($utilisateurs instanceof PDOStatement) {
        $allUsers = $utilisateurs->fetchAll(PDO::FETCH_ASSOC);
    } elseif (is_array($utilisateurs)) {
        // Si c'est déjà un tableau
        $allUsers = $utilisateurs;
    }
}

// Pagination configuration
$itemsPerPage = 8; // Nombre d'utilisateurs par page
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$totalUsers = count($allUsers);
$totalPages = ceil($totalUsers / $itemsPerPage);

// Calcul des données pour la page courante
$startIndex = ($currentPage - 1) * $itemsPerPage;
$endIndex = min($startIndex + $itemsPerPage, $totalUsers);
$usersForCurrentPage = array_slice($allUsers, $startIndex, $itemsPerPage);

// Compter les utilisateurs pour les stats
$adminCount = 0;
$userCount = 0;

foreach ($allUsers as $user) {
    if (strtolower($user['typee']) == 'admin') {
        $adminCount++;
    } else {
        $userCount++;
    }
}

// Fonction pour générer les liens de pagination
function generatePaginationLinks($currentPage, $totalPages) {
    $links = '';
    
    // Bouton Précédent
    if ($currentPage > 1) {
        $prevPage = $currentPage - 1;
        $links .= '<li class="page-item"><a class="page-link" href="?page=' . $prevPage . '"><i class="fas fa-chevron-left"></i></a></li>';
    } else {
        $links .= '<li class="page-item disabled"><span class="page-link"><i class="fas fa-chevron-left"></i></span></li>';
    }
    
    // Affichage des numéros de page
    $maxVisiblePages = 5; // Nombre maximum de pages visibles dans la pagination
    $startPage = max(1, $currentPage - floor($maxVisiblePages / 2));
    $endPage = min($totalPages, $startPage + $maxVisiblePages - 1);
    
    // Ajuster le début si on est proche de la fin
    if ($endPage - $startPage + 1 < $maxVisiblePages) {
        $startPage = max(1, $endPage - $maxVisiblePages + 1);
    }
    
    // Première page
    if ($startPage > 1) {
        $links .= '<li class="page-item"><a class="page-link" href="?page=1">1</a></li>';
        if ($startPage > 2) {
            $links .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    // Pages numérotées
    for ($i = $startPage; $i <= $endPage; $i++) {
        if ($i == $currentPage) {
            $links .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
        } else {
            $links .= '<li class="page-item"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
        }
    }
    
    // Dernière page
    if ($endPage < $totalPages) {
        if ($endPage < $totalPages - 1) {
            $links .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        $links .= '<li class="page-item"><a class="page-link" href="?page=' . $totalPages . '">' . $totalPages . '</a></li>';
    }
    
    // Bouton Suivant
    if ($currentPage < $totalPages) {
        $nextPage = $currentPage + 1;
        $links .= '<li class="page-item"><a class="page-link" href="?page=' . $nextPage . '"><i class="fas fa-chevron-right"></i></a></li>';
    } else {
        $links .= '<li class="page-item disabled"><span class="page-link"><i class="fas fa-chevron-right"></i></span></li>';
    }
    
    return $links;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Utilisateurs</title>
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- CSS -->
    <link rel="stylesheet" href="../../assets/css/custom-backoffice.css">
    <style>
        :root {
            /* Professional Dark Palette */
            --primary-color: #ff4a57;
            --primary-light: #ff6b6b;
            --secondary-color: rgba(255, 255, 255, 0.05);
            --accent-color: #1e202e;
            --border-color: #2d3748;
            --text-color: #e2e8f0;
            --text-muted: #94a3b8;
            --bg-body: #0f111a;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-color);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            margin: 0;
            padding: 40px;
            min-height: 100vh;
        }

        /* Header Section */
        .missions-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-color);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-title i {
            color: var(--primary-color);
        }

        /* Stats Cards */
        .missions-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .mission-stat-card {
            background: var(--accent-color);
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }
        
        .mission-stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 74, 87, 0.2);
        }
        
        .mission-stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
            display: block;
        }
        
        .mission-stat-label {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        /* Add User Button */
        .add-mission-btn {
            background: linear-gradient(45deg, var(--primary-color), var(--primary-light));
            border: none;
            border-radius: 25px;
            padding: 12px 25px;
            font-weight: 600;
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        
        .add-mission-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 74, 87, 0.4);
            color: white;
        }

        /* User Cards Grid */
        .mission-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            align-items: start;
            margin-bottom: 30px;
        }

        @media (max-width: 992px) {
            .mission-grid {
                grid-template-columns: repeat(1, 1fr);
            }
        }

        /* User Card */
        .mission-card {
            background: var(--accent-color);
            padding: 20px;
            border-radius: 15px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            flex-direction: column;
        }
        
        .mission-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(255, 74, 87, 0.3);
            border-color: var(--primary-color);
        }
        
        .mission-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--border-color);
            background-color: #333;
        }
        
        .mission-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-color);
            margin: 0 0 5px 0;
        }
        
        .mission-difficulty {
            background: var(--secondary-color);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }
        
        .difficulty-facile { 
            color: #28a745; 
            border: 1px solid #28a745; 
            background: rgba(40, 167, 69, 0.1);
        }
        
        .difficulty-moyen { 
            color: #ffc107; 
            border: 1px solid #ffc107;
            background: rgba(255, 193, 7, 0.1);
        }
        
        .difficulty-difficile { 
            color: #dc3545; 
            border: 1px solid #dc3545;
            background: rgba(220, 53, 69, 0.1);
        }
        
        /* User Info Details */
        .mission-details {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 15px;
            flex: 1;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            background: var(--secondary-color);
            border-radius: 8px;
            font-size: 0.85rem;
        }
        
        .detail-item i {
            color: var(--primary-color);
            width: 16px;
            text-align: center;
            flex-shrink: 0;
            font-size: 0.9rem;
        }
        
        .detail-label {
            color: var(--text-muted);
            font-size: 0.75rem;
            display: block;
            margin-bottom: 2px;
        }
        
        .detail-value {
            color: var(--text-color);
            font-weight: 500;
            font-size: 0.9rem;
        }

        /* Role Badge */
        .role-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
            margin-top: 5px;
        }
        
        .role-admin {
            color: #dc3545;
            border: 1px solid #dc3545;
            background: rgba(220, 53, 69, 0.1);
        }
        
        .role-user {
            color: #3498db;
            border: 1px solid #3498db;
            background: rgba(52, 152, 219, 0.1);
        }
        
        /* Action Buttons */
        .mission-actions {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: auto;
            padding-top: 15px;
            border-top: 1px solid var(--border-color);
        }
        
        .btn-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            font-size: 1rem;
            cursor: pointer;
        }
        
        .btn-icon:hover {
            transform: translateY(-3px) scale(1.1);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        .btn-candidatures {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
        }
        
        .btn-candidatures:hover {
            background: linear-gradient(45deg, #0056b3, #004099);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
        }
        
        .btn-modifier {
            background: linear-gradient(45deg, #ffc107, #e0a800);
            color: #212529;
        }
        
        .btn-modifier:hover {
            background: linear-gradient(45deg, #e0a800, #d39e00);
            box-shadow: 0 5px 15px rgba(255, 193, 7, 0.4);
        }
        
        .btn-supprimer {
            background: linear-gradient(45deg, #dc3545, #c82333);
            color: white;
        }
        
        .btn-supprimer:hover {
            background: linear-gradient(45deg, #c82333, #bd2130);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
            grid-column: 1 / -1;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: var(--text-muted);
            opacity: 0.5;
        }

        /* User Info Section */
        .user-info {
            flex: 1;
        }

        .user-id {
            color: var(--text-muted);
            font-size: 0.8rem;
        }

        /* Pagination Styles */
        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 30px;
            padding: 20px 0;
        }

        .pagination {
            display: flex;
            gap: 6px;
            justify-content: center;
            padding-left: 0;
            list-style: none;
            margin: 0;
        }

        .pagination .page-item .page-link {
            color: var(--text-color);
            background: var(--accent-color);
            border: 1px solid var(--border-color);
            padding: 10px 15px;
            border-radius: 8px;
            min-width: 44px;
            text-align: center;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(45deg, var(--primary-color), var(--primary-light));
            color: white;
            border-color: transparent;
            box-shadow: 0 4px 12px rgba(255, 74, 87, 0.3);
        }

        .pagination .page-item .page-link:hover {
            transform: translateY(-3px);
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary-color);
            color: var(--primary-color);
            box-shadow: 0 4px 12px rgba(255, 74, 87, 0.2);
        }

        .pagination .page-item.disabled .page-link {
            opacity: 0.5;
            pointer-events: none;
            color: var(--text-muted);
        }

        .pagination .page-item .page-link i {
            font-size: 0.9rem;
        }

        /* Pagination Info */
        .pagination-info {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-left: 20px;
            padding: 10px 15px;
            background: var(--accent-color);
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding: 20px;
            }
            
            .mission-header {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }
            
            .user-avatar {
                width: 80px;
                height: 80px;
            }
            
            .mission-actions {
                flex-wrap: wrap;
            }
            
            .pagination {
                flex-wrap: wrap;
            }
            
            .pagination-info {
                margin-left: 0;
                margin-top: 15px;
                text-align: center;
                width: 100%;
            }
            
            .pagination-container {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="missions-header">
        <h2 class="section-title"><i class="fas fa-users-cog"></i> Gestion des Utilisateurs</h2>
        
    </div>

    <!-- Stats Section -->
    <div class="missions-stats">
        <div class="mission-stat-card">
            <span class="mission-stat-number"><?= $totalUsers ?></span>
            <span class="mission-stat-label">Utilisateurs Totaux</span>
        </div>
        <div class="mission-stat-card">
            <span class="mission-stat-number"><?= $adminCount ?></span>
            <span class="mission-stat-label">Administrateurs</span>
        </div>
        <div class="mission-stat-card">
            <span class="mission-stat-number"><?= $userCount ?></span>
            <span class="mission-stat-label">Utilisateurs Normaux</span>
        </div>
    </div>

    <!-- Users Grid -->
    <div class="mission-grid">
        <?php if ($totalUsers > 0 && count($usersForCurrentPage) > 0): ?>
            <?php foreach($usersForCurrentPage as $u): ?>
            <div class="mission-card">
                <div class="mission-header">
                    <?php 
                    $userImgPath = '../../frontoffice/assets/img/default_avatar.jpg'; 
                    if (!empty($u['img'])) {
                        $pathFO = '../../frontoffice/assets/uploads/profiles/' . $u['img'];
                        if (file_exists(__DIR__ . '/../../../view/frontoffice/assets/uploads/profiles/' . $u['img'])) {
                            $userImgPath = $pathFO;
                        }
                    }
                    ?>
                    <img src="<?= htmlspecialchars($userImgPath) ?>" alt="User" class="user-avatar">
                    <div class="user-info">
                        <h3 class="mission-title"><?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?></h3>
                        <span class="user-id">ID: #<?= $u['id_util'] ?></span>
                        <div>
                            <span class="mission-difficulty <?= strtolower($u['typee']) == 'admin' ? 'difficulty-difficile' : 'difficulty-facile' ?>">
                                <?= htmlspecialchars($u['typee']) ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="mission-details">
                    <div class="detail-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <span class="detail-label">Email</span>
                            <span class="detail-value"><?= htmlspecialchars($u['mail']) ?></span>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <span class="detail-label">Téléphone</span>
                            <span class="detail-value"><?= htmlspecialchars($u['num']) ?></span>
                        </div>
                    </div>
                    
                    <?php if (!empty($u['adresse'])): ?>
                    <div class="detail-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <span class="detail-label">Adresse</span>
                            <span class="detail-value"><?= htmlspecialchars($u['adresse']) ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="mission-actions">
                    <a href="updateUtilisateur.php?id=<?= $u['id_util'] ?>" class="btn-icon btn-modifier" title="Modifier">
                        <i class="fas fa-pen"></i>
                    </a>
                    <a href="deleteUtilisateur.php?id=<?= $u['id_util'] ?>" class="btn-icon btn-supprimer" onclick="return confirm('Confirmer la suppression ?')" title="Supprimer">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-users-slash"></i>
                <h3>Aucun utilisateur trouvé</h3>
                <p>Commencez par ajouter un nouvel utilisateur</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="pagination-container">
        <ul class="pagination">
            <?= generatePaginationLinks($currentPage, $totalPages) ?>
        </ul>
        <div class="pagination-info">
            Affichage des utilisateurs <?= $startIndex + 1 ?> à <?= $endIndex ?> sur <?= $totalUsers ?> 
            (Page <?= $currentPage ?> sur <?= $totalPages ?>)
        </div>
    </div>
    <?php endif; ?>
</body>
</html>