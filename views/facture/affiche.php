<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../includes/connection.php';
require_once __DIR__ . '/../../models/Devis.php';
require_once __DIR__ . '/../../models/Facture.php';

use Dompdf\Dompdf;
use Dompdf\Options;

error_reporting(E_ALL);
ini_set('display_errors', '0');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$factureModel = new Facture($pdo);
$devisModel = new Devis($pdo);

$facture = $factureModel->obtenirFactureParId($id); // Méthode à implémenter pour obtenir une facture par ID
$ligneFacture = $devisModel->obtenirLignesParDevis($facture['devis_id']); // Méthode à implémenter pour obtenir les lignes de la facture
$client = $devisModel->obtenirClientParDevis($facture['client_id']); // Méthode déjà implémentée pour obtenir le client du devis

$options = new Options();
$options->set('defaultFont', 'Arial');
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);

$dompdf = new Dompdf($options);

ob_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture N° <?= htmlspecialchars($facture['num_facture']) ?></title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            margin-bottom: 20px;
            border: none;
        }
        .header-table td {
            vertical-align: top;
            padding: 10px;
        }
        .header-table img {
            width: 120px;
        }
        .header-table .title {
            text-align: right;
        }
        .header-table .title h3 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .header-table .title p {
            margin: 5px 0;
            font-size: 10px;
            color: #555;
        }
        .company-info, .client-info {
            margin-top: 20px;
        }
        h5 {
            margin-bottom: 10px;
            color: #333;
        }
        p {
            margin: 5px 0;
            font-size: 10px;
            color: #555;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            border: 1px solid #ddd;
        }
        table th, table td {
            padding: 5px 5px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .service_produit th {
            color: white;
            background-color: rgb(30, 116, 222);
        }
        table .total {
            font-weight: bold;
        }
        .totals {
            margin-top: 30px;
            text-align: right;
        }
        .totals table {
            width: 300px;
            margin-left: auto;
            border: none;
        }
        .totals th, .totals td {
            padding: 5px;
            text-align: right;
        }
        .totals td {
            border-top: 1px solid #ddd;
        }
        .client-info {
            margin-left: 500px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête -->
        <table class="header-table">
            <tr>
                <td style="width: 30%; border: none;">
                    <img src="data:image/png;base64,<?= base64_encode(file_get_contents('C:/xampp/htdocs/GF/img/logo.png')) ?>" alt="Logo">
                </td>
                <td style="width: 40%; border: none;"></td>
                <td class="title" style="width: 40%; border: none;">
                    <h3>Facture N  <?= htmlspecialchars($facture['num_facture']) ?></h3>
                    <p>Date : <?= htmlspecialchars($facture['date_creation']) ?></p>
                </td>
            </tr>
        </table>

        <!-- Infos Société -->
        <div class="company-info">
            <h5>Societe Online</h5>
            <p>Cyber Parc Djerba</p>
            <p>4180 - Djerba</p>
            <p>+216 58 165 882</p>
            <p>Email :</p>
        </div>

        <!-- Infos Client -->
        <div class="client-info">
            <h5>Client : <?= htmlspecialchars($client['nom']) ?></h5>
            <p>Adresse : <?= htmlspecialchars($client['adresse']) ?></p>
            <p><?= htmlspecialchars($client['ville']) ?>, <?= htmlspecialchars($client['code_postal']) ?></p>
        </div>

        <!-- Description -->
        <h4>Objet : <?= htmlspecialchars($facture['intitule']) ?></h4>
        <table class="service_produit">
            <thead>
                <th>Description</th>
                <th>Unite</th>
                <th>Quantite</th>
                <th>Prix Unitaire HT</th>
                <th>TVA</th>
                <th>Total HT</th>
            </thead>
            <tbody>
                <?php foreach ($ligneFacture as $ligne): ?>
                    <tr>
                        <td><?= htmlspecialchars($ligne['description']) ?></td>
                        <td><?= htmlspecialchars($ligne['unite']) ?></td>
                        <td><?= htmlspecialchars($ligne['quantite']) ?></td>
                        <td><?= htmlspecialchars($ligne['prix_unitaire']) ?> €</td>
                        <td>10%</td>
                        <td><?= htmlspecialchars($ligne['total']) ?> €</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Totaux -->
        <div class="totals">
            <table style="font-family: Arial;">
                <tr>
                    <th>Total HT :</th>
                    <td><?= htmlspecialchars($facture['montant_total_ht']) ?> €</td>
                </tr>
                <tr>
                    <th>TVA :</th>
                    <td><?= htmlspecialchars($facture['total_tva']) ?> €</td>
                </tr>
                <tr class="total">
                    <th>Total TTC :</th>
                    <td><?= htmlspecialchars($facture['montant_total_ttc']) ?> €</td>
                </tr>
            </table>
        </div>
        
        <div>
            <p>Cette facture est arretee à la somme de <span style="color: red;">Montant en toute lettre</span>.</p>
        </div>
        
        <div style="margin-left: 500px;">
            <p>Cachet et signature</p>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>Nom de l entreprise et adresse</p>
        </div>
    </div>
</body>
</html>
<?php
$html = ob_get_clean();

// Charger le contenu HTML dans Dompdf
$dompdf->loadHtml($html);

// (Optionnel) Définir la taille du papier et l'orientation
$dompdf->setPaper('A4', 'portrait');

// Rendre le PDF
$dompdf->render();

// Générer le fichier PDF
$dompdf->stream("facture_$id.pdf", ["Attachment" => false]); // Afficher dans le navigateur sans télécharger
?>
