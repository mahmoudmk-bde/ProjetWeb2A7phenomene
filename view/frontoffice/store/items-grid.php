<?php if (empty($items)): ?>
    <!-- Message si aucun jeu -->
    <div class="no-games">
        <h3 class="no-games-title">Aucun jeu disponible pour le moment</h3>
        <p class="no-games-text">Revenez bientôt pour découvrir nos nouveautés !</p>
    </div>
<?php else: ?>
    <!-- Grille de jeux -->
    <div class="row" id="games-container">
        <?php foreach ($items as $item): ?>
            <div class="col-lg-4 col-md-6 game-item" data-category="<?= $item['categorie'] ?>">
                <div class="game-card store-card">
                    <!-- Image du jeu -->
                    <div class="game-card-img">
                        <?php if ($item['image']): ?>
                            <img src="<?php echo BASE_URL . htmlspecialchars($item['image']); ?>"
                                alt="<?= htmlspecialchars($item['nom']) ?>">
                        <?php else: ?>
                            <img src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/img/gallery/gallery_item_1.png"
                                alt="<?= htmlspecialchars($item['nom']) ?>">
                        <?php endif; ?>

                        <!-- Badge de catégorie -->
                        <div class="game-badge">
                            <?= ucfirst($item['categorie']) ?>
                        </div>

                        <!-- Badge de stock -->
                        <?php if ($item['stock'] > 0): ?>
                            <?php if ($item['stock'] < 5): ?>
                                <div class="stock-badge stock-low">Stock limité</div>
                            <?php else: ?>
                                <div class="stock-badge">En stock</div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="stock-badge stock-out">Rupture</div>
                        <?php endif; ?>
                    </div>

                    <!-- Corps de la carte -->
                    <div class="game-card-body">
                        <h5 class="game-title">
                            <?= htmlspecialchars($item['nom']) ?>
                        </h5>

                        <div class="game-partner">Par <?= htmlspecialchars($item['partenaire_nom'] ?? 'Inconnu') ?></div>

                        <div class="game-info">
                            <span class="game-category">
                                <?= ucfirst($item['categorie']) ?>
                            </span>
                            <?php if ($item['age_minimum']): ?>
                                <span class="game-age">
                                    <i class="fas fa-child"></i> <?= $item['age_minimum'] ?>+
                                </span>
                            <?php endif; ?>
                        </div>

                        <?php if ($item['plateforme']): ?>
                            <div class="game-platform"><?= htmlspecialchars($item['plateforme']) ?></div>
                        <?php endif; ?>

                        <a href="?controller=Store&action=show&id=<?= $item['id'] ?>" class="btn-view-game">Voir les détails</a>
                        <div class="game-foot">
                            <div class="game-price-inline"><?= number_format($item['prix'], 2) ?> DT</div>
                            <div class="game-stats">
                                <span><?= isset($item['views_count']) ? (int) $item['views_count'] : 0 ?> vues</span>
                                <span><?= isset($item['likes_count']) ? (int) $item['likes_count'] : 0 ?> likes</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if (isset($totalPages) && $totalPages > 1): ?>
        <div class="row mt-5">
            <div class="col-lg-12">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php
                        // Build base URL for pagination links
                        $queryParams = $_GET;
                        unset($queryParams['page']);
                        $baseUrl = '?' . http_build_query($queryParams);
                        ?>

                        <!-- Previous Button -->
                        <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= $baseUrl ?>&page=<?= $currentPage - 1 ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>

                        <!-- Page Numbers -->
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                                <a class="page-link" href="<?= $baseUrl ?>&page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <!-- Next Button -->
                        <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= $baseUrl ?>&page=<?= $currentPage + 1 ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>