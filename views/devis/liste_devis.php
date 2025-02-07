<?php
include 'includes/connection.php';
require_once 'models/Client.php';
require_once 'models/Devis.php';

$clientModel = new Client($pdo);
$devisModel = new Devis($pdo);

$devisList = $devisModel->obtenirTousLesDevis();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'], $_POST['statut'])) {
    $id = intval($_GET['id']);
    $nouveauStatut = $_POST['statut'];
    $devisModel->modifierStatut($id, $nouveauStatut);
    header('Location: index.php?entity=devis&action=liste');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Devis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
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
        .btn-ajouter {
            background-color:rgb(8, 124, 241);
            
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
    }
    </style>
</head>
<body>
<?php if (isset($_SESSION['devis_envoyee'])) : ?>
    <div class="alert alert-success text-center">
        <?= htmlspecialchars($_SESSION['devis_envoyee']); ?>
    </div>
    <?php unset($_SESSION['facture_envoyee']);  ?>
<?php endif; ?>
<div class="table-container">
    <h3 class="text-center text-primary mb-4"><i class="fas fa-file-invoice"></i> Liste des Devis</h3>
    <?php if (!empty($devisList)) : ?>
        <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th># Numéro</th>
                    <th>Intitulé</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Total HT (€)</th>
                    <th>TVA (%)</th>
                    <th>Total TTC (€)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($devisList as $devis) : 
                    $client = $clientModel->obtenirClientParId($devis['client_id']);
                ?>
                    <tr>
                        <td><?= htmlspecialchars($devis['num']) ?></td>
                        <td><?= htmlspecialchars($devis['Intitule']) ?></td>
                        <td><?= htmlspecialchars($client['nom']) ?></td>
                        <td><?= htmlspecialchars($devis['date_creation']) ?></td>
                        <td>
                            <form method="POST" action="index.php?entity=devis&action=modifierStatut&id=<?= $devis['id'] ?>" style="display:flex; align-items:center;">
                                <i class="<?= $devis['statut'] === 'en attente' ? 'fas fa-clock text-warning' : ($devis['statut'] === 'valide' ? 'fas fa-check-circle text-success' : 'fas fa-times-circle text-danger') ?>" style="margin-right: 8px;"></i>
                                <select name="statut" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="en attente" <?= $devis['statut'] === 'en attente' ? 'selected' : '' ?>>En attente</option>
                                    <option value="valide" <?= $devis['statut'] === 'valide' ? 'selected' : '' ?>>Accepté</option>
                                    <option value="refuse" <?= $devis['statut'] === 'refuse' ? 'selected' : '' ?>>Refusé</option>
                                </select>
                            </form>
                        </td>
                        <td><?= number_format($devis['total_ht'], 2) ?> €</td>
                        <td><?= htmlspecialchars($devis['tva']) ?>%</td>
                        <td><?= number_format($devis['total_ttc'], 2) ?> €</td>
                        <td>
                            <div class="btn-actions">
                                <a href="index.php?entity=devis&action=imprimer&id=<?= $devis['id'] ?>" class="btn btn-sm btn-primary" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="index.php?entity=devis&action=send&id=<?= $devis['id'] ?>" class="btn btn-sm btn-success" title="Envoyer">
                                    <i class="fas fa-paper-plane"></i>
                                </a>
                                <?php if ($devis['statut'] === 'valide') : ?>
                                    <a href="index.php?entity=facture&action=create&id=<?= $devis['id'] ?>" class="btn btn-sm btn-success" title="Facturer">
                                        <i class="fas fa-file-invoice"></i>
                                    </a>
                                <?php else : ?>
                                    <a href="index.php?entity=devis&action=afficherLigneDevis&id=<?= $devis['id'] ?>" class="btn btn-sm btn-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                <?php endif; ?>
                                <a href="index.php?entity=devis&action=delete&id=<?= $devis['id'] ?>" class="btn btn-sm btn-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce devis ?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                                
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="text-center mt-4">
            <a href="index.php?entity=devis&action=create" class="btn btn-success btn-ajouter">
                <i class="fas fa-plus"></i> 
            </a>
        </div>
    <?php else : ?>
        <div class="alert alert-warning text-center">
            Aucun devis disponible.
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
