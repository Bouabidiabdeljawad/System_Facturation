<?php

include 'Facture.php';
class Devis {
    private $pdo;
    private $id;
    private $client_id;
    private $date_creation;
    private $statut;
    private $total_ht;
    private $tva;
    private $total_ttc;
    private $num;
    private $intitule;
    private $date_validite;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Génère un numéro unique à 4 chiffres pour un devis
     */
    private function genererNumeroDevis() {
        return str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Ajoute un nouveau devis avec un numéro unique
     */
    public function ajouterDevis($client_id, $date_creation, $statut, $total_ht, $tva, $num,$intitule, $lignes) {
        try {
            
            $this->pdo->beginTransaction();
            $total_ttc = floatval($total_ht) + (floatval($total_ht) * floatval($tva) / 100);
            $stmt = $this->pdo->prepare("select MAX(id) from devis");
            $id = $stmt->execute();
            $id = $stmt->fetchColumn();
            $id = $id + 1;

            
            

            $stmt = $this->pdo->prepare("INSERT INTO devis (id,client_id, date_creation, statut, total_ht, tva, total_ttc, num,intitule) 
                                         VALUES (?,?, ?, ?, ?, ?, ?, ?,?)");
            
            // Debug: Vérifier les valeurs avant l'insertion
            var_dump([$id,$client_id, $date_creation, $statut, $total_ht, $tva, $total_ttc, $num,$intitule]);
    
            if (!$stmt->execute([$id,$client_id, $date_creation, $statut, $total_ht, $tva, $total_ttc, $num,$intitule])) {
                // Afficher l'erreur si l'insertion échoue
                throw new Exception("Erreur lors de l'ajout du devis: " . implode(", ", $stmt->errorInfo()));
            }
    
            // Récupérer l'ID du devis nouvellement inséré
          
    
            // Insérer les lignes de devis
            $stmtLigne = $this->pdo->prepare("INSERT INTO ligne_devis (devis_id, description, quantite, prix_unitaire, total) 
                                              VALUES (?, ?, ?, ?, ?)");
    
            foreach ($lignes as $ligne) {
                $description = $ligne['description'];
                $quantite = $ligne['quantite'];
                $prix_unitaire = $ligne['prix_unitaire'];
                $total = $quantite * $prix_unitaire;
                
                if (!$stmtLigne->execute([$id, $description, $quantite, $prix_unitaire, $total])) {
                    // Afficher l'erreur si l'insertion échoue pour les lignes
                    throw new Exception("Erreur lors de l'ajout des lignes du devis.");
                }
            }
    
            // Commit de la transaction si tout est ok
            $this->pdo->commit();
    
            return true;
        } catch (Exception $e) {
            // Rollback en cas d'erreur
            $this->pdo->rollBack();
            echo "Erreur : " . $e->getMessage();
            return false;
        }
    }
    
    /**
     * Récupère un devis par son ID
     */
// Exemple de méthode dans le modèle Devis pour récupérer les lignes associées au devis
public function obtenirLignesParDevis($devis_id) {
    $stmt = $this->pdo->prepare("SELECT * FROM ligne_devis WHERE devis_id = ?");
    $stmt->execute([$devis_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Inside your `Devis` model method

public function obtenirDevisParId($id) {
    $sql = "SELECT * FROM devis WHERE id = :id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$result) {
        // Debugging: If no result, show the error
        echo "No Devis found for ID $id.";
        return false;
    }
    
    return $result;
}

public function obtenirClientParDevis($clientId) {
    $sql = "SELECT * FROM client WHERE id = :clientId";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindParam(':clientId', $clientId, PDO::PARAM_INT);
    $stmt->execute();
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$client) {
        // Debugging: If no client found, show the error
        echo "No client found for client ID $clientId.";
        return false;
    }
    
    return $client;
}


    /**
     * Modifie un devis existant
     */
    public function modifierDevis($id, $client_id, $date_creation, $statut, $total_ht, $tva) {
        $this->total_ttc = $this->calculerTotalTTC($total_ht, $tva);

        $stmt = $this->pdo->prepare("UPDATE devis SET client_id = ?, date_creation = ?, statut = ?, total_ht = ?, tva = ?, total_ttc = ? WHERE id = ?");
        return $stmt->execute([$client_id, $date_creation, $statut, $total_ht, $tva, $this->total_ttc, $id]);
    }

    /**
     * Supprime un devis
     */
    public function supprimerDevis($id) {
      
        $stmt = $this->pdo->prepare("DELETE FROM devis WHERE id = ?");
        return $stmt->execute([$id]);
    }
    

    public function renumeroterDevis() {
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
            $stmt = $this->pdo->query("SELECT id FROM devis ORDER BY id");
            $devis = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $newId = 1;
            foreach ($devis as $devisRow) {
                $stmtUpdate = $this->pdo->prepare("UPDATE devis SET id = ? WHERE id = ?");
                $stmtUpdate->execute([$newId, $devisRow['id']]);
                $newId++;
            }
            $this->pdo->exec("ALTER TABLE devis AUTO_INCREMENT = $newId");

            
            
    }
    
    /**
     * Récupère tous les devis
     */
    public function modifierStatut($id, $statut) {
        $stmt = $this->pdo->prepare("UPDATE devis SET statut = :statut WHERE id = :id");
        $stmt->execute(['statut' => $statut, 'id' => $id]);
        
    }

    public function obtenirTousLesDevis() {
        $stmt = $this->pdo->query("SELECT * FROM devis");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    

    /**
     * Calcule le total TTC
     */
    private function calculerTotalTTC($total_ht, $tva) {
        return $total_ht + ($total_ht * $tva / 100);
    }

    // Getters et Setters
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getClientId() {
        return $this->client_id;
    }

    public function setClientId($client_id) {
        $this->client_id = $client_id;
    }

    public function getDateCreation() {
        return $this->date_creation;
    }

    public function setDateCreation($date_creation) {
        $this->date_creation = $date_creation;
    }

    public function getStatut() {
        return $this->statut;
    }

    public function setStatut($statut) {
        $this->statut = $statut;
    }

    public function getTotalHT() {
        return $this->total_ht;
    }

    public function setTotalHT($total_ht) {
        $this->total_ht = $total_ht;
    }

    public function getTva() {
        return $this->tva;
    }

    public function setTva($tva) {
        $this->tva = $tva;
    }

    public function getTotalTTC() {
        return $this->total_ttc;
    }

    public function setTotalTTC($total_ttc) {
        $this->total_ttc = $total_ttc;
    }

    public function getNum() {
        return $this->num;
    }

    public function setNum($num) {
        $this->num = $num;
    }
}

// Fermeture de la connexion PDO

?>
