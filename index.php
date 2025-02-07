<?php
ob_start();
session_start();
require_once 'includes/auth.php';
require_once 'controllers/DevisController.php';
require_once 'controllers/ClientController.php';
require_once 'controllers/LigneDevisController.php';
require_once 'controllers/FactureController.php';
require_once 'controllers/PromotionController.php';


include('navbar.php');
include('./includes/connection.php');

if (!isset($_SESSION['anniversaire'])) {
    include('views/anniversaire/message.php');
    $_SESSION['anniversaire'] = true;
    exit();
}



$entity = $_GET['entity'] ?? null; 
$action = $_GET['action'] ?? null; 
$id = $_GET['id'] ?? null;


if (!$pdo) {  
    echo "Database connection failed.";
    exit();
}

switch ($entity) {
    case 'devis':
        $controller = new DevisController($pdo);
        break;
    case 'client':
      
        $controller = new ClientController($pdo);
        break;
    case 'ligneDevis':
    
        $controller = new LigneDevisController($pdo);
        break;
    case 'facture':
        $controller = new FactureController($pdo);
        break;
    case 'promotion':
        $controller= new PromotionController($pdo);
        break;
    default:

    include('acceuil.php');
        exit();
}

if (!$action) {
    echo "Action not specified.";
    exit();
}       

switch ($action) {
    case 'create':
        $controller->create();
        break;
    case 'edit':
        $controller->edit($id);
        break;
    case 'afficher':
        $controller->afficher($id);
        break;
    case 'send':
        $controller->envoyer($id);
        break;
    case 'delete':
        $controller->delete($id);
        break;
    case 'modifierStatut':
        $controller->modifierStatut($id);
        break;
    case 'afficherLigneDevis':
        $controller->afficherId($id);
        break;
    case 'index':
        $controller->index();
        break;
    case 'imprimer':
        $controller->imprimer($id);
        break;
    case 'modifierStatut':
        $controller->modifierStatut($id);
        break;
   
    case 'filtrer':
        $controller->filtrerClients();
        break;
    case 'detail':
        $controller->afficherPromotion($id);
        break;
    
    default:
      break;
}
ob_end_flush();
?>
