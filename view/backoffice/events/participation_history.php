<?php
session_start();
require_once '../../config.php';
require_once '../../model/participationModel.php';

$participationModel = new ParticipationModel();
$history = $participationModel->getAllParticipationsWithUsers();
?>
<?php include 'assets/layout_top.php'; ?>

            <div class="row mt-3">
                <div class="col-12">
                    <h1 class="mb-4">Historique global des participants</h1>

                    <div class="card dashboard-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Toutes les réservations</h5>
                            <a href="evenement.php" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Retour aux événements
                            </a>
                        </div>
                        <div class="card-body">
                            <?php if (empty($history)): ?>
                                <div class="alert alert-info mb-0">Aucune participation enregistrée pour le moment.</div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Participant</th>
                                                <th>Email</th>
                                                <th>Événement</th>
                                                <th>Date</th>
                                                <th>Quantité</th>
                                                <th>Montant</th>
                                                <th>Mode</th>
                                                <th>Statut</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($history as $row):
                                                $qty = isset($row['quantite']) ? max(1, (int)$row['quantite']) : 1;
                                                $amountDisplay = isset($row['montant_total']) && $row['montant_total'] !== null
                                                    ? number_format((float)$row['montant_total'], 2) . ' TND'
                                                    : ($row['type_evenement'] === 'payant'
                                                        ? number_format($qty * (float)($row['prix'] ?? 0), 2) . ' TND'
                                                        : 'Gratuit');
                                                $modeDisplay = $row['mode_paiement'] ?? ($row['type_evenement'] === 'payant' ? 'Carte' : 'Gratuit');
                                            ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($row['prenom'] . ' ' . $row['nom']) ?></td>
                                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                                    <td><?= htmlspecialchars($row['titre']) ?></td>
                                                    <td><?= date('d/m/Y', strtotime($row['date_participation'])) ?></td>
                                                    <td><?= $qty ?></td>
                                                    <td><?= $amountDisplay ?></td>
                                                    <td><?= htmlspecialchars($modeDisplay) ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php 
                                                            echo $row['statut'] === 'acceptée' ? 'success' : ($row['statut'] === 'refusée' ? 'danger' : 'warning');
                                                        ?>">
                                                            <?= htmlspecialchars($row['statut']) ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <h1 class="mb-4">Historique global des événements</h1>

                    <div class="card dashboard-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Tous les participants</h5>
                            <a href="evenement.php" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Retour</a>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($history)): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Participant</th>
                                            <th>Email</th>
                                            <th>Événement</th>
                                            <th>Date</th>
                                            <th>Statut</th>
                                            <th>Quantité</th>
                                            <th>Montant</th>
                                            <th>Mode</th>
                                            <th>Référence</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($history as $row):
                                            $qty = isset($row['quantite']) ? max(1, (int)$row['quantite']) : 1;
                                            $amountDisplay = isset($row['montant_total']) && $row['montant_total'] !== null
                                                ? number_format((float)$row['montant_total'], 2) . ' TND'
                                                : ($row['type_evenement'] === 'payant'
                                                    ? number_format($qty * (float)($row['prix'] ?? 0), 2) . ' TND'
                                                    : 'Gratuit');
                                            $modeDisplay = $row['mode_paiement'] ?? ($row['type_evenement'] === 'payant' ? 'Carte' : 'Gratuit');
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['prenom'] . ' ' . $row['nom']) ?></td>
                                            <td><?= htmlspecialchars($row['email']) ?></td>
                                            <td><?= htmlspecialchars($row['titre']) ?></td>
                                            <td>
                                                <?= !empty($row['date_evenement']) ? date('d/m/Y', strtotime($row['date_evenement'])) : '-' ?>
                                                <?php if (!empty($row['heure_evenement'])): ?>
                                                    <div class="small text-muted"><?= substr($row['heure_evenement'],0,5) ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?=
                                                    $row['statut'] == 'acceptée' ? 'success' :
                                                    ($row['statut'] == 'en attente' ? 'warning' : 'danger');
                                                ?>">
                                                    <?= $row['statut'] ?>
                                                </span>
                                            </td>
                                            <td><?= $qty ?></td>
                                            <td><?= $amountDisplay ?></td>
                                            <td><?= htmlspecialchars($modeDisplay) ?></td>
                                            <td><?= htmlspecialchars($row['reference_paiement'] ?? '-') ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-info mb-0">
                                Aucun historique pour le moment.
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

<?php include 'assets/layout_bottom.php'; ?>

