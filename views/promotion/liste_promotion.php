<?php

require __DIR__ . '/../../includes/connection.php';
require_once __DIR__ . '/../../models/Promotion.php';
require_once __DIR__ . '/../../models/Client.php';
$clientModel = new Client($pdo);

// Assurez-vous que vous avez récupéré les clients selon les filtres (vous pouvez adapter selon la logique de recherche).
$clients = $clientModel->rechercherClients('', 'tout', '', ''); // Exemple de récupération de clients sans filtres
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Promotions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #e9ecef;
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
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .promotion-card:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }
        .promotion-card img {
            height: 180px;
            object-fit: cover;
        }
        .card-body {
            padding: 20px;
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: bold;
        }
        .card-text {
            color: #6c757d;
        }
        .card-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }
        .btn-view {
            background-color: #4caf50;
            color: white;
        }
        .btn-edit {
            background-color: #ff9800;
            color: white;
        }
        .btn-delete {
            background-color: #f44336;
            color: white;
        }
        .btn-send {
            background-color: #007bff;
            color: white;
        }
        .btn-add {
            background-color: #007bff;
            color: white;
            border: none;
        }
        .btn-add:hover {
            background-color: #0056b3;
        }

        /* Positionner le bouton Ajouter en bas à droite */
        .btn-container {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 999;
        }

        /* Centrer le modal */
        .modal-dialog {
            max-width: 800px;
            margin: auto;
        }

        /* Recherche dans le modal */
        #searchClient {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row g-4">
        <?php if (!empty($promotions)) : ?>
            <?php foreach ($promotions as $promotion) : ?>
                <div class="col-md-4">
                    <div class="card promotion-card">
                        <img src="<?= htmlspecialchars($promotion['image_url']) ?>" class="card-img-top" alt="Image de la promotion">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($promotion['titre']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars(substr($promotion['description'], 0, 100)) ?>...</p>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <!-- Bouton pour envoyer à plusieurs clients -->
                            <a href="#" class="btn btn-send btn-sm btn-send-promotion" data-promotion-id="<?= $promotion['id'] ?>">
                                <i class="fas fa-paper-plane"></i> Envoyer
                            </a>
                            <div>
                                <!-- Bouton Modifier -->
                                <button class="btn btn-edit btn-sm" 
                                        data-promotion-id="<?= $promotion['id'] ?>" 
                                        data-promotion-title="<?= htmlspecialchars($promotion['titre']) ?>" 
                                        data-promotion-description="<?= htmlspecialchars($promotion['description']) ?>" 
                                        data-promotion-start-date="<?= $promotion['date_debut'] ?>" 
                                        data-promotion-end-date="<?= $promotion['date_fin'] ?>" 
                                        data-promotion-image-url="<?= $promotion['image_url'] ?>" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editPromotionModal">
                                    <i class="fas fa-edit"></i> Modifier
                                </button>
                                <!-- Bouton Supprimer -->
                                <a href="index.php?entity=promotion&action=delete&id=<?= $promotion['id'] ?>" class="btn btn-delete btn-sm" onclick="return confirm('Voulez-vous vraiment supprimer cette promotion ?')">
                                    <i class="fas fa-trash"></i> Supprimer
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="col-12">
                <div class="alert alert-info text-center">Aucune promotion disponible pour le moment.</div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de modification -->
<div class="modal fade" id="editPromotionModal" tabindex="-1" aria-labelledby="editPromotionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPromotionModalLabel">Modifier la Promotion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editPromotionForm" method="POST" action="edit_promotion_process.php">
                    <div class="form-group">
                        <label for="promotionTitle">Titre de la promotion :</label>
                        <input type="text" class="form-control" id="promotionTitle" name="promotion_title" required>
                    </div>
                    <div class="form-group">
                        <label for="promotionDescription">Description :</label>
                        <textarea class="form-control" id="promotionDescription" name="promotion_description" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="promotionStartDate">Date de début :</label>
                        <input type="date" class="form-control" id="promotionStartDate" name="promotion_start_date" required>
                    </div>
                    <div class="form-group">
                        <label for="promotionEndDate">Date de fin :</label>
                        <input type="date" class="form-control" id="promotionEndDate" name="promotion_end_date" required>
                    </div>
                    <div class="form-group">
    <label for="promotionImageUrl">Sélectionner une image :</label>
    <!-- Afficher l'image existante à côté du champ input -->
    <div>
        <img id="currentImage" src="path_to_existing_image.jpg" alt="Image actuelle" style="width: 150px; height: 150px;">
    </div>
    <input type="file" class="form-control" id="promotionImageUrl" name="promotion_image" accept="image/*">
</div>


                    <input type="hidden" name="promotion_id" id="promotion_id">
                    <button type="submit" class="btn btn-primary mt-3">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Popup de sélection des clients -->
<div class="modal fade" id="selectClientsModal" tabindex="-1" aria-labelledby="selectClientsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="selectClientsModalLabel">Sélectionner les clients</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="sendPromotionForm" method="POST" action="views/promotion/envoye.php?id=<?= $promotion['id'] ?>">
                    <div class="form-group">
                        <label for="searchClient">Recherche Client :</label>
                        <input type="text" id="searchClient" class="form-control" placeholder="Rechercher par nom, email...">
                    </div>
                    <div class="form-group">
                        <label for="clients">Sélectionner les clients</label>
                        <div class="d-flex justify-content-between">
                            <input type="checkbox" id="selectAllClients"> Tout sélectionner
                        </div>
                        <div id="clientsList">
                            <?php
                            // Afficher tous les clients
                            foreach ($clients as $client) {
                                echo "<div><input type='checkbox' name='clients[]' value='{$client['id']}' data-name='{$client['nom']} {$client['prenom']} - {$client['email']}'> {$client['nom']} {$client['prenom']} - {$client['email']}</div>";
                            }
                            ?>
                        </div>
                    </div>
                    <input type="hidden" name="promotion_id" id="promotion_id">
                    <button type="submit" class="btn btn-primary mt-3">Envoyer la promotion</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Script pour ouvrir le popup et rechercher des clients -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('.btn-send-promotion').forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            var promotionId = this.getAttribute('data-promotion-id');
            document.getElementById('promotion_id').value = promotionId;

            // Afficher le modal
            var modal = new bootstrap.Modal(document.getElementById('selectClientsModal'));
            modal.show();
        });
    });

    // Fonction de recherche dans la liste des clients
    document.getElementById('searchClient').addEventListener('input', function() {
        var searchQuery = this.value.toLowerCase();
        var clients = document.querySelectorAll('#clientsList div');
        clients.forEach(function(client) {
            var clientName = client.textContent.toLowerCase();
            if (clientName.includes(searchQuery)) {
                client.style.display = 'block';
            } else {
                client.style.display = 'none';
            }
        });
    });

    // Sélectionner ou désélectionner tous les clients
    document.getElementById('selectAllClients').addEventListener('change', function() {
        var isChecked = this.checked;
        var clients = document.querySelectorAll('#clientsList input[type="checkbox"]');
        clients.forEach(function(client) {
            client.checked = isChecked;
        });
    });

    // Stocker la sélection des clients dans une session
    document.getElementById('sendPromotionForm').addEventListener('submit', function(e) {
        var selectedClients = [];
        var clients = document.querySelectorAll('#clientsList input[type="checkbox"]:checked');
        clients.forEach(function(client) {
            selectedClients.push(client.value);
        });

        // Stocker la sélection des clients dans une session PHP
        fetch('envoye.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ clients: selectedClients })
        });
    });


    

// Ouvrir le modal de modification avec les données de la promotion
document.querySelectorAll('.btn-edit').forEach(function(button) {
    button.addEventListener('click', function(e) {
        var promotionId = this.getAttribute('data-promotion-id');
        var promotionTitle = this.getAttribute('data-promotion-title');
        var promotionDescription = this.getAttribute('data-promotion-description');
        var promotionStartDate = this.getAttribute('data-promotion-start-date');
        var promotionEndDate = this.getAttribute('data-promotion-end-date');
        var promotionImageUrl = this.getAttribute('data-promotion-image-url');

        document.getElementById('promotion_id').value = promotionId;
        document.getElementById('promotionTitle').value = promotionTitle;
        document.getElementById('promotionDescription').value = promotionDescription;
        document.getElementById('promotionStartDate').value = promotionStartDate;
        document.getElementById('promotionEndDate').value = promotionEndDate;
        document.getElementById('promotionImageUrl').value = promotionImageUrl;
    });
});

</script>
</body>
</html>
