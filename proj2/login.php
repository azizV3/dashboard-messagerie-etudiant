<?php
session_start();
require_once 'connect.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? ''); // prepared statement handles SQL; no HTML-escaping before storage/lookup
    $password = $_POST['password'];

    $sql = "SELECT * FROM students WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student && password_verify($password, $student['password'])) {
        session_regenerate_id(true); 
        $_SESSION['student_id'] = $student['id'];
        $_SESSION['student_name'] = $student['full_name'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Email ou mot de passe incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Étudiant</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-body">
    <div class="login-container">
        <h1>Connexion</h1>
        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'registered'): ?>
            <p style="color: #20c997;">Inscription réussie ! Connectez-vous.</p>
        <?php endif; ?>
        <?php if($error): ?><p style="color: #ff6b6b;"><?= $error ?></p><?php endif; ?>
        
        <form action="login.php" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <input type="submit" value="Se connecter">
        </form>
        <h6>Pas encore de compte ? <a href="register.php">S'inscrire</a></h6>
    </div>
</body>
</html>