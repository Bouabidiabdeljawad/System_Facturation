<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';



$mail = new PHPMailer(true);  

try {
    
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';  
    $mail->SMTPAuth = true;
    $mail->Username = 'votre_email@gmail.com';  
    $mail->Password = 'votre mot de passe ';  
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;  

    
    $mail->setFrom('votre_email@gmail.com', 'Votre Nom');  
    $mail->addAddress('votre_email@gmail.com', 'Destinataire');  

    
    $mail->isHTML(true);
    $mail->Subject = 'Code de Validation de votre compte';
    $mail->Body    = 'entre le code pour active votre compte.'.$_SESSION['validation_code'];
    $mail->AltBody = 'Ceci est un test d\'envoi d\'email avec PHPMailer. (version texte)'.$_SESSION['validation_code'];

    
    $mail->send();
    echo 'L\'email a été envoyé avec succès';
} catch (Exception $e) {
    echo "L'email n'a pas pu être envoyé. Erreur: {$mail->ErrorInfo}";
}
?>
