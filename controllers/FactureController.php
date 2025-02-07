<?php

require_once 'models/Facture.php';

class FactureController
{
    private $pdo;
    private $factureModel;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->factureModel = new Facture($pdo); 
    }
   
    public function index()
    {
        $factures = $this->factureModel->obtenirToutesLesFactures();
        include 'views/facture/liste_facture.php'; 
    }

    
    public function afficher($id)
    {
        $facture = $this->factureModel->obtenirFactureParId($id);
        include 'views/facture/affiche.php'; 
    }
    public function envoyer($id)
    {
        $facture = $this->factureModel->obtenirFactureParId($id);
        include 'views/facture/envoyer.php'; 
    }

    
     public function create()
     {
        if(isset($_GET['id'])){
         include 'views/facture/ajouter_facture.php'; }
         if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
             $devisId = $_POST['devis_id'];
             $clientId = $_POST['client_id'];
             $dateCreation = date('y-m-d');
             $intitule = $_POST['intitule'];
             $statut = $_POST['statut'] ?? 'en attente'; 
             $modePaiement = $_POST['mode_paiement'] ?? 'virement';
             $montantTotalHt = $_POST['total_ht'];
             $totalTva = $_POST['total_tva'];
             $montantTotalTtc = $_POST['montant_total_ttc'];
     
             $this->factureModel->setDevisId($devisId);
             $this->factureModel->setClientId($clientId);
             $this->factureModel->setDateCreation($dateCreation);
             $this->factureModel->setIntitule($intitule);
             $this->factureModel->setStatut($statut);
             $this->factureModel->setModePaiement($modePaiement);
             $this->factureModel->setMontantTotalHt($montantTotalHt);
             $this->factureModel->setTotalTva($totalTva);
             $this->factureModel->setMontantTotalTtc($montantTotalTtc);

                 $this->factureModel->creerFactureDepuisDevis(
                     $devisId,
                     $clientId,
                     $dateCreation,
                   
                     $intitule,
                     $statut,
                     $modePaiement,
                     $montantTotalHt,
                     $totalTva,
                     $montantTotalTtc
                 );
                
     
             header("Location: index.php?entity=facture&action=index");
             exit();
         }
     }
     public function modifierStatut($id) {
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['statut'])) {
            $nouveauStatut = $_POST['statut'];
            $this->factureModel->modifierStatut($id, $nouveauStatut);
            
            header('Location: index.php?entity=facture&action=index');
            exit;
        }
        
    }

    
    // Supprimer une facture
    public function delete($id)
    {
        try {
            $this->factureModel->supprimerFacture($id);
            $this->factureModel->renumeroterfacture();
            $_SESSION['message'] = "Facture supprimée avec succès.";
            $_SESSION['message_type'] = "success";
        } catch (Exception $e) {
            $_SESSION['message'] = "Erreur lors de la suppression de la facture : " . $e->getMessage();
            $_SESSION['message_type'] = "danger";
        }

        header("Location: index.php?entity=facture&action=index");
        exit();
    }
}

?>
