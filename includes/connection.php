<?php
$dbName = "gestionfacture";  // Nom de la base de données
$utilisateur = "root";  // Nom d'utilisateur pour MySQL
$motdepasse = '';  // Mot de passe pour l'utilisateur
$host = 'localhost';  // Hôte de la base de données (généralement localhost)

try {
    // Connexion à la base de données avec la variable $dbName
    $pdo = new PDO("mysql:host=$host;dbname=$dbName", $utilisateur, $motdepasse);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Erreur de connexion : ' . $e->getMessage();
    exit();
}
?>
