<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Ensure BASE_URL is defined
if (!defined('BASE_URL')) {
    define('BASE_URL', '/ProjetWeb2A7phenomene/');
}
?>
<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Recommandations IA - Engage</title>
    <link rel="icon" href="img/favicon.png" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/assets/css/animate.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/assets/css/owl.carousel.min.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/assets/css/all.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/assets/css/style.css" />
    <style>
        /* Dark Theme & Card Styling */
        body {
            background-color: #0c0d14;
            color: #ffffff;
            font-family: 'Rajdhani', sans-serif;
        }

        .section_padding {
            padding: 80px 0;
        }

        .reco-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .reco-header h2 {
            font-size: 48px;
            font-weight: 700;
            background: linear-gradient(45deg, #ff0066, #ffcc00);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 20px;
        }

        .reco-card {
            background: #1a1b26;
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
            border: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .reco-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(255, 0, 102, 0.3);
            border-color: #ff0066;
        }

        .match-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(45deg, #ff0066, #ff4a57);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9rem;
            z-index: 2;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .reco-img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .reco-body {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .reco-title {
            color: white;
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-decoration: none;
        }

        .reco-title:hover {
            color: #ff0066;
        }

        .reco-meta {
            color: #888;
            font-size: 0.9rem;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
        }

        .reco-price {
            color: #ffcc00;
            font-weight: 700;
            font-size: 1.5rem;
            margin-top: auto;
            margin-bottom: 15px;
        }

        .btn-view {
            background: transparent;
            border: 2px solid #ff0066;
            color: white;
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            text-align: center;
        }

        .btn-view:hover {
            background: #ff0066;
            color: white;
            text-decoration: none;
        }

        .empty-state {
            text-align: center;
            padding: 60px;
            background: #1a1b26;
            border-radius: 20px;
        }
    </style>
</head>

<body>
    <?php include __DIR__ . '/../../header_common1.php'; ?>

    <section class="breadcrumb breadcrumb_bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb_iner text-center">
                        <div class="breadcrumb_iner_item">
                            <h2>Pour Vous</h2>
                            <p>Store / Recommandations IA</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section_padding">
        <div class="container">
            <div class="reco-header">
                <h2>Sélectionnée par notre IA</h2>
                <p class="text-muted">Basé sur vos achats et votre liste d'envies</p>
            </div>

            <?php if (empty($recommendations)): ?>
                <div class="empty-state">
                    <i class="fas fa-robot fa-4x mb-4 text-warning"></i>
                    <h3>Pas encore assez de données !</h3>
                    <p class="text-muted">Ajoutez des jeux à votre liste d'envies ou passez des commandes pour que notre IA
                        puisse apprendre vos goûts.</p>
                    <a href="?controller=Store&action=index" class="btn_1 mt-4">Explorer le Store</a>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($recommendations as $item): ?>
                        <div class="col-lg-4 col-md-6 mb-5">
                            <div class="reco-card">
                                <?php
                                // Calculate a "match percentage" based on score relative to max possible (15 + 1 = 16)
                                // Just for visuals
                                $percent = min(99, 80 + ($item['match_score'] * 2));
                                ?>
                                <div class="match-badge">
                                    <i class="fas fa-magic mr-1"></i> <?= $percent ?>% Match
                                </div>

                                <a href="?controller=Store&action=show&id=<?= $item['id'] ?>">
                                    <?php if ($item['image']): ?>
                                        <img src="<?= BASE_URL . $item['image'] ?>" class="reco-img"
                                            alt="<?= htmlspecialchars($item['nom']) ?>">
                                    <?php else: ?>
                                        <div class="reco-img d-flex align-items-center justify-content-center bg-dark">
                                            <i class="fas fa-gamepad fa-3x text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </a>

                                <div class="reco-body">
                                    <div class="reco-meta">
                                        <span><i class="fas fa-tag mr-1"></i> <?= htmlspecialchars($item['categorie']) ?></span>
                                        <span><i class="fas fa-desktop mr-1"></i>
                                            <?= htmlspecialchars($item['plateforme']) ?></span>
                                    </div>

                                    <a href="?controller=Store&action=show&id=<?= $item['id'] ?>" class="reco-title">
                                        <?= htmlspecialchars($item['nom']) ?>
                                    </a>

                                    <div class="reco-price">
                                        <?= number_format($item['prix'], 2) ?> DT
                                    </div>

                                    <a href="?controller=Store&action=show&id=<?= $item['id'] ?>" class="btn-view">
                                        Voir les détails
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer Area (You might want to include your common footer here) -->
    <script src="<?php echo BASE_URL; ?>view/frontoffice/assets/js/jquery-1.12.1.min.js"></script>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/assets/js/popper.min.js"></script>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/assets/js/bootstrap.min.js"></script>
</body>

</html>