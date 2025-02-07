<?php
include 'includes/connection.php';
require_once __DIR__ . '/../../models/Devis.php';
require_once __DIR__ . '/../../models/Facture.php';

// Inclure le modèle Devis

// Récupérer l'ID du devis depuis l'URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Charger les données du devis et des lignes associées
$devisModel = new Devis($pdo);
$factureModel = new Facture($pdo);
// Assurez-vous que $pdo est une instance PDO valide
$devis = $devisModel->obtenirDevisParId($id);
$ligneDevis = $devisModel->obtenirLignesParDevis($id);
$montantTotalFactures = $factureModel->obtenirMontantTotalFactures($id);
$montantTotalDevis = $factureModel->obtenirMontantTotalDevis($id);

// Vérifier si le devis existe
if (!$devis) {
    die("Devis introuvable.");
}

// Charger les informations du client associées
$client = $devisModel->obtenirClientParDevis($devis['client_id']); // Méthode pour obtenir les détails du client
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer une Facture</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        // Fonction pour recalculer le montant total de la facture et vérifier la condition
        function recalculerFacture() {
            var pourcentage = parseFloat(document.getElementById('pourcentage').value) || 0;
            var montantDevis = parseFloat("<?= $montantTotalDevis ?>"); // Utiliser le montant total du devis
            var montantFactures = parseFloat("<?= $montantTotalFactures ?>"); // Utiliser le montant total des factures existantes

            // Calcul du montant HT en fonction du pourcentage
            var montantHTCalculé = montantDevis * (pourcentage / 100);

            // Afficher le montant HT calculé
            document.getElementById('total_ht').value = montantHTCalculé.toFixed(2);
            var montantTTCalculé = montantHTCalculé * (1 + <?= $devis['tva'] ?> / 100);
           
            // Affichage du montant de la facture dans le champ de saisie
            document.getElementById('montant_total_ttc').value = montantTTCalculé.toFixed(2);

            // Vérification si la somme des factures et du montant de la nouvelle facture dépasse le montant du devis
            if ((montantFactures + montantTTCalculé) > montantDevis) {
                document.getElementById('error-message').textContent = "Le montant total des factures ne doit pas dépasser le montant du devis : " + (montantDevis - montantFactures).toFixed(2); // Afficher un message d'erreur
                document.getElementById('submit-button').disabled = true;  // Désactiver le bouton
            } else {
                document.getElementById('error-message').textContent = ""; // Réinitialiser le message d'erreur
                document.getElementById('submit-button').disabled = false; // Réactiver le bouton
            }
        }

        // Événements pour recalculer dès que le pourcentage change
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('pourcentage').addEventListener('input', recalculerFacture);
        });
    </script>
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center mb-4">Créer une Facture à partir du Devis N°<?= htmlspecialchars($devis['num']) ?></h1>
    <form action="index.php?entity=facture&action=create" method="POST">
        <!-- Informations Client -->
        <div class="card mb-4">
            <div class="card-header">Informations du Client</div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="client_nom" class="form-label">Nom du Client</label>
                        <input type="hidden" name="devis_id" value="<?= htmlspecialchars($devis['id']) ?>">
                        <input type="hidden" name="client_id" value="<?= htmlspecialchars($client['id']) ?>">
                        <input type="text" id="client_nom" class="form-control" value="<?= htmlspecialchars($client['nom']) ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="client_adresse" class="form-label">Adresse</label>
                        <input type="text" id="client_adresse" class="form-control" value="<?= htmlspecialchars($client['adresse']) ?>" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="client_ville" class="form-label">Ville</label>
                        <input type="text" id="client_ville" class="form-control" value="<?= htmlspecialchars($client['ville']) ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="client_code_postal" class="form-label">Code Postal</label>
                        <input type="text" id="client_code_postal" class="form-control" value="<?= htmlspecialchars($client['code_postal']) ?>" readonly>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations de la Facture -->
        <div class="card mb-4">
            <div class="card-header">Informations de la Facture</div>
            <div class="card-body">
                <input type="hidden" name="devis_id" value="<?= htmlspecialchars($devis['id']) ?>">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="intitule" class="form-label">Intitulé</label>
                        <input type="text" name="intitule" id="intitule" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="pourcentage" class="form-label">Pourcentage du montant total du devis</label>
                        <input type="number" name="pourcentage" id="pourcentage" class="form-control" required min="1" max="100">
                    </div>
                    <div class="col-md-6">
                        <label for="mode_paiement" class="form-label">Mode de Paiement</label>
                        <select name="mode_paiement" id="mode_paiement" class="form-control" required>
                            <option value="virement">Virement</option>
                            <option value="cheque">Chèque</option>
                            <option value="especes">Espèces</option>
                            <option value="carte">Carte Bancaire</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Détails des Articles -->
        <div class="card mb-4">
            <div class="card-header">Détails des Articles</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Description</th>
                        <th>Quantité</th>
                        <th>Prix Unitaire</th>
                        <th>Total HT</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($ligneDevis as $ligne): ?>
                        <tr>
                            <td><input type="text" name="descriptions[]" class="form-control" value="<?= htmlspecialchars($ligne['description']) ?>" readonly></td>
                            <td><input type="number" name="quantites[]" class="form-control" value="<?= htmlspecialchars($ligne['quantite']) ?>" readonly></td>
                            <td><input type="number" name="prix_unitaires[]" class="form-control" value="<?= htmlspecialchars($ligne['prix_unitaire']) ?>" readonly></td>
                            <td><input type="number" name="totals[]" class="form-control" value="<?= htmlspecialchars($ligne['total']) ?>" readonly></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Totaux -->
        <div class="card mb-4">
            <div class="card-header">Totaux</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label for="total_ht" class="form-label">Montant Total HT</label>
                        <input type="number" name="total_ht" id="total_ht" class="form-control" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="total_tva" class="form-label">Total TVA</label>
                        <input type="number" name="total_tva" id="total_tva" class="form-control" value="<?= htmlspecialchars($devis['tva']) ?>" readonly>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="montant_total_ttc" class="form-label">Montant de la Facture</label>
                        <input type="number" name="montant_total_ttc" id="montant_total_ttc" class="form-control" readonly>
                    </div>
                </div>
            </div>
        </div>

        <!-- Erreur -->
        <div id="error-message" class="text-danger mb-3"></div>

        <!-- Boutons -->
        <div class="d-flex justify-content-end">
            <button type="submit" id="submit-button" class="btn btn-success">Créer la Facture</button>
            <button type="reset" class="btn btn-secondary ms-2">Annuler</button>
        </div>
    </form>
</div>
</body>
</html>
