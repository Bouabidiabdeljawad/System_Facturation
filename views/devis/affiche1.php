<?php
require_once __DIR__ . '/../../includes/connection.php';
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../models/Devis.php'; // Include your Devis model

use Dompdf\Dompdf;
use Dompdf\Options;

// Get the `id` parameter from the URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch the data for the specified `Devis` along with related client and ligne_devis data
$devisModel = new Devis($pdo); // Assuming $pdo is a valid PDO instance
$devis = $devisModel->obtenirDevisParId($id);
$ligneDevis = $devisModel->obtenirLignesParDevis($id);

// Check if the `Devis` exists
if (!$devis) {
    die("Devis introuvable.");
}

// Get the associated client data
$client = $devisModel->obtenirClientParDevis($devis['client_id']); // Adjust this method to fetch client details by `client_id`

// Configuration de Dompdf
$options = new Options();
$options->set('defaultFont', 'Arial');
$dompdf = new Dompdf($options);

// Generate the dynamic HTML content
ob_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devis N° <?= htmlspecialchars($devis['id']) ?></title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .header-table img {
            width: 120px;
        }
        .header-table td {
            vertical-align: middle;
            padding: 10px;
        }
        .header-table .title {
            text-align: right;
        }
        .header-table .title h3 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .header-table .title p {
            margin: 5px 0;
            font-size: 14px;
            color: #777;
        }
        .company-info, .client-info {
            margin-top: 30px;
        }
        .client-info {
            text-align: right;
        }
        h5 {
            margin-bottom: 10px;
            color: #333;
        }
        p {
            margin: 5px 0;
            font-size: 14px;
            color: #555;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            border: 1px solid #ddd;
        }
        table th, table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #d4fffe;
            font-weight: bold;
        }
        table td {
            border: 1px solid #ddd;
        }
        table .total {
            font-weight: bold;
        }
        .totals {
            margin-top: 30px;
            text-align: right;
        }
        .totals table {
            width: 250px;
            margin-left: auto;
            border: none;
        }
        .totals th {
            padding: 8px;
            font-size: 16px;
            text-align: left;
            border: none;
            background-color: transparent;
        }
        .totals td {
            padding: 8px;
            font-size: 16px;
            text-align: right;
            border-top: 1px solid #ddd;
            border-right: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
        }
        .footer {
            margin-top: 50px;
            text-align: left;
            font-size: 12px;
            color: #777;
        }
        

.header-table td {
   
    border: none; /* Retirer les bordures des cellules */
}
       
    </style>
</head>
<body>

<div class="container">
    <!-- En-tête -->
    <table class="header-table" style="border: none;">
        <tr>
            <td style="width: 25%"><img src="GF/img/logo.png" alt="Logo"></td>
            <td style="width: 35%"></td>
            <td class="title" style="background_color: #80add8;" >
                <h3 style="text-align: center;">DEVIS         N° <?= htmlspecialchars($devis['num']) ?></h3>
                <p style="text-align: left; color: #333;margin-left: 15px">Date : <?= htmlspecialchars($devis['date_creation']) ?></p>
            </td>
        </tr>
    </table>

    <div class="company-info">
    <h5><?= isset($devis['company_name']) ? htmlspecialchars($devis['company_name']) : 'Nom de l\'entreprise non disponible' ?></h5>
    <p>Adresse : <?= isset($devis['company_address']) ? htmlspecialchars($devis['company_address']) : 'Adresse non disponible' ?></p>
    <p><?= isset($devis['company_city']) ? htmlspecialchars($devis['company_city']) : 'Ville non disponible' ?>, <?= isset($devis['company_zip']) ? htmlspecialchars($devis['company_zip']) : 'Code postal non disponible' ?></p>
    <p>Téléphone : <?= isset($devis['company_phone']) ? htmlspecialchars($devis['company_phone']) : 'Téléphone non disponible' ?></p>
    <p>Email : <?= isset($devis['company_email']) ? htmlspecialchars($devis['company_email']) : 'Email non disponible' ?></p>
    </div>

    <!-- Informations du client -->
    <div class="client-info">
        <h5>Client : <?= htmlspecialchars($client['nom']) ?> <?= htmlspecialchars($client['prenom']) ?></h5>
        <p>Adresse : <?= htmlspecialchars($client['adresse']) ?></p>
        <p><?= htmlspecialchars($client['ville']) ?>, <?= htmlspecialchars($client['code_postal']) ?></p>
        <p>Téléphone : <?= htmlspecialchars($client['telephone']) ?></p>
        <p>Email : <?= htmlspecialchars($client['email']) ?></p>
    </div>

    <!-- Description du projet -->
    <h4>Description du projet</h4>
    <table>
        <thead>
            <tr>
                <th>Quantité</th>
                <th>Désignation</th>
                <th>Prix Unitaire</th>
                <th>Total HT</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ligneDevis as $ligne): ?>
                <tr>
                    <td><?= htmlspecialchars($ligne['quantite']) ?></td>
                    <td><?= htmlspecialchars($ligne['description']) ?></td>
                    <td><?= htmlspecialchars($ligne['prix_unitaire']) ?> €</td>
                    <td><?= htmlspecialchars($ligne['total']) ?> €</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Totaux -->
    <div class="totals">
        <table>
            <tr>
                <th>Total HT :</th>
                <td style="border-left: none; "><?= htmlspecialchars($devis['total_ht']) ?> €</td>
            </tr>
            <tr>
                <th>TVA (<?= htmlspecialchars($devis['tva']) ?>%) :</th>
                <td style="border-left: none; "><?= htmlspecialchars($devis['tva']) ?> €</td>
            </tr>
            <tr class="total">
                <th>Total TTC :</th>
                <td style="border-left: none; "><?= htmlspecialchars($devis['total_ttc']) ?> €</td>
            </tr>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Nous restons à votre disposition pour toute information complémentaire.<br>
        Cordialement,</p><br>
        <p>Si ce devis vous convient, veuillez nous le retourner signé précédé de la mention :<br>
        "BON POUR ACCORD ET EXECUTION DU DEVIS"</p><br>
        <table style="width: 100%; margin-top: 20px;border: none;">
            <tr>
                <td style="width: 50%;border: none;">Date :</td>
                <td style="width: 50%;border: none;">Signature :</td>
            </tr>
        </table>

    </div>
</div>

</body>
</html>
<?php
$html = ob_get_clean();

// Charger le contenu HTML dans Dompdf
$dompdf->loadHtml($html);

// Définir la taille et l'orientation de la page
$dompdf->setPaper('A4', 'portrait');

// Rendre le fichier PDF
$dompdf->render();

// Télécharger ou afficher le fichier PDF
$dompdf->stream("devis_$id.pdf", ["Attachment" => false]);
?>
