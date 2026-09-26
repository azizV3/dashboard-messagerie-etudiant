<?php
require_once 'connect.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = htmlspecialchars($_POST['full_name']);
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $university = htmlspecialchars($_POST['university']);


    try {
        $sql = "INSERT INTO students (full_name, email, password, university) 
                VALUES (:full_name, :email, :password, :university)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':university', $university);
        
        if ($stmt->execute()) {
            header("Location: login.php?msg=registered");
            exit();
        }
    } catch(Exception $e) {
        $error = "Cet email est déjà utilisé.";
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription Étudiant</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-body">
    <div class="login-container">
        <h1>Créer un compte</h1>
        <?php if($error): ?><p style="color: #ff6b6b;"><?= $error ?></p><?php endif; ?>
        <form action="register.php" method="POST">
            <input type="text" name="full_name" placeholder="Nom Complet" required>
            <input type="email" name="email" placeholder="Email Étudiant" required>
            <input type="text" name="university" placeholder="Université (ex: ISI El Manar)" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <input type="submit" value="S'inscrire">
        </form>
        <h6>Déjà un compte ? <a href="login.php">Se connecter</a></h6>
    </div>
</body>
</html>