<?php
class LigneDevis {
    private $pdo;
    private $id;
    private $devis_id;
    private $description;
    private $quantite;
    private $prix_unitaire;
    private $total;
    private $date_creation;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // 🟢 Ajouter une ligne de devis
    public function ajouterLigneDevis($devis_id, $description, $quantite, $prix_unitaire) {
        $this->total = $quantite * $prix_unitaire;
        $stmt = $this->pdo->prepare("
            INSERT INTO ligne_devis (devis_id, description, quantite, prix_unitaire, total) 
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$devis_id, $description, $quantite, $prix_unitaire, $this->total]);
    }

    // 🟢 Modifier une ligne de devis
    public function modifierLigneDevis($id, $description, $quantite, $prix_unitaire) {
        $this->total = $quantite * $prix_unitaire;
        $stmt = $this->pdo->prepare("
            UPDATE ligne_devis 
            SET description = ?, quantite = ?, prix_unitaire = ?, total = ? 
            WHERE id = ?
        ");
        return $stmt->execute([$description, $quantite, $prix_unitaire, $this->total, $id]);
    }

    // 🟢 Supprimer une ligne de devis
    public function supprimerLigneDevis($id) {
        $stmt = $this->pdo->prepare("DELETE FROM ligne_devis WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // 🟢 Supprimer toutes les lignes associées à un devis
    public function supprimerLignesParDevis($devis_id) {
        $stmt = $this->pdo->prepare("DELETE FROM ligne_devis WHERE devis_id = ?");
        return $stmt->execute([$devis_id]);
    }

    // 🟢 Obtenir une ligne de devis par ID
    public function obtenirLigneParId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM ligne_devis WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 🟢 Obtenir toutes les lignes d'un devis
    public function obtenirLignesParDevis($devis_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM ligne_devis WHERE devis_id = ?");
        $stmt->execute([$devis_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🟢 Getters et Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getDevisId() { return $this->devis_id; }
    public function setDevisId($devis_id) { $this->devis_id = $devis_id; }

    public function getDescription() { return $this->description; }
    public function setDescription($description) { $this->description = $description; }

    public function getQuantite() { return $this->quantite; }
    public function setQuantite($quantite) { $this->quantite = $quantite; }

    public function getPrixUnitaire() { return $this->prix_unitaire; }
    public function setPrixUnitaire($prix_unitaire) { $this->prix_unitaire = $prix_unitaire; }

    public function getTotal() { return $this->total; }
    public function setTotal($total) { $this->total = $total; }

    public function getDateCreation() { return $this->date_creation; }
    public function setDateCreation($date_creation) { $this->date_creation = $date_creation; }
}
?>
