<?php
include 'includes/connection.php';
require_once 'models/Devis.php';

$devisId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Instancier le modèle Devis
$devisModel = new Devis($pdo);

// Récupérer les données du devis
$devis = $devisModel->obtenirDevisParId($devisId);
$ligneDevis = $devisModel->obtenirLignesParDevis($devisId);

if (!$devis) {
    die('Devis introuvable.');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Devis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-warning text-white text-center">
            <h3>Modifier Devis N° <?= htmlspecialchars($devis['id']) ?></h3>
        </div>
        <div class="card-body">
            <form method="POST" action="index.php?entity=devis&action=edit&id=<?= $devis['id'] ?>">
                
                <!-- Client -->
                <div class="mb-3">
                    <label for="client_id" class="form-label">Client</label>
                    <select id="client_id" name="client_id" class="form-select" required>
                        <option value="<?= $devis['client_id'] ?>"><?= htmlspecialchars($devis['client_id']) ?></option>
                        <!-- Ajouter d'autres options si nécessaire -->
                    </select>
                </div>

                <!-- Status -->
                <div class="mb-3">
                    <label for="statut" class="form-label">Statut</label>
                    <select id="statut" name="statut" class="form-select">
                        <option value="en attente" <?= $devis['statut'] == 'en attente' ? 'selected' : '' ?>>En attente</option>
                        <option value="valide" <?= $devis['statut'] == 'valide' ? 'selected' : '' ?>>Valide</option>
                        <option value="annule" <?= $devis['statut'] == 'annule' ? 'selected' : '' ?>>Annulé</option>
                    </select>
                </div>

                <!-- Produits/Services -->
                <fieldset id="produits-container">
                    <legend>Produits/Services</legend>
                    <div id="ligne-produits">
                        
                        <?php echo '<pre>'; print_r($ligneDevis); echo '</pre>'; foreach ($ligneDevis as $ligne): ?>
                            <div class="row produit-item">
                                <input type="hidden" name="ligne_ids[]" value="<?= $ligne['id'] ?>">
                                <div class="col-md-3">
                                    <label class="form-label">Désignation</label>
                                    <input type="text" class="form-control" name="descriptions[]" value="<?= htmlspecialchars($ligne['description']) ?>" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Quantité</label>
                                    <input type="number" class="form-control quantite" name="quantites[]" value="<?= htmlspecialchars($ligne['quantite']) ?>" min="1" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Prix Unitaire (€)</label>
                                    <input type="number" class="form-control prix_unitaire" name="prix_unitaires[]" value="<?= htmlspecialchars($ligne['prix_unitaire']) ?>" step="0.01" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Total (€)</label>
                                    <input type="number" class="form-control prix_total" name="prix_totals[]" value="<?= htmlspecialchars($ligne['total']) ?>" readonly>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" class="btn-delete-line btn btn-danger">❌</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn btn-success mt-2" onclick="ajouterLigne()">+ Ajouter un produit/service</button>
                </fieldset>

                <!-- Totaux -->
                <div class="mt-4">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="total_ht" class="form-label">Total HT (€)</label>
                            <input type="number" id="total_ht" name="total_ht" class="form-control" value="<?= htmlspecialchars($devis['total_ht']) ?>" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="tva" class="form-label">TVA (%)</label>
                            <input type="number" id="tva" name="tva" class="form-control" value="<?= htmlspecialchars($devis['tva']) ?>" onchange="calculerTotalTTC()">
                        </div>
                        <div class="col-md-4">
                            <label for="total_ttc" class="form-label">Total TTC (€)</label>
                            <input type="number" id="total_ttc" name="total_ttc" class="form-control" value="<?= htmlspecialchars($devis['total_ttc']) ?>" readonly>
                        </div>
                    </div>
                </div>

                <!-- Bouton Soumettre -->
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary">Enregistrer les Modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Fonction pour ajouter une ligne de produit/service
function ajouterLigne() {
    let container = document.getElementById('ligne-produits');
    let newRow = container.children[0].cloneNode(true);
    newRow.querySelectorAll('input').forEach(input => {
        input.value = '';
        // Ajouter l'écouteur pour les nouveaux champs
        input.addEventListener('input', () => calculerTotal(input));
    });
    container.appendChild(newRow);
}

// Fonction pour calculer le total d'une ligne
function calculerTotal(input) {
    let row = input.closest('.produit-item');
    let quantite = parseFloat(row.querySelector('.quantite').value) || 0;
    let prixUnitaire = parseFloat(row.querySelector('.prix_unitaire').value) || 0;
    let prixTotal = row.querySelector('.prix_total');
    prixTotal.value = (quantite * prixUnitaire).toFixed(2);

    // Recalculer les totaux globaux
    calculerTotalTTC();
}

// Fonction pour calculer les totaux globaux (HT et TTC)
function calculerTotalTTC() {
    let totalHT = 0;

    // Calculer le total HT
    document.querySelectorAll('.prix_total').forEach(input => {
        totalHT += parseFloat(input.value) || 0;
    });

    document.getElementById('total_ht').value = totalHT.toFixed(2);

    // Calculer le total TTC
    let tva = parseFloat(document.getElementById('tva').value) || 0;
    let totalTTC = totalHT + (totalHT * tva / 100);
    document.getElementById('total_ttc').value = totalTTC.toFixed(2);
}

// Ajouter des écouteurs d'événements aux champs existants
document.querySelectorAll('.quantite, .prix_unitaire').forEach(input => {
    input.addEventListener('input', () => calculerTotal(input));
});

</script>

</body>
</html>
