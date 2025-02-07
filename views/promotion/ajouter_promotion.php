<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer une Promotion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        fieldset {
            border: 2px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .btn-delete-line {
            font-size: 16px;
            cursor: pointer;
            color: red;
            border: none;
            background: none;
        }
        .btn-delete-line:hover {
            color: #c82333;
        }
        .bg-light-gray {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-primary text-white text-center">
            <h3>Créer une Promotion</h3>
        </div>
        <div class="card-body">

            <form method="POST" action="index.php?entity=promotion&action=create" enctype="multipart/form-data">
            <input type="hidden" id="titre" name="type" value="promotion">
                <!-- Champ Titre de la Promotion -->
                <div class="mb-3">
                    <label for="titre" class="form-label">Titre de la Promotion</label>
                    <input type="text" id="titre" name="titre" class="form-control" placeholder="Entrez le titre de la promotion" required>
                </div>

                <!-- Champ Description -->
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="4" placeholder="Entrez une description" ></textarea>
                </div>

                <!-- Champ Image -->
                <div class="mb-3">
                    <label for="image" class="form-label">Image de la Promotion</label>
                    <input type="file" id="image" name="image" class="form-control" >
                </div>

                

                <!-- Dates de Validité -->
                <div class="row">
                    <div class="col-md-6">
                        <label for="date_debut" class="form-label">Date de Début</label>
                        <input type="date" id="date_debut" name="date_debut" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="date_fin" class="form-label">Date de Fin</label>
                        <input type="date" id="date_fin" name="date_fin" class="form-control" required>
                    </div>
                </div>

                <!-- Bouton Soumettre -->
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary">Créer Promotion</button>
                </div>

            </form>
        </div>
    </div>
</div>

</body>
</html>
