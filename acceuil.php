<?php
//require_once 'config.php'; // Connexion à la base de données

// Récupération des statistiques
$sql_clients = "SELECT COUNT(*) as total FROM client";
$sql_devis = "SELECT COUNT(*) as total FROM devis";
$sql_factures = "SELECT COUNT(*) as total FROM facture";
$sql_evenements = "SELECT COUNT(*) as total FROM evenement"; // Exemple pour le nombre d'événements

// Devis et factures par état
$sql_devis_par_etat = "SELECT statut, COUNT(*) as total FROM devis GROUP BY statut";
$sql_factures_par_etat = "SELECT statut, COUNT(*) as total FROM facture GROUP BY statut";

// Récupérer les données
$clients = $pdo->query($sql_clients)->fetch(PDO::FETCH_ASSOC)['total'];
$devis = $pdo->query($sql_devis)->fetch(PDO::FETCH_ASSOC)['total'];
$factures = $pdo->query($sql_factures)->fetch(PDO::FETCH_ASSOC)['total'];
$evenements = $pdo->query($sql_evenements)->fetch(PDO::FETCH_ASSOC)['total'];

// Devis et Factures par Etat
$devis_par_etat = $pdo->query($sql_devis_par_etat)->fetchAll(PDO::FETCH_ASSOC);
$factures_par_etat = $pdo->query($sql_factures_par_etat)->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; text-align: center; }
        .container { width: 90%; margin: auto; padding: 20px; }
        .cards { display: flex; justify-content: space-around; }
        .card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); width: 30%; }
        h2 { color: #333; }
        canvas { background: white; border-radius: 10px; padding: 10px; width: 100%; height: 300px; }
        .charts { display: flex; justify-content: space-between; }
    </style>
</head>
<body>


   
    
    <div class="cards">
        <div class="card">
            <h2>👥 Clients</h2>
            <p><?= $clients; ?></p>
        </div>
        <div class="card">
            <h2>📄 Devis</h2>
            <p><?= $devis; ?></p>
        </div>
        <div class="card">
            <h2>💰 Factures</h2>
            <p><?= $factures; ?></p>
        </div>
    </div>
   
        
    




</body>
</html>
