<?php

require_once __DIR__ . '/../models/Promotion.php';

class PromotionController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Action : Afficher toutes les promotions
    public function index() {
        $promotionModel = new Promotion($this->pdo);
        $promotions = $promotionModel->obtenirToutesPromotions();

        include 'views/promotion/liste_promotion.php'; // Vue pour afficher la liste des promotions
    }

    // Action : Afficher une promotion par ID
    public function afficherPromotion($id) {
        $promotionModel = new Promotion($this->pdo);
        $promotion = $promotionModel->obtenirPromotionParId($id);

        if ($promotion) {
            include 'views/promotion/detail.php'; // Vue pour afficher une promotion
        } else {
            echo "Promotion introuvable.";
        }
    }

    // Action : Créer une nouvelle promotion
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $promotion = new Promotion($this->pdo);
            $promotion->type=$_POST['type'];
            $promotion->titre = $_POST['titre'];
            $promotion->description = !empty($_POST['description']) ? $_POST['description'] : null; 
            $promotion->image_url = !empty($_FILES['image']['name']) 
                ? $this->uploadImage($_FILES['image']) 
                : null; 
            $promotion->date_debut = $_POST['date_debut'];
            $promotion->date_fin = $_POST['date_fin'];
    
            $id = $promotion->creerPromotion();
            header("Location: index.php?entity=promotion&action=index");
        } else {
            include 'views/promotion/ajouter_promotion.php'; 
        }
    }
    private function uploadImage($file) {
        $uploadDir = __DIR__ . '/../uploads/';
        $fileName = uniqid() . '-' . basename($file['name']);
        $targetPath = $uploadDir . $fileName;
    
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return 'uploads/' . $fileName; 
        } else {
            return null;
        }
    }

    // Action : Mettre à jour une promotion
    public function edit($id) {
        $promotionModel = new Promotion($this->pdo);
        $promotion = $promotionModel->obtenirPromotionParId($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $promotion->type= $_POST['type'];
            $promotion->titre = $_POST['titre'];
            $promotion->description = $_POST['description'];
            $promotion->image_url = $_POST['image_url'];
            $promotion->date_debut = $_POST['date_debut'];
            $promotion->date_fin = $_POST['date_fin'];

            $promotion->mettreAJourPromotion();
            header("Location: /promotions/detail.php?id=$id");
        } else {
            include __DIR__ . '/../views/promotions/form.php'; // Vue pour le formulaire d'édition
        }
    }

    // Action : Supprimer une promotion
    public function delete($id) {
        $promotionModel = new Promotion($this->pdo);
        $promotion = $promotionModel->obtenirPromotionParId($id);

        if ($promotion) {
            $promotion->supprimerPromotion();
            header("Location: index.php?entity=promotion&action=index");
        } else {
            echo "Promotion introuvable.";
            header("Location: index.php?entity=promotion&action=index?id=404");
        }


    }
}

?>
