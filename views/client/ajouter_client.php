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
<?php
// Récupérer les données du formulaire s'il y a eu une erreur
$formData = $_SESSION['formData'] ?? [];
unset($_SESSION['formData']); 
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Nouveau Client</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function toggleCompanyFields() {
            var typeClient = document.getElementById('type_client').value;
            document.getElementById('company-fields').style.display = typeClient === 'societe' ? 'block' : 'none';
            document.getElementById('person-fields').style.display = typeClient === 'particulier' ? 'block' : 'none';
            document.getElementById('prenom-group').style.display = typeClient === 'societe' ? 'none' : 'block';
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('date_inscription').value = new Date().toISOString().split('T')[0];
            toggleCompanyFields();
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
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white text-center">
                <h3><i class="fas fa-user-plus"></i> Ajouter un Nouveau Client</h3>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="index.php?entity=client&action=create">

                    <!-- Type de Client -->
                    <div class="mb-3">
                        <label for="type_client" class="form-label">Type de Client</label>
                        <select id="type_client" name="type_client" class="form-select" onchange="toggleCompanyFields()" required>
                            <option value="particulier" <?= (isset($formData['type_client']) && $formData['type_client'] === 'particulier') ? 'selected' : ''; ?>>Particulier</option>
                            <option value="societe" <?= (isset($formData['type_client']) && $formData['type_client'] === 'societe') ? 'selected' : ''; ?>>Société</option>
                        </select>
                    </div>

                    <!-- Nom et Prénom -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" id="nom" name="nom" class="form-control" value="<?= htmlspecialchars($formData['nom'] ?? '') ?>" required>
                        </div>
                        <div id="prenom-group" class="col-md-6 mb-3">
                            <label for="prenom" class="form-label">Prénom</label>
                            <input type="text" id="prenom" name="prenom" class="form-control" value="<?= htmlspecialchars($formData['prenom'] ?? '') ?>">
                        </div>
                    </div>

                    <!-- Adresse -->
                    <div class="mb-3">
                        <label for="adresse" class="form-label">Adresse</label>
                        <input type="text" id="adresse" name="adresse" class="form-control" value="<?= htmlspecialchars($formData['adresse'] ?? '') ?>" required>
                    </div>

                    <!-- Ville et Code Postal -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ville" class="form-label">Ville</label>
                            <input type="text" id="ville" name="ville" class="form-control" value="<?= htmlspecialchars($formData['ville'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="code_postal" class="form-label">Code Postal</label>
                            <input type="text" id="code_postal" name="code_postal" class="form-control" value="<?= htmlspecialchars($formData['code_postal'] ?? '') ?>" required>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($formData['email'] ?? '') ?>" required>
                    </div>

                    <!-- Téléphone -->
                    <div class="mb-3">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="text" id="telephone" name="telephone" class="form-control" value="<?= htmlspecialchars($formData['telephone'] ?? '') ?>" required>
                    </div>

                    <!-- Champs spécifiques pour les particuliers -->
                    <div id="person-fields">
                        <div class="mb-3">
                            <label for="date_naissance" class="form-label">Date de Naissance</label>
                            <input type="date" id="date_naissance" name="date_naissance" class="form-control" value="<?= htmlspecialchars($formData['date_creation'] ?? '') ?>" 
                            max="2009-01-01">
                        </div>
                    </div>

                    <!-- Champs spécifiques pour les sociétés -->
                    <div id="company-fields" style="display:none;">
                        <div class="mb-3">
                            <label for="date_creation" class="form-label">Date de Création</label>
                            <input type="date" id="date_creation" name="date_creation" class="form-control" value="<?= htmlspecialchars($formData['date_creation'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="code_fiscal" class="form-label">Code Fiscal</label>
                            <input type="text" id="code_fiscal" name="code_fiscal" class="form-control" value="<?= htmlspecialchars($formData['code_fiscal'] ?? '') ?>" max="<?= date('Y-m-d') ?>">
                        </div>
                    </div>

                    <!-- Date d'Inscription (Cachée) -->
                    <input type="hidden" id="date_inscription" name="date_inscription" value="<?= htmlspecialchars($formData['date_inscription'] ?? date('Y-m-d')) ?>">

                    <!-- Boutons -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="index.php?entity=client&action=index" class="btn btn-outline-dark btn-lg"><i class="fas fa-arrow-left"></i> Retour</a>
                        <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Ajouter</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</body>

</html>
