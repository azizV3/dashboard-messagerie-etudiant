<?php
session_start();
require_once __DIR__ . '/../connect.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$my_id = $_SESSION['student_id'];
$my_name = $_SESSION['student_name'];

$query = "SELECT id, full_name FROM students WHERE id != ?";
$stmt = $conn->prepare($query);
$stmt->execute([$my_id]);
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC); 
?>




<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Messagerie</title>
    <link rel="stylesheet" href="style1.css">
    <script>
        const mySessionId = <?= json_encode($my_id) ?>;
    </script>
</head>
<body class="message-body">




<aside class="sidebar">
<h1 class="text-xl">Connecté en tant que : <?= htmlspecialchars($my_name) ?></h1>    
    <nav class="nav-menu">
        <li class="nav-item"><a href="../dashboard.php">Dashboard</a></li>

    </nav>


    
</aside>

<main class="message-main">
    <header style="margin-bottom: 30px;">
        <h1 class="text-xl">Messagerie</h1>
        
    </header>

    <section id="messages-interface" class="card messaging-card">
        <div class="contact-list">
            <div style="padding: 1rem; border-bottom: 1px solid rgba(255,255,255,0.05);">
                <span class="text-sm bold">Étudiants</span>
            </div>
            <?php foreach ($contacts as $contact): ?>
                <div class="contact-item" onclick="loadMessages(<?= $contact['id'] ?>)">
                    <div class="flex" style="align-items: center; gap: 10px;">
                        <div class="avatar-circle"><?= strtoupper(substr($contact['full_name'], 0, 1)) ?></div>
                        <span><?= htmlspecialchars($contact['full_name']) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="chat-view">
            <div id="chat-header" class="text-sm bold" style="padding: 1rem; background: rgba(0,0,0,0.1);">
                Sélectionnez une discussion
            </div>
            
            <div id="messages" class="messages-display">
                <p class="text-muted center" style="height: 100%;">Commencez à discuter avec vos camarades.</p>
            </div>

            <div class="chat-footer">
                <input type="text" id="message-input" class="glass-input" placeholder="Écrivez votre message...">
                <button class="btn-glow" onclick="envoyerMessage()">Envoyer</button>
            </div>
        </div>
    </section>
</main>

<script src="messages.js"></script>
</body>
</html>