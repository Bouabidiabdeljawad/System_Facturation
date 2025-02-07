<?php
ob_start();
require_once 'models/Client.php';

class ClientController
{
    private $bd;

    
    public function __construct($bd)
    {
        $this->bd = $bd;
    }

    
    public function index()
    {   
        $client = new Client($this->bd);
        $clients = $client->obtenirTousLesClients();
       
        require 'views/client/liste_clients.php';
        
        
    }

    public function filterByType()
{
    if (isset($_GET['type_client']) && !empty($_GET['type_client']) ) {
        $type_client = 'type_client';
        
        $valeur_filtre = $_GET[$type_client]; 

        
        $clientModel = new Client($this->bd);

        
        $clients = $clientModel->obtenirClientsParType($type_client, $valeur_filtre);

        
        include 'views/client/liste_clients.php';

    } else {
        
        echo "Aucun filtre appliqué ou valeur manquante.";
    }
}
/*
public function filtrer($type_client, $date_start, $date_end)
{
    $client = new Client($this->bd);
    $clients = $client->obtenirClientsParFiltres($type_client, $date_start, $date_end);
    include('views/client/liste_clients.php');
}*/

public function filtrerClients()
{
    // Récupérer les paramètres de la requête
    $search_query = isset($_GET['search_query']) ? $_GET['search_query'] : '';  // Recherche par nom, adresse ou email
    $type_client = isset($_GET['type_client']) ? $_GET['type_client'] : 'tout';  // Filtre par type de client
    $date_start = isset($_GET['date_inscription_start']) ? $_GET['date_inscription_start'] : '';  // Date d'inscription (début)
    $date_end = isset($_GET['date_inscription_end']) ? $_GET['date_inscription_end'] : '';  // Date d'inscription (fin)

    // Création de l'objet Client
    $client = new Client($this->bd);

    // Appeler la méthode de filtrage et récupération des clients en fonction des critères
    $clients = $client->rechercherClients($search_query, $type_client, $date_start, $date_end);

    // Inclure la vue pour afficher les résultats
    include('views/client/liste_clients.php');
}



    

    

    


// Ajoutez ceci en haut du fichier si ce n'est pas déjà fait

public function create()
{
    $error = null;
    $formData = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // Stocker les données saisies pour réutilisation
            $formData = $_POST;

            $client = new Client($this->bd);
            $client->setTypeClient($_POST['type_client']);
            $client->setNom($_POST['nom']);
            $client->setAdresse($_POST['adresse']);
            $client->setVille($_POST['ville']);
            $client->setCodePostal($_POST['code_postal']);
            $client->setEmail($_POST['email']);
            $client->setTelephone($_POST['telephone']);
            $client->setDateInscription($_POST['date_inscription']);

            if ($_POST['type_client'] === 'particulier') {
                $client->setPrenom($_POST['prenom']);
                $client->setDateNaissance($_POST['date_naissance']);
            } elseif ($_POST['type_client'] === 'societe') {
                $client->setDateCreation($_POST['date_creation']);
                $client->setCodeFiscal($_POST['code_fiscal']);
            }

            // Vérification si l'email ou le téléphone existent déjà
            if ($client->exists('email', $_POST['email'])) {
                throw new Exception("L'email est déjà utilisé. Veuillez en choisir un autre.");
            }
            if ($client->exists('telephone', $_POST['telephone'])) {
                throw new Exception("Le numéro de téléphone est déjà utilisé. Veuillez en choisir un autre.");
            }

            // Tentative d'enregistrement
            $client->creerClient($_POST['type_client']);

            // ✅ Succès : stocker un message et rediriger
            $_SESSION['message'] = "Client ajouté avec succès !";
            $_SESSION['message_type'] = "success";
            header('Location: index.php?entity=client&action=index');
            exit;

        } catch (Exception $e) {
            // ❌ Stocker l'erreur et les données saisies
            $_SESSION['message'] = "Erreur : " . $e->getMessage();
            $_SESSION['message_type'] = "danger";
            $_SESSION['formData'] = $_POST;

            // Redirection vers le formulaire en cas d'erreur
            header('Location: index.php?entity=client&action=create');
            exit;
        }
    }

    // Si on arrive ici, afficher le formulaire avec les données pré-remplies
    $formData = $_SESSION['formData'] ?? [];
    include 'views/client/ajouter_client.php';
}


public function delete($id)
{
    if (isset($id)) {
        $client = new Client($this->bd); 
        $client->supprimerClient($id); 

        $_SESSION['message'] = "Client supprimé avec succès !";
        $_SESSION['message_type'] = "warning";

        header('Location: index.php?entity=client&action=index');
        exit;
    } else {
        $_SESSION['message'] = "Erreur lors de la suppression du client.";
        $_SESSION['message_type'] = "danger";

        header('Location: index.php?entity=client&action=index');
        exit;
    }
}

public function edit($id)
{
    $client = new Client($this->bd);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $client->setTypeClient($_POST['type_client']);
        $client->setNom($_POST['nom']);
        $client->setAdresse($_POST['adresse']);
        $client->setVille($_POST['ville']);
        $client->setCodePostal($_POST['code_postal']);
        $client->setEmail($_POST['email']);
        $client->setTelephone($_POST['telephone']);
        $client->setDateInscription($_POST['date_inscription']);

        if ($_POST['type_client'] === 'particulier') {
            $client->setPrenom($_POST['prenom']);
            $client->setDateNaissance($_POST['date_naissance']);
        } elseif ($_POST['type_client'] === 'societe') {
            $client->setDateCreation($_POST['date_creation']);
            $client->setCodeFiscal($_POST['code_fiscal']);
        }

        $client->modifierClient($id);

        $_SESSION['message'] = "Client mis à jour avec succès !";
        $_SESSION['message_type'] = "info";

        header('Location: index.php?entity=client&action=index');
        exit;
    }
}

public function indexById($id)
{
    $clientModel = new Client($this->bd);
    $client = $clientModel->obtenirClientParId($id);

    if ($client) {
        include 'views/client/afficher_client.php';
    } else {
        $_SESSION['message'] = "Client introuvable.";
        $_SESSION['message_type'] = "danger";
        header('Location: index.php?entity=client&action=index');
        exit;
    }
}

    
}
ob_end_flush(); 
?>
