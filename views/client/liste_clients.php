<?php
include('./includes/connection.php');
include('./includes/auth.php');




$entity = isset($_GET['entity']) ? $_GET['entity'] : '';
$action = isset($_GET['action']) ? $_GET['action'] : '';
$type_client_filter = isset($_GET['type_client']) ? $_GET['type_client'] : '';
$date_inscription_filter_start = isset($_GET['date_inscription_start']) ? $_GET['date_inscription_start'] : '';
$date_inscription_filter_end = isset($_GET['date_inscription_end']) ? $_GET['date_inscription_end'] : '';



?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Clients</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />
    <style>
        
        .notification {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #28a745;
            color: white;
            padding: 15px 30px;
            border-radius: 5px;
            display: none;
            font-size: 16px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .filter-form .form-row {
    margin-bottom: 15px;
}

.filter-form .form-label {
    font-weight: bold;
}

.filter-form .btn {
    margin-top: 30px;
    margin-left: 10px;
}


        /* Style boutons d'action */
        .action-btns button {
            margin-right: 10px;
        }

        .btn-floating {
            position: fixed;
            bottom: 20px;
            right: 20px;
            font-size: 24px;
        }
    </style>
    
</head>
<body>
<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <script>
        setTimeout(function () {
            let alert = document.querySelector('.alert');
            if (alert) {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 500);
            }
        }, 3000);
    </script>
    <?php 
        unset($_SESSION['message']);
        unset($_SESSION['message_type']); 
    ?>
<?php endif; ?>

    <!-- Notification de suppression -->
    <div id="notification" class="notification">
        Client supprimé avec succès !
    </div>

    <!-- Formulaire de filtrage -->
    <div class="container mt-5">
        <h2 class="text-center">Liste des Clients</h2>

        <form method="GET" class="filter-form d-flex justify-content-between w-100 mb-4">
    <input type="hidden" name="entity" value="<?php echo htmlspecialchars($entity); ?>">
    <input type="hidden" name="action" value="filtrer">

    <div class="form-row w-75">
        <!-- Champ de recherche par nom, adresse, email -->
        <div class="col-md-3">
            <label for="search_query" class="form-label">Recherche</label>
            <input type="text" class="form-control" name="search_query" id="search_query" 
                   placeholder="Nom, Email, Adresse" 
                   value="<?php echo isset($search_query) ? htmlspecialchars($search_query) : ''; ?>">
        </div>

        <!-- Filtre par type de client -->
        <div class="col-md-3">
            <label for="type_client" class="form-label">Type de Client</label>
            <select class="form-control" name="type_client" id="type_client">
                <option value="tout">Tous</option>
                <option value="particulier" <?php echo $type_client_filter == 'particulier' ? 'selected' : ''; ?>>Particulier</option>
                <option value="societe" <?php echo $type_client_filter == 'societe' ? 'selected' : ''; ?>>Société</option>
            </select>
        </div>

        <!-- Filtre par date d'inscription -->
        <div class="col-md-3">
            <label for="date_inscription_start" class="form-label">Date d'Inscription (Début)</label>
            <input type="date" class="form-control" name="date_inscription_start" id="date_inscription_start" 
                   value="<?php echo htmlspecialchars($date_inscription_filter_start); ?>">
        </div>

        <div class="col-md-3">
            <label for="date_inscription_end" class="form-label">Date d'Inscription (Fin)</label>
            <input type="date" class="form-control" name="date_inscription_end" id="date_inscription_end" 
                   value="<?php echo htmlspecialchars($date_inscription_filter_end); ?>">
        </div>
    </div>

    <!-- Bouton avec icône de filtre -->
    <div class="d-flex align-items-center">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-filter"></i> Filtrer
        </button>
    </div>
</form>


        <!-- Table des Clients -->
        <table class="table table-striped mt-4">
            <thead>
                <tr>
                  
                    <th scope="col">Type de Client</th>
                    <th scope="col">Nom</th>
                    <th scope="col">Adresse</th>
                    <th scope="col">Ville</th> 
                    <th scope="col">Code Postal</th>
                    <th scope="col">Email</th>
                    <th scope="col">Téléphone</th>
                  
                    <?php if ($type_client_filter == 'societe'): ?>
                        <th scope="col">Code Fiscal</th>
                        <th scope="col">Date de Création</th>
                    <?php endif; ?>
                    <?php if ($type_client_filter == 'particulier'): ?>
                        <th scope="col">Date Naissance</th>
                    <?php endif; ?>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clients as $client): ?>
                    <tr>
                        
                        <td><?php echo ucfirst($client['type_client']); ?></td>
                        <td><?php echo htmlspecialchars($client['nom']); ?></td>
                        <td><?php echo htmlspecialchars($client['adresse']); ?></td>
                        <td><?php echo htmlspecialchars($client['ville']); ?></td> 
                        <td><?php echo htmlspecialchars($client['code_postal']); ?></td>
                        <td><?php echo htmlspecialchars($client['email']); ?></td>
                        <td><?php echo htmlspecialchars($client['telephone']); ?></td>
                       
                        <?php if ($type_client_filter == 'societe'): ?>
                            <td><?php echo htmlspecialchars($client['code_fiscal']); ?></td>
                            <td><?php echo htmlspecialchars($client['date_creation']); ?></td>
                        <?php endif; ?>
                        <?php if ($type_client_filter == 'particulier'): ?>
                            <td><?php echo htmlspecialchars($client['date_naissance']); ?></td>
                        <?php endif; ?>
                        <td class="action-btns">
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modifierModal<?php echo $client['id']; ?>">
                                <i class="fas fa-edit"></i> 
                            </button>
                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal<?php echo $client['id']; ?>">
                                <i class="fas fa-trash-alt"></i> 
                            </button>
                            <!-- Modal de confirmation de suppression -->
                            <div class="modal fade" id="confirmDeleteModal<?php echo $client['id']; ?>" tabindex="-1" aria-labelledby="confirmDeleteModalLabel<?php echo $client['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="confirmDeleteModalLabel<?php echo $client['id']; ?>">Confirmer la Suppression</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Êtes-vous sûr de vouloir supprimer ce client ?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                            <a href="index.php?entity=client&action=delete&id=<?php echo $client['id']; ?>" class="btn btn-danger">
                                                Confirmer
                                            </a>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                           <!-- Modal de modification du client -->
                    <div class="modal fade" id="modifierModal<?php echo $client['id']; ?>" tabindex="-1" aria-labelledby="modifierModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title" id="modifierModalLabel">
                                        <i class="fas fa-user-edit me-2"></i>Modifier le Client
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="index.php?entity=client&action=edit&id=<?php echo $client['id']; ?>" method="POST" id="editClientForm<?php echo $client['id']; ?>">
                                        <input type="hidden" name="id" value="<?php echo $client['id']; ?>">
                                        
                                        <!-- Type de client -->
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Type de Client</label>
                                                <select class="form-select" name="type_client" id="type_client_<?php echo $client['id']; ?>" required>
                                                    <option value="particulier" <?php echo $client['type_client'] == 'particulier' ? 'selected' : ''; ?>>Particulier</option>
                                                    <option value="societe" <?php echo $client['type_client'] == 'societe' ? 'selected' : ''; ?>>Société</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Informations générales -->
                                        <div class="card mb-3">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">Informations Générales</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Nom</label>
                                                        <input type="text" class="form-control" name="nom" value="<?php echo htmlspecialchars($client['nom']); ?>" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Email</label>
                                                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($client['email']); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Téléphone</label>
                                                        <input type="tel" class="form-control" name="telephone" value="<?php echo htmlspecialchars($client['telephone']); ?>" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Date d'inscription</label>
                                                        <input type="date" class="form-control" name="date_inscription" value="<?php echo htmlspecialchars($client['date_inscription']); ?>" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Adresse -->
                                        <div class="card mb-3">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">Adresse</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-12 mb-3">
                                                        <label class="form-label">Adresse complète</label>
                                                        <input type="text" class="form-control" name="adresse" value="<?php echo htmlspecialchars($client['adresse']); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Ville</label>
                                                        <input type="text" class="form-control" name="ville" value="<?php echo htmlspecialchars($client['ville']); ?>" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Code Postal</label>
                                                        <input type="text" class="form-control" name="code_postal" value="<?php echo htmlspecialchars($client['code_postal']); ?>" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Champs spécifiques Société -->
                                        <div class="card mb-3 societe-fields" id="societeFields_<?php echo $client['id']; ?>" style="display: <?php echo $client['type_client'] == 'societe' ? 'block' : 'none'; ?>">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">Informations Société</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Code Fiscal</label>
                                                        <input type="text" class="form-control" name="code_fiscal" value="<?php echo isset($client['code_fiscal']) ? htmlspecialchars($client['code_fiscal']) : ''; ?>">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Date de Création</label>
                                                        <input type="date" class="form-control" name="date_creation" value="<?php echo isset($client['date_creation']) ? htmlspecialchars($client['date_creation']) : ''; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Champs spécifiques Particulier -->
                                        <div class="card mb-3 particulier-fields" id="particulierFields_<?php echo $client['id']; ?>" style="display: <?php echo $client['type_client'] == 'particulier' ? 'block' : 'none'; ?>">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">Informations Particulier</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Prénom</label>
                                                        <input type="text" class="form-control" name="prenom" value="<?php echo isset($client['prenom']) ? htmlspecialchars($client['prenom']) : ''; ?>">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Date de Naissance</label>
                                                        <input type="date" class="form-control" name="date_naissance" value="<?php echo isset($client['date_naissance']) ? htmlspecialchars($client['date_naissance']) : ''; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-end mt-4">
                                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Annuler</button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>Enregistrer les modifications
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>


                        </td>
                    </tr>
                    
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Bouton flottant pour ajouter un nouveau client -->
        <a href="index.php?entity=client&action=create" class="btn btn-primary btn-floating"><button class="btn btn-primary btn-floating" data-bs-toggle="modal" data-bs-target="#ajouterModal">
            <i class="fas fa-plus"></i>
        </button></a>

        

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Affichage de la notification
        function showNotification() {
            const notification = document.getElementById('notification');
            notification.style.display = 'block';
            setTimeout(() => {
                notification.style.display = 'none';
            }, 3000);
        }

        // Gestion du formulaire de type de client
        const typeClientSelect = document.getElementById('type_client');
        const societeFields = document.getElementById('societeFields');
        const particulierFields = document.getElementById('particulierFields');

        typeClientSelect.addEventListener('change', () => {
            if (typeClientSelect.value === 'societe') {
                societeFields.style.display = 'block';
                particulierFields.style.display = 'none';
            } else if (typeClientSelect.value === 'particulier') {
                societeFields.style.display = 'none';
                particulierFields.style.display = 'block';
            } else {
                societeFields.style.display = 'none';
                particulierFields.style.display = 'none';   
            }
        });
    
document.addEventListener('DOMContentLoaded', function() {
    // Gestion du changement de type de client pour chaque modal
    const typeClientSelects = document.querySelectorAll('[id^="type_client_"]');
    
    typeClientSelects.forEach(select => {
        const clientId = select.id.split('_').pop();
        const societeFields = document.getElementById(`societeFields_${clientId}`);
        const particulierFields = document.getElementById(`particulierFields_${clientId}`);
        
        select.addEventListener('change', function() {
            if (this.value === 'societe') {
                societeFields.style.display = 'block';
                particulierFields.style.display = 'none';
            } else {
                societeFields.style.display = 'none';
                particulierFields.style.display = 'block';
            }
        });
    });
});




setTimeout(function () {
        let alert = document.querySelector('.alert');
        if (alert) {
            alert.classList.remove('show'); // Cache progressivement l'alerte
            alert.classList.add('fade'); // Ajoute un effet de disparition
            setTimeout(() => alert.remove(), 500); // Supprime l'élément du DOM après l'effet
        }
    }, 3000);

</script>
</body>
</html>
