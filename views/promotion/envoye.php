<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../includes/connection.php';
require_once __DIR__ . '/../../models/Promotion.php';

// Récupération des données de la promotion
$id = $_GET['id'];
$promotion = new Promotion($pdo);
$promo = $promotion->obtenirPromotionParId($id);
if (!isset($_POST['clients'])) {
    echo "Erreur : La liste des clients n'est pas envoyée.";
    exit;
}

if (empty($_POST['clients'])) {
    echo "Erreur : Aucun client sélectionné.";
    exit;
}
$titre = $promo->getTitre();
$description = $promo->getDescription();
$dateDebut = $promo->getDateDebut();
$dateFin = $promo->getDateFin();
$imagePath = __DIR__ . '/../../' . $promo->getImageUrl();

// Liste des destinataires récupérés depuis la session ou le formulaire
if (isset($_POST['clients']) && !empty($_POST['clients'])) {
    $destinataires = [];

    // Récupérer les détails des clients
    $clients = $_POST['clients']; // Liste des IDs de clients sélectionnés

    foreach ($clients as $clientId) {
        // Récupérer le client depuis la base de données
        $clientQuery = $pdo->prepare('SELECT nom, prenom, email FROM client WHERE id = :id');
        $clientQuery->execute(['id' => $clientId]);
        $client = $clientQuery->fetch();

        if ($client) {
            $destinataires[] = ['email' => $client['email'], 'nom' => $client['nom'] . ' ' . $client['prenom']];
        }
    }

    $mail = new PHPMailer(true);

    try {
        // Configuration du serveur SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'votre_email@gmail.com';
        $mail->Password = 'password';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Expéditeur
        $mail->setFrom('votre_email@gmail.com', 'Nom de l\'Entreprise');

        // Envoi d'emails à chaque destinataire
        foreach ($destinataires as $destinataire) {
            $mail->clearAddresses();
            $mail->addAddress($destinataire['email'], $destinataire['nom']);

            // Contenu de l'email
            $mail->isHTML(true);
            $mail->Subject = "Promotion : $titre";

            // Générer le contenu HTML du mail avec un design attractif
            $htmlContent = "
                <div style='font-family: Arial, sans-serif; color: #333; background-color: #f4f4f4; padding: 20px;'>
                    <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 10px; padding: 30px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);'>
                        <div style='text-align: center;'>
                            <h1 style='color: #0066cc;'>$titre</h1>
                        </div>
                        <p style='font-size: 16px; line-height: 1.5;'> 
                            <strong>Valable du :</strong> $dateDebut <strong>au :</strong> $dateFin
                        </p>";

            if (!empty($description)) {
                $htmlContent .= "<p style='font-size: 16px; line-height: 1.5;'>$description</p>";
            }

            if (!empty($imagePath)) {
                $htmlContent .= "
                    <div style='margin-top: 20px; text-align: center;'>
                        <img src='cid:promoImage' alt='Promotion' style='max-width: 100%; height: auto; border-radius: 8px;' />
                    </div>";
                $mail->addEmbeddedImage($imagePath, 'promoImage', 'promotion.jpg');
            }

            $htmlContent .= "
                <div style='text-align: center; margin-top: 30px;'>
                    <a href='#' style='padding: 12px 25px; background-color: #0066cc; color: #ffffff; text-decoration: none; font-size: 18px; border-radius: 5px;'>Profitez de l'offre</a>
                </div>";

            $htmlContent .= "
                <div style='text-align: center; margin-top: 20px; font-size: 14px; color: #888;'>
                    <p>Online Societe - Tous droits réservés</p>
                </div>
            </div>
        </div>";

            $mail->Body = $htmlContent;

            // Envoi
            $mail->send();
        }

        echo 'Les emails ont été envoyés avec succès';
    } catch (Exception $e) {
        echo "Erreur lors de l'envoi : " . $mail->ErrorInfo;
    }
} else {
    echo "Aucun client sélectionné.";
}
?>
