<?php
include 'includes/connection.php';
require_once 'models/Client.php';

$clientModel = new Client($pdo);
$clients = $clientModel->obtenirTousLesClients();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Devis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .btn-remove {
            background-color: #dc3545;
            color: white;
        }
        .btn-remove:hover {
            background-color: #c82333;
        }
        fieldset {
            border: 2px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 10px;
            position: relative;
        }
        .btn-delete-line {
            font-size: 16px;
            cursor: pointer;
            color: red;
            border: none;
            background: none;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-primary text-white text-center">
            <h3>Créer un Devis  </h3>
        </div>
        <div class="card-body">
        <form method="POST" action="index.php?entity=devis&action=create">
    
    <!-- Sélection du client -->
    <div class="mb-3">
        <label for="client_id" class="form-label">Sélectionner un Client</label>
        <select id="client_id" name="client_id" class="form-select" required>
            <option value="">Choisir un client</option>
            <?php foreach ($clients as $client) : ?>
                <option value="<?= $client['id'] ?>"><?= $client['nom'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Ajout du champ Intitule -->
    <div class="mb-3">
        <label for="intitule" class="form-label">Intitulé</label>
        <input type="text" id="intitule" name="intitule" class="form-control" required>
    </div>

    <!-- Section Produits/Services -->
    <fieldset id="produits-container">
        <legend>Produits/Services</legend>
        <div id="ligne-produits">
            <div class="row produit-item">
                <div class="col-md-3">
                    <label class="form-label">Désignation</label>
                    <input type="text" class="form-control" name="descriptions[]" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Quantité</label>
                    <input type="number" class="form-control quantite" name="quantites[]" min="1" value="1" required onchange="calculerTotal(this)">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Prix Unitaire (€)</label>
                    <input type="number" class="form-control prix_unitaire" name="prix_unitaires[]" step="0.01" required onchange="calculerTotal(this)">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Total (€)</label>
                    <input type="text" class="form-control prix_total" name="prix_totals[]" readonly>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn-delete-line" onclick="supprimerLigne(this)">❌</button>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-success mt-2" onclick="ajouterLigne()">+ Ajouter un produit/service</button>
    </fieldset>

    <!-- Totaux -->
    <div class="mt-4">
        <div class="row">
            <div class="col-md-4">
                <label for="total_ht" class="form-label">Total HT (€)</label>
                <input type="number" id="total_ht" name="total_ht" class="form-control" readonly>
            </div>
            <div class="col-md-4">
                <label for="tva" class="form-label">TVA (%)</label>
                <input type="number" id="tva" name="tva" class="form-control" value="20" onchange="calculerTotalTTC()">
            </div>
            <div class="col-md-4">
                <label for="total_ttc" class="form-label">Total TTC (€)</label>
                <input type="text" id="total_ttc" name="total_ttc" class="form-control" readonly>
            </div>
        </div>
    </div>

    <!-- Bouton soumettre -->
    <div class="text-center mt-4">
        <button type="submit" class="btn btn-primary">Créer Devis</button>
    </div>
</form>

        </div>
    </div>
</div>

<script>
    function ajouterLigne() {
        let container = document.getElementById('ligne-produits');
        let newRow = container.children[0].cloneNode(true);
        newRow.querySelectorAll('input').forEach(input => input.value = '');
        container.appendChild(newRow);
    }

    function supprimerLigne(button) {
        let row = button.closest('.produit-item');
        let container = document.getElementById('ligne-produits');
        if (container.children.length > 1) {
            row.remove();
            calculerTotalTTC();
        }
    }

    function calculerTotal(input) {
        let row = input.closest('.produit-item');
        let quantite = row.querySelector('.quantite').value;
        let prixUnitaire = row.querySelector('.prix_unitaire').value;
        let prixTotal = row.querySelector('.prix_total');

        prixTotal.value = (quantite * prixUnitaire).toFixed(2);
        calculerTotalTTC();
    }

    function calculerTotalTTC() {
        let totalHT = 0;
        document.querySelectorAll('.prix_total').forEach(input => {
            totalHT += parseFloat(input.value) || 0;
        });

        document.getElementById('total_ht').value = totalHT.toFixed(2);
        let tva = parseFloat(document.getElementById('tva').value) || 0;
        let totalTTC = totalHT + (totalHT * tva / 100);
        document.getElementById('total_ttc').value = totalTTC.toFixed(2);
    }
</script>

</body>
</html>
