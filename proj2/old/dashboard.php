<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

$sql = "SELECT * FROM tasks WHERE student_id = :student_id ORDER BY due_date ASC";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':student_id', $student_id);
$stmt->execute();
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord - Études</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dash-body">
    <header>
        <h1>Espace Étudiant: <?= htmlspecialchars($_SESSION['student_name']) ?></h1>
        <nav>
            <a href="#tasks">Mes Tâches</a>
            <a href="#add-task">Nouvelle Tâche</a>
             <a href="messages\messages.php">messages</a>
            <a href="logout.php" style="color: #ff6b6b;">Déconnexion</a>
           
        </nav>
        
    </header>

    <main>



         <?php

$current_id = isset($_GET['music_id']) ? intval($_GET['music_id']) : 1;

$stmt = $conn->prepare("SELECT * FROM music WHERE id = ?");
$stmt->execute([$current_id]);
$track = $stmt->fetch();
?>



<form action="play_audio.php" method="POST">
 

<section id="focus-mode">
    <h2> <?= htmlspecialchars($track['titre']) ?></h2>
 <div id="audio-div">
    <audio controls style="width: 100%;">
        <source src="<?= htmlspecialchars($track['file_path']) ?>" type="audio/mpeg">
    </audio>


</div>
    <input type="hidden" name="current_id" value="<?= $current_id ?>">
    <div class="buttons">
       
         <button class="btn-skip" type="submit" name="action" value="prev"><img src="images\skipr.png" alt="Suivant" width="30" height="30"></button>
        <button class="btn-skip" type="submit" name="action" value="next"><img src="images\skipn.png" alt="Precedant" width="30" height="30"></button>

    </div>


    
</section></form>

        <section id="add-task">
            <h2>Ajouter une Tâche</h2>

            <form action="process_task.php" method="POST">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <input type="text" name="title" placeholder="Titre de la tâche" required>
                    <input type="date" name="due_date" onfocus="this.min=new Date().toISOString().split('T')[0]" required>
                </div>



                <div class="radio-group">
                    <label class="priority"><input type="radio" name="priority"  value="Low"> Faible</label>
                    <label class="priority"><input type="radio" name="priority"  value="Medium" checked> Moyenne</label>
                    <label class="priority"><input type="radio" name="priority"  value="High"> Haute</label>
                </div>

                <textarea name="description" placeholder="Détails (ex: Finir les matrices de GLCM)..." ></textarea>
                <button type="submit" class="btn-primary">Ajouter</button>
            </form>
        </section>
        

        <section id="tasks">
            <h2>Taches en cours</h2>



            <?php if(empty($tasks)): ?>
                <p>Aucune tâche pour le moment!</p>
            <?php else: ?>
            <?php foreach($tasks as $task): ?>
                    <div class="task-card">
                        <div class="task-info">
                            <h3><?= htmlspecialchars($task['title']) ?> <span class="badge priority-<?= strtolower($task['priority']) ?>"><?= $task['priority'] ?></span></h3>
                            <p><strong>Pour le :</strong> <?= date('d/m/Y', strtotime($task['due_date'])) ?></p>
                            <p><?= nl2br(htmlspecialchars($task['description'])) ?></p> 

                        </div>
                        <form action="process_task.php" method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="delete">
                          <input type="hidden" name="task_id" value="<?= $task['id'] ?>"> 
                            <button type="submit" class="btn-danger">Terminer / Supprimer</button>
                        </form>
                    </div>
            <?php endforeach; ?>
            <?php endif; ?>


        </section>
    </main>
</body>
</html>