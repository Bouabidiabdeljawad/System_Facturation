<?php
session_start();
include('./includes/connection.php');

// Initialize the session variable to count failed attempts
if (!isset($_SESSION['tentatives_echouees'])) {
    $_SESSION['tentatives_echouees'] = 0;
}

$message = ''; 

// Check if POST method is used (login attempt)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];
    $motdepasse = $_POST['motdepasse'];

    // SQL query to fetch the user based on the login (email)
    $sql = "SELECT * FROM admin WHERE login = :login";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':login', $login);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the account is activated
        if ($user['valide'] == 0) {
            $_SESSION['blocked_message'] = "Votre compte est desactivé. Pour l'activer, saisissez le code envoyé par email.";
            header('Location: login.php');
            exit;
        } else {
            // Password verification (with hashed password)
            if (password_verify($motdepasse, $user['motdepasse'])) {
                // Correct password
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['login'] = $user['login'];
                $_SESSION['tentatives_echouees'] = 0;

                header('Location: index.php');
                exit;
            } elseif ($user['motdepasse'] === $motdepasse) {
                // If the entered password matches the plain password (legacy, not secure)
                $hashedPassword = password_hash($motdepasse, PASSWORD_BCRYPT);

                // Update the password with the hashed version
                $updatePasswordSql = "UPDATE admin SET motdepasse = :motdepasse WHERE id = :id";
                $updateStmt = $pdo->prepare($updatePasswordSql);
                $updateStmt->bindParam(':motdepasse', $hashedPassword);
                $updateStmt->bindParam(':id', $user['id']);
                $updateStmt->execute();

                // Set session variables after successful login
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['login'] = $user['login'];
                $_SESSION['tentatives_echouees'] = 0;

                header('Location: index.php');
                exit;
            } else {
                // Incorrect password
                $_SESSION['tentatives_echouees']++;

                // Lock the account after 3 failed attempts and send verification code
                if ($_SESSION['tentatives_echouees'] >= 3) {
                    $code_verification = rand(100000, 999999);
                    $_SESSION['validation_code'] = $code_verification;
                    include('message.php');

                    // Update account status to blocked and set the verification code
                    $sql_block = "UPDATE admin SET valide = 0, validation_code = :validation_code WHERE id = :id";
                    $stmt_block = $pdo->prepare($sql_block);
                    $stmt_block->bindParam(':id', $user['id']);
                    $stmt_block->bindParam(':validation_code', $code_verification);
                    $stmt_block->execute();

                    $_SESSION['blocked_message'] = "Votre compte est bloqué. Code d'activation envoyé par mail.";
                } else {
                    $message = "Mot de passe invalide.";
                }
            }
        }
    } else {
        $message = "Login invalide.";
    }
}

// Verification code processing (to unlock the account)
if (isset($_POST['code_verification'])) {
    $code = $_POST['code_verification'];

    $sql_verify = "SELECT * FROM admin WHERE validation_code = :validation_code";
    $stmt_verify = $pdo->prepare($sql_verify);
    $stmt_verify->bindParam(':validation_code', $code);
    $stmt_verify->execute();

    if ($stmt_verify->rowCount() > 0) {
        $user = $stmt_verify->fetch(PDO::FETCH_ASSOC);

        // Unlock the account after successful verification
        $sql_unlock = "UPDATE admin SET valide = 1, validation_code = NULL WHERE id = :id";
        $stmt_unlock = $pdo->prepare($sql_unlock);
        $stmt_unlock->bindParam(':id', $user['id']);
        $stmt_unlock->execute();

        $_SESSION['tentatives_echouees'] = 0;

        header('Location: login.php');
        exit;
    } else {
        $_SESSION['blocked_message'] = "Code de vérification invalide.";
        header('Location: login.php');
        exit;
    }
}
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Login</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <script>
        document.addEventListener("DOMContentLoaded", function () {
           
            <?php if (isset($message) && $message !== '') { ?>
                alert("<?php echo $message; ?>");
            <?php } ?>

            // Show verification code popup if account is blocked
            <?php if (isset($_SESSION['blocked_message']) && strpos($_SESSION['blocked_message'], 'Votre compte bloque') !== false) { ?>
                document.getElementById('verificationPopup').classList.add('show');
            <?php } ?>

            // Close the popup if the account was unlocked
            <?php if (isset($_SESSION['blocked_message']) && strpos($_SESSION['blocked_message'], 'Votre Compte Active Maintenant') !== false) { ?>
                setTimeout(function() {
                    document.getElementById('verificationPopup').classList.remove('show');
                }, 3000); 
            <?php } ?>
        });
    </script>

    <style>
        /* Styling for the popup */
        #verificationPopup {
            display: none;
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 9999;
            transition: all 0.3s ease;
            justify-content: center;
            align-items: center;
        }

        #verificationPopup.show {
            display: flex;
        }

        .popup-content {
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            width: 400px;
            box-shadow: 0px 10px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            animation: fadeIn 0.5s ease-in-out;
        }

        .popup-content h2 {
            margin-bottom: 15px;
            color: #333;
        }

        .popup-content input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .popup-content button {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .popup-content button:hover {
            background-color: #45a049;
        }

       

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="bg-gradient-primary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0"> 
                        <div class="row">
                        <div class="row">
                                <!-- Image à gauche -->
                                <div class="col-lg-6 d-none d-lg-block bg-login-image">
                                    <img src="img/login.jpg" alt="Image de connexion" class="img-fluid" style="height: 100%; object-fit: cover;">
                                </div>

                                <!-- Formulaire à droite -->
                                <div class="col-lg-6">
                                    <div class="p-5">
                                        <div class="text-center">
                                            <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                                        </div>

                                        <form class="user" method="POST" action="">
                                            <div class="form-group">
                                                <input type="text" class="form-control form-control-user"
                                                    placeholder="Login (Email)" name="login" value="ali" required>
                                            </div>
                                            <div class="form-group">
                                                <input type="password" class="form-control form-control-user"
                                                    placeholder="Password" value="123" name="motdepasse" required>
                                            </div>

                                            <button type="submit" class="btn btn-primary btn-user btn-block">Login</button>
                                        </form>

                                        <div class="text-center">
                                            <a class="small" href="forgot-password.html">Forgot Password?</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Verification Code Popup -->
        <div id="verificationPopup">
            <div class="popup-content">
                <h2>Enter Verification Code</h2>
                <form method="POST">
                    <input type="text" name="code_verification" placeholder="Verification Code" required>
                    <button type="submit">Verify</button>
                    <button type="button" class="close-popup" onclick="document.getElementById('verificationPopup').classList.remove('show')">X</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
