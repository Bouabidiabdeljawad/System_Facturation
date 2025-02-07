<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../includes/connection.php';
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../models/Client.php';  

$client = new Client($pdo);
$clients = $client->obtenirTousLesClientsAvecAnniversaireAujourdhui();  

if ($clients) {
    $mail = new PHPMailer(true);  

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'abdeljawadbouabidi0@gmail.com';
        $mail->Password = 'uejb qdfg gtln vpac';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('abdeljawadbouabidi0@gmail.com', 'Online Societe');

        foreach ($clients as $client) {
            $mail->clearAddresses();
            $mail->addAddress($client['email'], $client['nom']);

            $mail->isHTML(true);
            $mail->Subject = 'Joyeux anniversaire à ' . $client['nom'] . ' !';

            $mail->Body = "
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
                        .email-container { max-width: 600px; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); text-align: center; }
                        h1 { color: #ff6f61; }
                        p { font-size: 16px; color: #333; }
                    </style>
                </head>
                <body>
                    <div class='email-container'>
                        <h1>🎉 Joyeux Anniversaire, " . $client['nom'] . " ! 🎂</h1>
                        <p>Objet : Joyeux anniversaire à " . $client['nom'] . " !</p>
                        <p>Cher(e) " . $client['nom'] . ",</p>
                        <p>En ce jour spécial, toute l’équipe de <b>Societe Online</b> tient à vous adresser ses vœux les plus sincères à l’occasion de votre anniversaire "
                        
                        . ($client["type_client"] == "societe" ? "de votre entreprise " : "de votre naissance") .
                        
                        ".</p>
                        <p>Nous souhaitons que cette nouvelle année soit synonyme de réussite, de prospérité et d’épanouissement, tant sur le plan professionnel que personnel.</p>
                        <p>Votre engagement et votre vision inspirent et contribuent au succès de notre partenariat.</p>
                        <p>Nous sommes ravis de collaborer avec vous et espérons continuer à construire ensemble de beaux projets.</p>
                        <p>Très belle journée à vous et à toute votre équipe !</p>
                       
                    </div>
                </body>
                </html>
            ";

            $mail->AltBody = "Joyeux Anniversaire, " . $client['nom'] . " ! En ce jour spécial, toute l’équipe de Votre Entreprise tient à vous adresser ses vœux les plus sincères.";

            $mail->send();
            
        }

    } catch (Exception $e) {
        header('index.php');
    }
} else {
    header('index.php');
}
?>
