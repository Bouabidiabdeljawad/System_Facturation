<?php

class Promotion {
    private $pdo;

    // Attributs de la table `promotion`
    public $id;
    public $type;
    public $titre;
    public $description;
    public $image_url;
    public $date_debut;
    public $date_fin;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    //getters and setters
    public function getId() {
        return $this->id;
    }
    public function setId($id) {
        $this->id = $id;
    }
    public function getType() {
        return $this->type;
    }
    public function setType($type) {
        $this->type = $type;
    }
    public function getTitre() {
        return $this->titre;
    }
    public function setTitre($titre) {
        $this->titre = $titre;
    }
    public function getDescription() {
        return $this->description;
    }
    public function setDescription($description) {

            $this->description = $description;

    }
    public function getImageUrl() {
        return $this->image_url;
    }
    public function setImageUrl($image_url) {
        $this->image_url = $image_url;
    }
    public function getDateDebut() {
        return $this->date_debut;
    }
    public function setDateDebut($date_debut) {
        $this->date_debut = $date_debut;
    }
    public function getDateFin() {
        return $this->date_fin;
    }
    public function setDateFin($date_fin) {
        $this->date_fin = $date_fin;
    }
   
    
    // Créer une promotion
    public function creerPromotion() {
        $query = "INSERT INTO evenement (titre, description, image_url, date_debut, date_fin, type_evenement) 
                  VALUES (:titre, :description, :image_url, :date_debut, :date_fin, :type_evenement)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            ':titre' => $this->titre,
            ':description' => $this->description,
            ':image_url' => $this->image_url,
            ':date_debut' => $this->date_debut,
            ':date_fin' => $this->date_fin,
            ':type_evenement' => $this->type
        ]);
        $this->id = $this->pdo->lastInsertId(); 
        return $this->id;
    }

    // Récupérer une promotion par ID
    public function obtenirPromotionParId($id) {
        $query = "SELECT * FROM evenement WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $this->id = $result['id'];
            $this->titre = $result['titre'];
            $this->description = $result['description'];
            $this->image_url = $result['image_url'];
            $this->date_debut = $result['date_debut'];
            $this->date_fin = $result['date_fin'];
            
        }

        return $result ? $this : null; // Retourne l'objet si trouvé, sinon null
    }

    // Récupérer toutes les promotions
    public function obtenirToutesPromotions() {
        $query = "SELECT * FROM evenement";
        $stmt = $this->pdo->query($query);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        
        return $results; // Retourne un tableau d'objets Promotion
    }

    // Mettre à jour une promotion
    public function mettreAJourPromotion() {
        $query = "UPDATE evenement 
                  SET titre = :titre, description = :description, image_url = :image_url, date_debut = :date_debut, date_fin = :date_fin 
                  WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            ':id' => $this->id,
            ':titre' => $this->titre,
            ':description' => $this->description,
            ':image_url' => $this->image_url,
            ':date_debut' => $this->date_debut,
            ':date_fin' => $this->date_fin
        ]);
    }

    // Supprimer une promotion
    public function supprimerPromotion() {
        $query = "DELETE FROM evenement WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([':id' => $this->id]);
    }
}

?>
