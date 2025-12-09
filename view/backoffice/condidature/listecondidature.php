<?php
require_once __DIR__ . '/../../../controller/condidaturecontroller.php';

$condC = new condidaturecontroller();

// Vérifier si un filtre par mission est demandé
$mission_id = $_GET['mission_id'] ?? null;

if ($mission_id) {
    $liste = $condC->getCondidaturesByMission($mission_id);
    $mission_titre = $condC->getMissionTitle($mission_id);
    $pageTitle = "Candidatures pour : " . htmlspecialchars($mission_titre);
    $isFiltered = true;
} else {
    $liste = $condC->getAllCondidatures();
    $pageTitle = "Toutes les candidatures";
    $isFiltered = false;
}

// Grouper les candidatures par mission pour éviter la duplication
$candidaturesParMission = [];
foreach ($liste as $candidature) {
    $mission_titre = $candidature['mission_titre'];
    if (!isset($candidaturesParMission[$mission_titre])) {
        $candidaturesParMission[$mission_titre] = [];
    }
    $candidaturesParMission[$mission_titre][] = $candidature;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title><?= $pageTitle ?> - ENGAGE Admin</title>

    <link rel="stylesheet" href="../assets/css/custom-backoffice.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        .mission-group {
            margin-bottom: 30px;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
        }
        
        .mission-header {
            background: linear-gradient(45deg, var(--primary-color), var(--primary-light));
            color: white;
            padding: 15px 20px;
            font-weight: 700;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .mission-count {
            background: rgba(255, 255, 255, 0.2);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        
        .candidatures-list {
            background: var(--accent-color);
        }
        
        .candidature-row {
            display: grid;
            grid-template-columns: 80px 1fr 1fr 1fr 1fr 120px 150px;
            gap: 1px;
            border-bottom: 1px solid var(--border-color);
            align-items: center;
        }
        
        .candidature-row:last-child {
            border-bottom: none;
        }
        
        .candidature-cell {
            padding: 12px 15px;
            display: flex;
            align-items: center;
        }
        
        .candidature-header {
            background: var(--secondary-color);
            font-weight: 600;
            color: var(--primary-color);
        }
    </style>
</head>

<body>

<div class="container mt-4">

    <!-- En-tête avec statistiques -->
    <div class="stats-header">
        <div>
            <h2 class="text-white">
                <?php if ($isFiltered): ?>
                    📋 Candidatures pour : <span style="color: var(--primary-color)">"<?= htmlspecialchars($mission_titre) ?>"</span>
                <?php else: ?>
                    📋 Toutes les candidatures par mission
                <?php endif; ?>
            </h2>
            
            <?php if ($isFiltered): ?>
                <a href="listecondidature.php" class="btn-primary" style="margin-top: 10px; display: inline-block;">
                    <i class="fas fa-arrow-left"></i> Voir toutes les candidatures
                </a>
            <?php endif; ?>
        </div>
        
        <div class="filters">
            <select class="filter-select" id="filterStatut" onchange="filterTable()">
                <option value="">Tous les statuts</option>
                <option value="en_attente">En attente</option>
                <option value="acceptée">Acceptée</option>
                <option value="refusée">Refusée</option>
            </select>

            <input type="text" class="search-box" id="searchInput" placeholder="🔍 Rechercher..." onkeyup="filterTable()">
        </div>
    </div>

    <!-- Cartes de statistiques -->
    <?php
    $totalCandidatures = count($liste);
    $enAttente = count(array_filter($liste, fn($c) => $c['statut'] === 'en_attente'));
    $acceptees = count(array_filter($liste, fn($c) => $c['statut'] === 'acceptée'));
    $refusees = count(array_filter($liste, fn($c) => $c['statut'] === 'refusée'));
    ?>
    
    <div class="stats-cards">
        <div class="stat-card">
            <span class="stat-number"><?= $totalCandidatures ?></span>
            <span class="stat-label">Total Candidatures</span>
        </div>
        <div class="stat-card">
            <span class="stat-number"><?= $enAttente ?></span>
            <span class="stat-label">En attente</span>
        </div>
        <div class="stat-card">
            <span class="stat-number"><?= $acceptees ?></span>
            <span class="stat-label">Acceptées</span>
        </div>
        <div class="stat-card">
            <span class="stat-number"><?= $refusees ?></span>
            <span class="stat-label">Refusées</span>
        </div>
    </div>

    <!-- Message si aucune candidature -->
    <?php if (empty($liste)): ?>
        <div class="empty-state" style="margin: 40px 0;">
            <i class="fas fa-users-slash" style="font-size: 4rem;"></i>
            <h3>Aucune candidature trouvée</h3>
            <p>Aucun joueur n'a encore postulé aux missions</p>
        </div>
    <?php else: ?>
        <!-- Liste des candidatures groupées par mission -->
        <div id="candidaturesContainer">
            <?php foreach ($candidaturesParMission as $mission_titre => $candidatures): ?>
                <div class="mission-group" data-mission="<?= htmlspecialchars($mission_titre) ?>">
                    <div class="mission-header">
                        <div>
                            <i class="fas fa-tasks"></i>
                            <?= htmlspecialchars($mission_titre) ?>
                        </div>
                        <div class="mission-count">
                            <?= count($candidatures) ?> candidature(s)
                        </div>
                    </div>
                    
                    <div class="candidatures-list">
                        <!-- En-tête du tableau -->
                        <div class="candidature-row candidature-header">
                            <div class="candidature-cell">ID</div>
                            <div class="candidature-cell">Pseudo</div>
                            <div class="candidature-cell">Email</div>
                            <div class="candidature-cell">Niveau</div>
                            <div class="candidature-cell">Disponibilités</div>
                            <div class="candidature-cell">Statut</div>
                            <div class="candidature-cell">Actions</div>
                        </div>
                        
                        <!-- Candidatures -->
                        <?php foreach ($candidatures as $c): ?>
                            <div class="candidature-row" data-statut="<?= $c['statut'] ?>">
                                <div class="candidature-cell">
                                    <strong>#<?= $c['id'] ?></strong>
                                </div>
                                <div class="candidature-cell">
                                    <div class="player-info">
                                        <i class="fas fa-user"></i>
                                        <?= htmlspecialchars($c['pseudo_gaming']) ?>
                                    </div>
                                </div>
                                <div class="candidature-cell">
                                    <div class="email-info">
                                        <i class="fas fa-envelope"></i>
                                        <?= htmlspecialchars($c['email']) ?>
                                    </div>
                                </div>
                                <div class="candidature-cell">
                                    <span class="badge badge-info">
                                        <?= htmlspecialchars($c['niveau_experience']) ?>
                                    </span>
                                </div>
                                <div class="candidature-cell">
                                    <small><?= htmlspecialchars($c['disponibilites']) ?></small>
                                </div>
                                <div class="candidature-cell">
                                    <span class="badge <?= $c['statut'] ?>">
                                        <?= $c['statut'] ?>
                                    </span>
                                </div>
                                <div class="candidature-cell">
                                    <div class="action-buttons">
                                        <!-- Modifier statut -->
                                        <a href="modifiercondidature.php?id=<?= $c['id'] ?>" 
                                           class="btn-icon btn-modifier"
                                           title="Modifier statut">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- Supprimer -->
                                        <a href="deletecondidature.php?id=<?= $c['id'] ?>" 
                                           class="btn-icon btn-supprimer"
                                           title="Supprimer"
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette candidature ?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
// Fonction de filtrage combinée
function filterTable() {
    const statutFilter = document.getElementById('filterStatut').value;
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    
    const missionGroups = document.querySelectorAll('.mission-group');
    let visibleMissions = 0;
    
    missionGroups.forEach(missionGroup => {
        const missionTitle = missionGroup.getAttribute('data-mission').toLowerCase();
        const candidatureRows = missionGroup.querySelectorAll('.candidature-row:not(.candidature-header)');
        let visibleCandidatures = 0;
        
        // Filtrer les candidatures dans cette mission
        candidatureRows.forEach(row => {
            const statut = row.getAttribute('data-statut');
            const text = row.textContent.toLowerCase();
            
            const matchStatut = !statutFilter || statut === statutFilter;
            const matchSearch = !searchTerm || text.includes(searchTerm) || missionTitle.includes(searchTerm);
            
            if (matchStatut && matchSearch) {
                row.style.display = 'grid';
                visibleCandidatures++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Afficher/masquer la mission selon s'il y a des candidatures visibles
        if (visibleCandidatures > 0 || (!statutFilter && !searchTerm)) {
            missionGroup.style.display = 'block';
            visibleMissions++;
            
            // Mettre à jour le compteur
            const missionCount = missionGroup.querySelector('.mission-count');
            if (missionCount && searchTerm) {
                missionCount.textContent = visibleCandidatures + ' candidature(s) filtrée(s)';
            }
        } else {
            missionGroup.style.display = 'none';
        }
    });
    
    // Message si aucune mission visible
    const container = document.getElementById('candidaturesContainer');
    let noResultsMsg = container.querySelector('.no-results-message');
    
    if (visibleMissions === 0 && (statutFilter || searchTerm)) {
        if (!noResultsMsg) {
            noResultsMsg = document.createElement('div');
            noResultsMsg.className = 'empty-state no-results-message';
            noResultsMsg.innerHTML = `
                <i class="fas fa-search"></i>
                <h3>Aucune candidature trouvée</h3>
                <p>Aucune candidature ne correspond à vos critères de recherche</p>
            `;
            container.appendChild(noResultsMsg);
        }
    } else if (noResultsMsg) {
        noResultsMsg.remove();
    }
}
// Exporter les données (fonctionnalité bonus)
function exportToCSV() {
    const rows = [];
    const headers = ['ID', 'Mission', 'Pseudo Gaming', 'Email', 'Niveau', 'Disponibilités', 'Statut'];
    rows.push(headers);
    
    document.querySelectorAll('#candidaturesTable tbody tr:not([style*="display: none"])').forEach(row => {
        if (row.querySelector('.empty-state')) return;
        
        const cells = row.querySelectorAll('td');
        const rowData = [
            cells[0].textContent.trim(),
            cells[1].textContent.trim(),
            cells[2].textContent.trim(),
            cells[3].textContent.trim(),
            cells[4].textContent.trim(),
            cells[5].textContent.trim(),
            cells[6].textContent.trim()
        ];
        rows.push(rowData);
    });
      const csvContent = rows.map(row => row.join(',')).join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'candidatures_' + new Date().toISOString().split('T')[0] + '.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}
    

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    filterTable();
    const exportBtn = document.createElement('button');
    exportBtn.textContent = '📊 Exporter CSV';
    exportBtn.className = 'btn-primary';
    exportBtn.style.marginLeft = '10px';
    exportBtn.onclick = exportToCSV;
    
    document.querySelector('.filters').appendChild(exportBtn);
});
</script>
<script src="../assets/js/back.js"></script>

</body>
</html>




<link rel="stylesheet" href="../assets/css/custom-backoffice.css">
