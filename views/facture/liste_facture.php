<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Factures</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .table-container {
            padding: 20px;
            width: 100%;
        }
        .table {
            margin-top: 20px;
            width: 100%;
        }
        .table thead {
            background-color: #343a40;
            color: white;
        }
        .table tbody tr:nth-child(odd) {
            background-color: #f2f2f2;
        }
        .table tbody tr:hover {
            background-color: #e9ecef;
        }
        .btn-actions {
            display: flex;
            gap: 5px;
        }
    </style>
</head>
<body>
<?php if (isset($_SESSION['facture_envoyee'])) : ?>
    <div class="alert alert-success text-center">
        <?= htmlspecialchars($_SESSION['facture_envoyee']); ?>
    </div>
    <?php unset($_SESSION['facture_envoyee']);  ?>
<?php endif; ?>
<div class="table-container">
    <h3 class="text-center text-primary mb-4"><i class="fas fa-file-invoice"></i> Liste des Factures</h3>
    <?php if (!empty($factures)) : ?>
        <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th># Facture</th>
                    <th>Intitulé</th>
                    <th>Date de Création</th>
                    <th>Statut</th>
                    <th>Client</th>
                    <th>Total TTC (€)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($factures as $facture) : ?>
                    <tr>
                        <td><?= htmlspecialchars($facture['num_facture']) ?></td>
                        <td><?= htmlspecialchars($facture['intitule']) ?></td>
                        <td><?= htmlspecialchars($facture['date_creation']) ?></td>
                        <td><?= htmlspecialchars($facture['statut']) ?></td>
                        <td>
                        <form method="POST" action="index.php?entity=facture&action=modifierStatut&id=<?= $facture['id'] ?>" style="display:flex; align-items:center;">
                                <i class="<?= $facture['statut'] === 'en attente' ? 'fas fa-clock text-warning' : ($facture['statut'] === 'payee' ? 'fas fa-check-circle text-success' : 'fas fa-times-circle text-danger') ?>" style="margin-right: 8px;"></i>
                                <select name="statut" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="en attente" <?= $facture['statut'] === 'en attente' ? 'selected' : '' ?>>En attente</option>
                                    <option value="payee" <?= $facture['statut'] === 'payee' ? 'selected' : '' ?>>Paye</option>
                                    <option value="annulee" <?= $facture['statut'] === 'annulee' ? 'selected' : '' ?>>Annulee</option>
                                </select>
                            </form>
                        </td>
                        <td><?= htmlspecialchars($facture['client_nom'] ?? 'N/A') ?></td>
                        <td><?= number_format($facture['montant_total_ttc'], 2) ?></td>
                        <td>
                            <div class="btn-actions">
                                <a href="views/facture/affiche.php?id=<?= $facture['id'] ?>" class="btn btn-sm btn-primary" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                    <a href="index.php?entity=facture&action=send&id=<?= $facture['id'] ?>" class="btn btn-sm btn-success" title="Envoyer">
                                        <i class="fas fa-paper-plane"></i>
                                    </a>
                                <a href="index.php?entity=facture&action=delete&id=<?= $facture['id'] ?>" class="btn btn-sm btn-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette facture ?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <div class="alert alert-warning text-center">
            Aucune facture disponible.
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
