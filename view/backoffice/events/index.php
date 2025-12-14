<?php require_once 'lang/lang_config.php'; ?>
<!DOCTYPE html>
<html lang="<?= get_current_lang() ?>" dir="<?= get_dir() ?>">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Backoffice - Engage</title>
    <link rel="icon" href="assets/img/favicon.png" />   
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="assets/css/all.css" />
    <!-- Custom CSS -->
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
        .text-info { color: #17a2b8 !important; }
        
        /* RTL Support */
        <?php if (get_dir() === 'rtl'): ?>
        #sidebar {
            right: 0;
            left: auto;
            border-right: none;
            border-left: 2px solid var(--primary-color);
        }
        
        #content {
            margin-left: 0;
            margin-right: var(--sidebar-width);
        }
        
        #sidebar.active {
            margin-right: -250px;
        }
        
        #content.active {
            margin-right: 0;
        }

        #sidebar ul li a {
            border-left: none;
            border-right: 4px solid transparent;
        }
        
        #sidebar ul li a:hover, 
        #sidebar ul li.active > a {
            border-left: none;
            border-right: 4px solid var(--primary-color);
        }

        #sidebar ul li a i {
            margin-right: 0;
            margin-left: 10px;
        }

        .dropdown-menu {
            text-align: right;
        }

        .ml-auto {
            margin-left: 0 !important;
            margin-right: auto !important;
        }
        <?php endif; ?>
    </style>
</head>

</head>

<body>
<?php
// Load statistics data
require_once '../../model/participationModel.php';
require_once '../../model/evenementModel.php';
$partModel = new ParticipationModel();
$evtModel = new EvenementModel();

// Handle Mail Broadcast
$broadcastMsg = '';
$broadcastType = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'broadcast_email') {
    $eventId = $_POST['event_id'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';

    if (empty($subject) || empty($message)) {
        $broadcastMsg = "Veuillez remplir le sujet et le message.";
        $broadcastType = "danger";
    } else {
        $recipients = [];
        if ($eventId === 'all') {
            // Get all unique participants from all events
            $allParts = $partModel->getAllParticipationsWithUsers();
            foreach ($allParts as $p) {
                if (!empty($p['email'])) {
                    $recipients[$p['email']] = $p['prenom'] . ' ' . $p['nom']; // Use email as key to deduplicate
                }
            }
        } else {
            // Get participants for specific event
            $parts = $partModel->getEventParticipants($eventId);
            foreach ($parts as $p) {
                if (!empty($p['email'])) {
                    $recipients[$p['email']] = $p['prenom'] . ' ' . $p['nom'];
                }
            }
        }

        if (empty($recipients)) {
            $broadcastMsg = "Aucun participant trouvé pour cette sélection.";
            $broadcastType = "warning";
        } else {
            // SIMULATION OF SENDING EMAIL
            // In a real environment, you would use mail() or PHPMailer here.
            // foreach ($recipients as $email => $name) { mail($email, $subject, $message); }
            
            $count = count($recipients);
            $broadcastMsg = "Succès ! Le message a été envoyé à <strong>$count</strong> participant(s).";
            $broadcastType = "success";
        }
    }
}

$stats = $partModel->getStatistics();
$allEvents = $evtModel->getActiveEvents();

$topEvents = array_column($stats['views'], 'titre');
$topViews = array_column($stats['views'], 'vues');

$partEvents = array_column($stats['participants'], 'titre');
$partCounts = array_column($stats['participants'], 'count');

$revEvents = array_column($stats['revenue'], 'titre');
$revCounts = array_column($stats['revenue'], 'total');
?>
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3><img src="assets/img/logo.png" alt="logo" style="height: 40px; margin-right: 10px;" />BACKOFFICE</h3>
        </div>

        <ul class="list-unstyled components">
            <li class="active">
                <a href="index.php"><i class="fas fa-tachometer-alt"></i> <?= __('dashboard') ?></a>
            </li>
            <li>
                <a href="#"><i class="fas fa-tasks"></i> <?= __('missions') ?></a>
            </li>
            <li>
                <a href="#"><i class="fas fa-gamepad"></i> <?= __('gamification') ?></a>
            </li>
            <li>
                <a href="#"><i class="fas fa-exclamation-circle"></i> <?= __('reclamations') ?></a>
            </li>
            <li>
                <a href="evenement.php"><i class="fas fa-calendar-alt"></i> <?= __('events') ?></a>
            </li>
            <li>
                <a href="#"><i class="fas fa-graduation-cap"></i> <?= __('education') ?></a>
            </li>
            <li>
                <a href="#"><i class="fas fa-users"></i> <?= __('users') ?></a>
            </li>
            <li>
                <a href="#"><i class="fas fa-chart-bar"></i> <?= __('analytics') ?></a>
            </li>
            <li>
                <a href="settings.php"><i class="fas fa-cog"></i> <?= __('settings') ?></a>
            </li>
        </ul>
    </nav>

    <!-- Page Content -->
    <div id="content">
        <!-- Top Navigation -->
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <button type="button" id="sidebarCollapse" class="btn">
                    <i class="fas fa-bars"></i>
                </button>
                
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" style="color: var(--text-color);">
                                <i class="fas fa-user-circle" style="color: var(--primary-color);"></i> Administrateur
                            </a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#"><i class="fas fa-user"></i> <?= __('admin_profile') ?></a>
                                <a class="dropdown-item" href="settings.php"><i class="fas fa-cog"></i> <?= __('settings') ?></a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt"></i> <?= __('logout') ?></a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="container-fluid">
            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card">
                        <div class="card-body">
                            <i class="fas fa-users"></i>
                            <h3>1,254</h3>
                            <p><?= __('active_users') ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card">
                        <div class="card-body">
                            <i class="fas fa-tasks"></i>
                            <h3>342</h3>
                            <p><?= __('ongoing_missions') ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card">
                        <div class="card-body">
                            <i class="fas fa-exclamation-circle"></i>
                            <h3>23</h3>
                            <p><?= __('reclamations') ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card">
                        <div class="card-body">
                            <i class="fas fa-calendar-alt"></i>
                            <h3>15</h3>
                            <p><?= __('upcoming_events') ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Advanced Charts Section -->
            <div class="row mb-4">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fas fa-eye"></i> Top Événements (Vues)</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="viewsChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fas fa-users"></i> Top Participations</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="participantsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mail Broadcast Section -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fas fa-envelope-open-text"></i> Communication Groupée</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($broadcastMsg)): ?>
                                <div class="alert alert-<?= $broadcastType ?> alert-dismissible fade show" role="alert">
                                    <?= $broadcastMsg ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>
                            
                            <form action="" method="post" id="broadcastForm">
                                <input type="hidden" name="action" value="broadcast_email">
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="eventSelect">Cibler les participants de :</label>
                                        <select id="eventSelect" name="event_id" class="form-control" style="background-color: var(--secondary-color); color: var(--text-color); border-color: #2d3047;">
                                            <option value="all">Tous les événements actifs</option>
                                            <?php foreach ($allEvents as $evt): ?>
                                            <option value="<?= $evt['id_evenement'] ?>"><?= htmlspecialchars($evt['titre']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-8">
                                        <label for="mailSubject">Sujet de l'email :</label>
                                        <input type="text" class="form-control" id="mailSubject" name="subject" placeholder="Important : Mise à jour de l'événement..." style="background-color: var(--secondary-color); color: var(--text-color); border-color: #2d3047;" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="mailMessage">Message :</label>
                                    <textarea class="form-control" id="mailMessage" name="message" rows="4" style="background-color: var(--secondary-color); color: var(--text-color); border-color: #2d3047;" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Envoyer le message
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fas fa-bolt"></i> <?= __('quick_actions') ?></h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <button class="btn btn-primary btn-block">
                                        <i class="fas fa-plus"></i> <?= __('new_mission') ?>
                                    </button>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <button onclick="window.location.href='evenement.php'" class="btn btn-primary btn-block">
                                        <i class="fas fa-calendar-plus"></i> <?= __('create_event') ?>
                                    </button>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <button class="btn btn-primary btn-block">
                                        <i class="fas fa-bullhorn"></i> <?= __('send_notification') ?>
                                    </button>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <button class="btn btn-primary btn-block">
                                        <i class="fas fa-chart-bar"></i> <?= __('generate_report') ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="assets/js/jquery-1.12.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/all.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function () {
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
                $('#content').toggleClass('active');
            });

            // --- Views Chart ---
            const ctxViews = document.getElementById('viewsChart').getContext('2d');
            new Chart(ctxViews, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($topEvents) ?>,
                    datasets: [{
                        label: 'Nombre de Vues',
                        data: <?= json_encode($topViews) ?>,
                        backgroundColor: 'rgba(255, 74, 87, 0.5)',
                        borderColor: '#ff4a57',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#2d3047' }, ticks: { color: '#fff' } },
                        x: { grid: { color: '#2d3047' }, ticks: { color: '#fff' } }
                    },
                    plugins: { legend: { labels: { color: '#fff' } } }
                }
            });

            // --- Participants Chart ---
            const ctxPart = document.getElementById('participantsChart').getContext('2d');
            new Chart(ctxPart, {
                type: 'doughnut',
                data: {
                    labels: <?= json_encode($partEvents) ?>,
                    datasets: [{
                        label: 'Participants',
                        data: <?= json_encode($partCounts) ?>,
                        backgroundColor: [
                            '#ff4a57', '#36a2eb', '#ffcd56', '#4bc0c0', '#9966ff'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { labels: { color: '#fff' } } }
                }
            });
        });
    </script>
</body>
</html>
