<?php

class Facture
{
    private $id;
    private $devisId;
    private $clientId;
    private $dateCreation;
    private $numFacture;
    private $intitule;
    private $statut;
    private $modePaiement;
    private $montantTotalHt;
    private $totalTva;
    private $montantTotalTtc;

    private $pdo; // Instance de la base de données

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
    
    // Getters et Setters pour dateLimite

 
    
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getDevisId()
    {
        return $this->devisId;
    }

    public function setDevisId($devisId)
    {
        $this->devisId = $devisId;
    }

    public function getClientId()
    {
        return $this->clientId;
    }

    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;
    }

    public function getNumFacture()
    {
        return $this->numFacture;
    }

    public function setNumFacture($numFacture)
    {
        $this->numFacture = $numFacture;
    }

    public function getIntitule()
    {
        return $this->intitule;
    }

    public function setIntitule($intitule)
    {
        $this->intitule = $intitule;
    }

    public function getStatut()
    {
        return $this->statut;
    }

    public function setStatut($statut)
    {
        $this->statut = $statut;
    }

    public function getModePaiement()
    {
        return $this->modePaiement;
    }

    public function setModePaiement($modePaiement)
    {
        $this->modePaiement = $modePaiement;
    }

    public function getMontantTotalHt()
    {
        return $this->montantTotalHt;
    }

    public function setMontantTotalHt($montantTotalHt)
    {
        $this->montantTotalHt = $montantTotalHt;
    }

    public function getTotalTva()
    {
        return $this->totalTva;
    }

    public function setTotalTva($totalTva)
    {
        $this->totalTva = $totalTva;
    }

    public function getMontantTotalTtc()
    {
        return $this->montantTotalTtc;
    }

    public function setMontantTotalTtc($montantTotalTtc)
    {
        $this->montantTotalTtc = $montantTotalTtc;
    }
    private function genererNumeroFacture() {
        return str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }

    // Méthodes pour la gestion des factures
  /*  public function creerFactureDepuisDevis($devisId, $clientId, $dateCreation, $dateLimit, $intitule, $statut, $modePaiement, $montantTotalHt, $totalTva, $montantTotalTtc)
    {
        // Vérification que les informations essentielles sont présentes
        if (empty($devisId) || empty($clientId) || empty($dateLimit) || empty($intitule)) {
            throw new Exception("Tous les champs obligatoires doivent être remplis.");
        }
        $stmt = $this->pdo->prepare("select MAX(id) from facture");
            $id = $stmt->execute();
            $id = $stmt->fetchColumn();
            $id = $id + 1;
        // Générez un numéro de facture unique
        $numFacture = $this->genererNumeroFacture();

        // Préparez la requête SQL pour insérer une nouvelle facture
        $query = "
            INSERT INTO facture (
            id,
                devis_id,
                client_id,
                date_creation,
              
                num_facture,
                intitule,
                statut,
                mode_paiement,
                montant_total_ht,
                total_tva,
                montant_total_ttc
            ) VALUES (:id,
                :devisId, :clientId, :dateCreation, :numFacture, :intitule,
                :statut, :modePaiement, :montantTotalHt, :totalTva, :montantTotalTtc
            )
        ";

        // Exécution de la requête préparée avec les données
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                ':id' => $id,
                ':devisId' => $devisId,
                ':clientId' => $clientId,
                ':dateCreation' => $dateCreation,
                ':numFacture' => $numFacture,
                ':intitule' => $intitule,
                ':statut' => $statut,
                ':modePaiement' => $modePaiement,
                ':montantTotalHt' => $montantTotalHt,
                ':totalTva' => $totalTva,
                ':montantTotalTtc' => $montantTotalTtc
            ]);

           
        } catch (PDOException $e) {
            // Gérer les erreurs SQL et les afficher
            throw new Exception("Erreur lors de l'insertion de la facture : " . $e->getMessage());
        }
    }*/

    public function creerFactureDepuisDevis($devisId, $clientId, $dateCreation, $intitule, $statut, $modePaiement, $montantTotalHt, $totalTva, $montantTotalTtc)
    {
        // Vérification que les informations essentielles sont présentes
        if (empty($devisId) || empty($clientId) || empty($intitule)) {
            throw new Exception("Tous les champs obligatoires doivent être remplis.");
        }

      
        // Générer un nouvel ID pour la facture
        $stmt = $this->pdo->prepare("SELECT MAX(id) FROM facture");
        $stmt->execute();
        $id = $stmt->fetchColumn() + 1;

        // Générer un numéro de facture unique
        $numFacture = $this->genererNumeroFacture();

        // Préparer la requête SQL pour insérer une nouvelle facture
        $query = "
            INSERT INTO facture (
                id, devis_id, client_id, date_creation, num_facture, intitule, statut, mode_paiement, montant_total_ht, total_tva, montant_total_ttc
            ) VALUES (
                :id, :devisId, :clientId, :dateCreation, :numFacture, :intitule, :statut, :modePaiement, :montantTotalHt, :totalTva, :montantTotalTtc
            )
        ";

        // Exécution de la requête préparée avec les données
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                ':id' => $id,
                ':devisId' => $devisId,
                ':clientId' => $clientId,
                ':dateCreation' => $dateCreation,
                ':numFacture' => $numFacture,
                ':intitule' => $intitule,
                ':statut' => $statut,
                ':modePaiement' => $modePaiement,
                ':montantTotalHt' => $montantTotalHt,
                ':totalTva' => $totalTva,
                ':montantTotalTtc' => $montantTotalTtc
            ]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de l'insertion de la facture : " . $e->getMessage());
        }
    
}


public function modifierStatut($id, $statut) {
    $stmt = $this->pdo->prepare("UPDATE facture SET statut = :statut WHERE id = :id");
    $stmt->execute(['statut' => $statut, 'id' => $id]);
    
}


    public function obtenirMontantTotalFactures($devisId)
    {
        $stmt = $this->pdo->prepare("SELECT SUM(montant_total_ttc) FROM facture WHERE devis_id = :devisId");
        $stmt->execute([':devisId' => $devisId]);
        return $stmt->fetchColumn();
    }

    public function obtenirMontantTotalDevis($devisId)
    {
        $stmt = $this->pdo->prepare("SELECT total_ht FROM devis WHERE id = :devisId");
        $stmt->execute([':devisId' => $devisId]);
        return $stmt->fetchColumn();
    }
    public function renumeroterfacture() {
     
            $stmt = $this->pdo->query("SELECT id FROM facture ORDER BY id");
            $devis = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $newId = 1;
            foreach ($devis as $devisRow) {
                $stmtUpdate = $this->pdo->prepare("UPDATE facture SET id = ? WHERE id = ?");
                $stmtUpdate->execute([$newId, $devisRow['id']]);
                $newId++;
            }
           

            
            
    }

    public function obtenirFactureParId($id)
    {
        $requete = $this->pdo->prepare("SELECT * FROM facture WHERE id = :id");
        $requete->bindParam(':id', $id, PDO::PARAM_INT);
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }
    public function obtenirClientParFacture($id)
    {
        $requete = $this->pdo->prepare("SELECT * FROM client WHERE id = :id");
        $requete->bindParam(':id', $id, PDO::PARAM_INT);
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenirToutesLesFactures()
    {
        $requete = $this->pdo->query("SELECT * FROM facture");
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }

   

    public function supprimerFacture($id)
    {
        $requete = $this->pdo->prepare("DELETE FROM facture WHERE id = :id");
        $requete->execute(['id' => $id]);
    }
}

?>
