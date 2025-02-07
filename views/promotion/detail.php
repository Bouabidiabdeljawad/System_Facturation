<?php
include 'includes/connection.php';

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Promotion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Roboto', sans-serif;
        }
        .container {
            margin-top: 30px;
        }
        .promotion-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
        }
        .promotion-card img {
            height: 350px;
            object-fit: cover;
            border-bottom: 2px solid #ddd;
        }
        .card-body {
            padding: 20px;
        }
        .card-title {
            font-size: 1.8rem;
            font-weight: bold;
            color: #007bff;
        }
        .card-text {
            color: #6c757d;
        }
        .btn-back {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 5px;
        }
        .btn-back:hover {
            background-color: #218838;
        }
        .promo-info {
            margin-top: 20px;
        }
        .promo-info p {
            font-size: 1.1rem;
            margin: 10px 0;
        }
        .promo-info i {
            color: #007bff;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Détails de la promotion -->
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card promotion-card">
                <?php if (!empty($promotion->image_url)): ?>
                    <img src="<?= htmlspecialchars($promotion->image_url) ?>" class="card-img-top" alt="Image de la promotion">
                <?php else: ?>
                    <div class="card-img-top d-flex justify-content-center align-items-center" style="height: 350px; background-color: #f0f0f0;">
                        <i class="fas fa-image fa-5x text-muted"></i> <!-- Icône pour image inconnue -->
                    </div>
                <?php endif; ?>
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($promotion->titre) ?></h5>
                    <p class="card-text"><?= nl2br(htmlspecialchars($promotion->description)) ?></p>
                    
                    <div class="promo-info">
                        <p><i class="fas fa-calendar-day"></i> <strong>Date de début :</strong> <?= htmlspecialchars($promotion->date_debut) ?></p>
                        <p><i class="fas fa-calendar-times"></i> <strong>Date de fin :</strong> <?= htmlspecialchars($promotion->date_fin) ?></p>
                        
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="index.php?entity=promotion" class="btn btn-back">
                        <i class="fas fa-arrow-left"></i> Retour à la liste des promotions
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
