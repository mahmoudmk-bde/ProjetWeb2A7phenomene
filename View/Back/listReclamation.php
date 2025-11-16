<?php
// View/Back/listReclamation.php
require_once __DIR__ . '/../../Controller/ReclamationController.php';
require_once __DIR__ . '/../../Controller/ResponseController.php';

$recCtrl = new ReclamationController();
$respCtrl = new ResponseController();
$list = $recCtrl->listReclamations();

// Build content using output buffering
ob_start();
?>
<style>
        :root {
            --primary-color: #ff4a57;
            --secondary-color: #1f2235;
            --accent-color: #24263b;
            --text-color: #ffffff;
            --sidebar-width: 250px;
        }
        
        body {
            background-color: #1f2235;
            color: var(--text-color);
            overflow-x: hidden;
            font-family: 'Arial', sans-serif;
        }
        
        #sidebar {
            min-height: 100vh;
            width: var(--sidebar-width);
            position: fixed;
            top: 0;
            left: 0;
            background: var(--secondary-color);
            color: white;
            transition: all 0.3s;
            z-index: 1000;
            border-right: 2px solid var(--primary-color);
        }
        
        #sidebar .sidebar-header {
            padding: 20px;
            background: var(--accent-color);
            text-align: center;
        }
        
        #sidebar .sidebar-header h3 {
            margin: 0;
            font-weight: bold;
        }
        
        #sidebar ul.components {
            padding: 20px 0;
        }
        
        #sidebar ul li a {
            padding: 15px 20px;
            color: var(--text-color);
            display: block;
            text-decoration: none;
            border-left: 4px solid transparent;
            transition: all 0.3s;
            font-size: 14px;
        }
        
        #sidebar ul li a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        #sidebar ul li a:hover {
            background: var(--accent-color);
            border-left: 4px solid var(--primary-color);
            color: var(--primary-color);
        }
        
        #sidebar ul li.active > a {
            background: var(--accent-color);
            border-left: 4px solid var(--primary-color);
            color: var(--primary-color);
        }
        
        #content {
            width: calc(100% - var(--sidebar-width));
            margin-left: var(--sidebar-width);
            padding: 20px;
            transition: all 0.3s;
            background: #1f2235;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            background: var(--secondary-color);
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            border: 1px solid #2d3047;
        }
        
        .card-header {
            background: var(--accent-color);
            border-bottom: 2px solid var(--primary-color);
            color: var(--text-color);
            font-weight: bold;
        }
        
        .stat-card {
            text-align: center;
            padding: 25px 15px;
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card i {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: var(--primary-color);
        }
        
        .stat-card h3 {
            font-size: 2rem;
            margin: 10px 0;
            color: var(--text-color);
        }
        
        .stat-card p {
            color: #b0b3c1;
            margin: 0;
        }
        
        .table-responsive {
            background: var(--secondary-color);
            border-radius: 10px;
            padding: 20px;
        }
        
        .table {
            color: var(--text-color);
            margin: 0;
        }
        
        .table thead th {
            border-bottom: 2px solid var(--primary-color);
            color: var(--primary-color);
            font-weight: bold;
        }
        
        .table tbody tr:hover {
            background: var(--accent-color);
        }
        
        .btn-primary {
            background: linear-gradient(45deg, var(--primary-color), #ff6b7a);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: bold;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background: linear-gradient(45deg, #ff6b7a, var(--primary-color));
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 74, 87, 0.4);
        }
        
        .navbar {
            background: var(--secondary-color) !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .badge {
            border-radius: 15px;
            padding: 5px 12px;
            font-weight: normal;
        }
        
        .badge-success {
            background: #28a745;
        }
        
        .badge-warning {
            background: #ffc107;
            color: #212529;
        }
        
        .badge-info {
            background: #17a2b8;
        }
        
        .list-group-item {
            background: var(--accent-color);
            border: 1px solid #2d3047;
            color: var(--text-color);
            margin-bottom: 10px;
            border-radius: 8px !important;
        }
        
        .list-group-item:hover {
            background: var(--secondary-color);
            border-color: var(--primary-color);
        }
        
        .dropdown-menu {
            background: var(--secondary-color);
            border: 1px solid var(--primary-color);
        }
        
        .dropdown-item {
            color: var(--text-color);
        }
        
        .dropdown-item:hover {
            background: var(--accent-color);
            color: var(--primary-color);
        }
        
        #sidebarCollapse {
            background: var(--primary-color);
            border: none;
            border-radius: 5px;
        }
        
        .text-primary { color: var(--primary-color) !important; }
        .text-success { color: #28a745 !important; }
        .text-warning { color: #ffc107 !important; }
        .text-info { color: #17a2b8 !important; }
    </style>
<div class="card">
  <h2>Liste des réclamations</h2>

  <table class="table" style="width:100%; border-collapse:collapse;">
    <thead>
      <tr style="background:#f5f5f5;">
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
          <td><?= htmlspecialchars($rec['sujet']) ?></td>
          <td><?= htmlspecialchars($rec['email']) ?></td>
          <td><?= htmlspecialchars($rec['date_creation'] ?? '') ?></td>
          <td><?= htmlspecialchars($rec['statut']) ?></td>
          <td style="max-width:320px;">
            <?php
              $responses = $respCtrl->getResponses($rec['id']);
              if ($responses) {
                foreach ($responses as $r) {
                  echo '<div style="margin-bottom:6px;padding:6px;background:#fafafa;border-radius:4px;">'
                       .nl2br(htmlspecialchars($r['contenu'])).
                       '<div style="font-size:11px;color:#666;">'.$r['date_response'].'</div></div>';
                }
              } else {
                echo '<em>Pas encore de réponse</em>';
              }
            ?>
          </td>
          <td>
            <a class="btn" style="background:#2d89ef;color:#fff" href="response.php?id=<?= $rec['id'] ?>">Répondre</a>
            <a class="btn" style="background:#ed1c24;color:#fff" href="delete.php?id=<?= $rec['id'] ?>" onclick="return confirm('Supprimer ?')">Supprimer</a>
            <a class="btn" style="background:#6c757d;color:#fff" href="details.php?id=<?= $rec['id'] ?>">Voir</a>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="7"><em>Aucune réclamation trouvée.</em></td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
