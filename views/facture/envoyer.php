<?php
    include 'includes/connection.php';
    require_once __DIR__ . '/../../vendor/autoload.php';
    require_once __DIR__ . '/../../models/Facture.php';
    require_once __DIR__ . '/../../models/Devis.php';

    use Dompdf\Dompdf;
    use Dompdf\Options;
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    // Obtenir l'ID de la facture depuis l'URL
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    $factureModel = new Facture($pdo);
    $devisModel = new Devis($pdo);
    $facture = $factureModel->obtenirFactureParId($id);
    $ligneFactures = $devisModel->obtenirLignesParDevis($facture['devis_id']);

    if (!$facture) {
        die("Facture introuvable.");
    }

    $client = $factureModel->obtenirClientParFacture($facture['client_id']);

    $options = new Options();
    $options->set('defaultFont', 'Arial');
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
                <?php foreach ($ligneFactures as $ligne): ?>
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

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Enregistrer le PDF
    $pdfPath = __DIR__ . "/facture_$id.pdf";
    file_put_contents($pdfPath, $dompdf->output());

    // Envoyer le PDF par email
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'votre_email@gmail.com';
        $mail->Password = 'password';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('votre_email@gmail.com', 'Votre Entreprise');
        $mail->addAddress('client_email@gmail.com', $client['nom']);

        $mail->addAttachment($pdfPath);
        $mail->isHTML(true);
        $mail->Subject = 'Votre facture';
        $mail->Body = "Bonjour {$client['nom']},<br><br>Veuillez trouver ci-joint votre facture.<br><br>Cordialement,<br>Votre Entreprise.";

        $mail->send();
        $_SESSION['facture_envoyee'] = "Facture envoyée avec succès à " . htmlspecialchars($client['nom']); // Message de succès
    header("Location: index.php?entity=facture&action=index"); // Redirection vers la page des factures
    exit();
    } catch (Exception $e) {
        echo "Erreur lors de l'envoi de l'email : {$mail->ErrorInfo}";
    }
    ?>
