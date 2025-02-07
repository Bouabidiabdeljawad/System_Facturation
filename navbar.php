<?php


function setActive($currentEntity, $currentAction, $linkEntity, $linkAction = 'index') {
    if ($currentEntity === $linkEntity && ($currentAction === $linkAction || ($linkAction === 'index' && empty($currentAction)))) {
        return 'class="active"';
    }
    return '';
}


$entity = $_GET['entity'] ?? 'null';
$action = $_GET['action'] ?? 'null';
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SB Admin 2 - Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
 <style>
    img{
        width: 225px;
        height: 70px;
        

    }
    #accordionSidebar{
        margin-top:70 px;
        position:  position: fixed;;
    }
 </style>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                
                <div class="sidebar-brand-text mx-3"><img src="./img/logo.png"/></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Interface
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseClients"
                    aria-expanded="true" aria-controls="collapseClients">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Gestion Client</span>
                </a>
                <div id="collapseClients" class="collapse" aria-labelledby="headingClients" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Gestion Client:</h6>
                        <a class="collapse-item" href="?entity=client&action=index" <?php  echo setActive($entity, $action, 'client', 'index'); ?>>Liste des clients</a>
                        <a class="collapse-item" href="?entity=client&action=create" <?php  echo setActive($entity, $action, 'client', 'create'); ?>>Ajouter un client</a>
                    </div>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseDevis"
                    aria-expanded="true" aria-controls="collapseDevis">
                    <i class="fas fa-fw fa-file-invoice"></i>
                    <span>Gestion Devis</span>
                </a>
                <div id="collapseDevis" class="collapse" aria-labelledby="headingDevis" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Gestion Devis:</h6>
                        <a class="collapse-item" href="index.php?entity=devis&action=index" <?php ob_start();echo setActive($entity, $action, 'devis', 'index'); ob_end_flush();?>>Liste des devis</a>
                        <a class="collapse-item" href="index.php?entity=devis&action=create" <?php ob_start();echo setActive($entity, $action, 'devis', 'create'); ob_end_flush();?>>Créer un devis</a>
                    </div>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseFacture"
                    aria-expanded="true" aria-controls="collapseFacture">
                    <i class="fas fa-fw fa-file-invoice"></i>
                    <span>Gestion Facture</span>
                </a>
                <div id="collapseFacture" class="collapse" aria-labelledby="headingDevis" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Gestion Facture:</h6>
                        <a class="collapse-item" href="index.php?entity=facture&action=index" <?php ob_start();echo setActive($entity, $action, 'facture', 'index'); ob_end_flush();?>>Liste des facture</a>
                        <a class="collapse-item" href="index.php?entity=facture&action=create" <?php ob_start();echo setActive($entity, $action, 'facture', 'create'); ob_end_flush();?>>Créer un facture</a>
                    </div>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePromotion"
                    aria-expanded="true" aria-controls="collapsePromotion">
                    <i class="fas fa-fw fa-file-invoice"></i>
                    <span>Gestion Promotion</span>
                </a>
                <div id="collapsePromotion" class="collapse" aria-labelledby="headingDevis" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Promotion</h6>
                        <a class="collapse-item" href="index.php?entity=promotion&action=index" <?php ob_start();echo setActive($entity, $action, 'promotion', 'index'); ob_end_flush();?>>Liste des promotion</a>
                        <a class="collapse-item" href="index.php?entity=promotion&action=create" <?php ob_start();echo setActive($entity, $action, 'promotion', 'create'); ob_end_flush();?>>Créer un promotion</a>
                    
                     
                    
                    
                    </div>
                </div>
            </li>

            <!-- Divider -->
            

            <!-- Divider -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
           
        </ul>
        <!-- End of Sidebar -->

        
 
<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

<!-- Main Content -->
<div id="content">

    <!-- Topbar -->
    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

        <!-- Sidebar Toggle (Topbar) -->
        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
        </button>



        <!-- Topbar Navbar -->
        <ul class="navbar-nav ml-auto">

            <!-- Nav Item - Search Dropdown (Visible Only XS) -->
            <li class="nav-item dropdown no-arrow d-sm-none">
                <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-search fa-fw"></i>
                </a>
                <!-- Dropdown - Messages -->
                <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                    aria-labelledby="searchDropdown">
                    <form class="form-inline mr-auto w-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small"
                                placeholder="Search for..." aria-label="Search"
                                aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </li>

            

            <div class="topbar-divider d-none d-sm-block"></div>

            <!-- Nav Item - User Information -->
            <li class="nav-item dropdown no-arrow">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $_SESSION['login'];?></span>
                    <img class="img-profile rounded-circle"
                        src="img/undraw_profile.svg">
                </a>
                <!-- Dropdown - User Information -->
                               
                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                    aria-labelledby="userDropdown">
                   
                
                    
                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                        Deconnexion
                    </a>
                </div>

                <!-- Logout Modal -->
                <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="logoutModalLabel">Prêt à quitter ?</h5>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">Sélectionnez "Déconnexion" ci-dessous si vous êtes prêt à deconnecter.</div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>
                                <a class="btn btn-primary" href="deconnecter.php">Deconnexion</a>
                            </div>
                        </div>
                    </div>
                </div>

            </li>

        </ul>

    </nav>
                 <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>


</body>
</html>

