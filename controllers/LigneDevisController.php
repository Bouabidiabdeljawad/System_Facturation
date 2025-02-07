<?php
require_once 'models/LigneDevis.php';

class LigneDevisController
{
    private $bd;

    public function __construct($bd)
    {
        $this->bd = $bd;
    }

    // Afficher toutes les lignes d'un devis
    public function index($devis_id)
    {
        $ligneDevisModel = new LigneDevis($this->bd);
        $lignes = $ligneDevisModel->obtenirLignesParDevis($devis_id);
        require 'views/ligne_devis/liste_lignes.php';
    }

    // Ajouter une nouvelle ligne de devis
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ligneDevisModel = new LigneDevis($this->bd);
            $devis_id = $_POST['devis_id'];
            $description = $_POST['description'];
            $quantite = $_POST['quantite'];
            $prix_unitaire = $_POST['prix_unitaire'];

            $ligneDevisModel->ajouterLigneDevis($devis_id, $description, $quantite, $prix_unitaire);

            $_SESSION['message'] = "Ligne de devis ajoutée avec succès !";
            $_SESSION['message_type'] = "success";
            header("Location: index.php?entity=ligne_devis&action=index&devis_id=$devis_id");
            exit;
        }

        include 'views/ligne_devis/ajouter_ligne.php';
    }

    // Modifier une ligne de devis
    public function edit($id)
    {
        $ligneDevisModel = new LigneDevis($this->bd);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $description = $_POST['description'];
            $quantite = $_POST['quantite'];
            $prix_unitaire = $_POST['prix_unitaire'];

            $ligneDevisModel->modifierLigneDevis($id, $description, $quantite, $prix_unitaire);

            $_SESSION['message'] = "Ligne de devis modifiée avec succès !";
            $_SESSION['message_type'] = "info";
            header('Location: index.php?entity=ligne_devis&action=index&devis_id=' . $_POST['devis_id']);
            exit;
        }

        $ligne = $ligneDevisModel->obtenirLigneParId($id);
        include 'views/ligne_devis/modifier_ligne.php';
    }

    // Supprimer une ligne de devis
    public function delete($id)
    {
        $ligneDevisModel = new LigneDevis($this->bd);
        $ligne = $ligneDevisModel->obtenirLigneParId($id);
        $devis_id = $ligne['devis_id'];

        $ligneDevisModel->supprimerLigneDevis($id);

        $_SESSION['message'] = "Ligne de devis supprimée avec succès !";
        $_SESSION['message_type'] = "warning";
        header("Location: index.php?entity=ligne_devis&action=index&devis_id=$devis_id");
        exit;
    }
}
?>
