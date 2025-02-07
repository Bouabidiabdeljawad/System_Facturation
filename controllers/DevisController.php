<?php
require_once 'models/Devis.php';
require_once 'models/Facture.php';

class DevisController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->devisModel = new Devis($pdo); // Initialisation ici

    }
    public function envoyer($id)
    {
        $devis = $this->devisModel->obtenirDevisParId($id);
        include 'views/devis/envoyer.php'; 
    }
    public function index() {
        $devisModel = new Devis($this->pdo);
        //$devisModel->renumeroterDevis(); 
        $devis = $devisModel->obtenirTousLesDevis();
        include 'views/devis/liste_devis.php';

       
    }

    public function afficherId($id) {
        $devisModel = new Devis($this->pdo);
        $devis = $devisModel->obtenirDevisParId($id);
        
        include 'views/devis/modifier_devis.php';

       
    }
    public function imprimer($id) {
        $devisModel = new Devis($this->pdo);
        $devis = $devisModel->obtenirDevisParId($id);
        
        header('location: views/devis/affiche.php?id=' . $id);

       
    }

    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $client_id = $_POST['client_id'];
            $date_creation = date('Y-m-d');
            $intitule= $_POST['intitule'];
            $statut = 'en attente';
            $total_ht = $_POST['total_ht'];
            $tva = $_POST['tva'];
          


           
            $num = rand(1000, 9999);

           
            $lignes = [];
            if (!empty($_POST['descriptions'])) {
                for ($i = 0; $i < count($_POST['descriptions']); $i++) {
                    $lignes[] = [
                        'description' => $_POST['descriptions'][$i],
                        'quantite' => $_POST['quantites'][$i],
                        'prix_unitaire' => $_POST['prix_unitaires'][$i]
                    ];
                }
            }

            $devisModel = new Devis($this->pdo);
            if ($devisModel->ajouterDevis($client_id, $date_creation, $statut, $total_ht, $tva, $num,$intitule, $lignes)) {
                $_SESSION['message'] = "Devis ajouté avec succès.";
                $_SESSION['message_type'] = "success";
                
            } else {
                $_SESSION['message'] = "Erreur lors de l'ajout du devis.";
                $_SESSION['message_type'] = "danger";
            }

            header("Location: index.php?entity=devis&action=index");
        } else {
            require 'views/devis/ajouter_devis.php';
        }
    }
    public function modifierStatut($id) {
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['statut'])) {
            $nouveauStatut = $_POST['statut'];
            $this->devisModel->modifierStatut($id, $nouveauStatut);
            
            header('Location: index.php?entity=devis&action=index');
            exit;
        }
        
    }

    public function edit($id) {
        $devisModel = new Devis($this->pdo);
        $ligneDevisModel = new LigneDevis($this->pdo); 
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $client_id = $_POST['client_id'];
            $intitule= $_POST['intitule'];
            $date_creation = date('Y-m-d');
            $statut = $_POST['statut'];
            $total_ht = $_POST['total_ht'];
            $tva = $_POST['tva'];
    
            if ($devisModel->modifierDevis($id, $client_id, $date_creation, $statut, $total_ht, $tva,$intitule)) {
                if (!empty($_POST['descriptions'])) {
                    for ($i = 0; $i < count($_POST['descriptions']); $i++) {
                        $ligneDevisModel->modifierLigneDevis($_POST['ligne_ids'][$i], $_POST['descriptions'][$i], $_POST['quantites'][$i], $_POST['prix_unitaires'][$i]);
                        if($_POST['ligne_ids'][$i] == null){
                            $ligneDevisModel->ajouterLigneDevis($id, $_POST['descriptions'][$i], $_POST['quantites'][$i], $_POST['prix_unitaires'][$i]);
                        }
                    }
                }
    
                $_SESSION['message'] = "Devis et lignes modifiés avec succès.";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Erreur lors de la modification du devis.";
                $_SESSION['message_type'] = "danger";
            }
    
            header("Location: index.php?entity=devis&action=index");
            exit();
        } else {
           
            $devis = $devisModel->obtenirDevisParId($id);
            $ligneDevis = $devisModel->obtenirLignesParDevis($id);
            require 'views/devis/modifier_devis.php'; 
        }
    }
    
    public function delete($id) {
        $devisModel = new Devis($this->pdo);
        if ($devisModel->supprimerDevis($id)) {
            $devisModel->renumeroterDevis();  // Appeler la méthode pour renuméroter les IDs
            $_SESSION['message'] = "Devis supprimé et IDs renumérotés avec succès.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Erreur lors de la suppression du devis.";
            $_SESSION['message_type'] = "danger";
        }
        header("Location: index.php?entity=devis&action=index");
        exit();
    }
    
}


?>
