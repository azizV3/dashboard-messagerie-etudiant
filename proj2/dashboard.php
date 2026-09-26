<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

$sql = "SELECT * FROM tasks WHERE student_id = :student_id";
$params = [':student_id' => $student_id];

$filter_priority = isset($_GET['filter_priority']) ? $_GET['filter_priority'] : '';
$search_keyword = isset($_GET['search_keyword']) ? trim($_GET['search_keyword']) : '';

if ($filter_priority && in_array($filter_priority, ['Low', 'Medium', 'High'])) {
    $sql .= " AND priority = :priority";
    $params[':priority'] = $filter_priority;
}

if ($search_keyword !== '') {
    $sql .= " AND (title LIKE :keyword OR description LIKE :keyword)";
    $params[':keyword'] = '%' . $search_keyword . '%';
}

$sql .= " ORDER BY due_date ASC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);




// --------------------------------
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord - etudes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dash-body">
    <header>
        <h1>Espace Etudiant: <?= htmlspecialchars($_SESSION['student_name']) ?></h1>
        <nav>
            <a href="#tasks">Mes Taches</a>
            <a href="#add-task">Nouvelle Tache</a>
             <a href="messages\messages.php">messages</a>
            <a href="logout.php" style="color: #ff6b6b;">Deconnexion</a>
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
        <h2> <?= htmlspecialchars($track['titre'] ?? 'No Track') ?></h2>
        <div id="audio-div">
            <audio controls style="width: 100%;">
                <source src="<?= htmlspecialchars($track['file_path'] ?? '') ?>" type="audio/mpeg">
            </audio>
        </div>
        
        <input type="hidden" name="current_id" value="<?= $current_id ?>">
        <input type="hidden" name="filter_priority" value="<?= htmlspecialchars($filter_priority) ?>">
        <input type="hidden" name="search_keyword" value="<?= htmlspecialchars($search_keyword) ?>">

        <div class="buttons">
            <button class="btn-skip" type="submit" name="action" value="prev"><img src="images\skipr.png" alt="Precedent" width="30" height="30"></button>

            <button class="btn-skip" type="submit" name="action" value="next"><img src="images\skipn.png" alt="Suivant" width="30" height="30"></button>
        </div>
    </section>
</form>

        <section id="add-task">
            <h2>Ajouter une Tâche</h2>
            <form action="process_task.php" method="POST">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <input type="text" name="title" placeholder="Titre de la taache" required>

                    <input type="date" name="due_date" onfocus="this.min=new Date().toISOString().split('T')[0]" required>

                </div>
                <div class="radio-group">

                    <label class="priority"><input type="radio" name="priority" value="Low"> Faible</label>
                    <label class="priority"><input type="radio" name="priority" value="Medium" checked> Moyenne</label>
                    <label class="priority"><input type="radio" name="priority" value="High"> Haute</label>

                </div>

                <textarea name="description" placeholder="Details (ex: Finir les matrices de GLCM)..." ></textarea>
                <button type="submit" class="btn-primary">Ajouter</button>
            </form>
        </section>
        
        <section id="tasks">


            <h2>Taches en cours</h2>

            
            <form method="GET" action="dashboard.php" style="margin-bottom: 20px; display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                
                
                <?php if(isset($_GET['music_id'])): ?>
                    <input type="hidden" name="music_id" value="<?= htmlspecialchars($_GET['music_id']) ?>">
                <?php endif; ?>
                
                <div style="display: flex; align-items: center; gap: 5px;">
                    <label for="search_keyword"><strong>Recherche:</strong></label>
                    <input type="text" name="search_keyword" id="search_keyword" placeholder="Mot-cle" value="<?= htmlspecialchars($search_keyword) ?>" style="padding: 5px; border-radius: 4px; border: 1px solid #ccc;">
                </div>

                <div style="display: flex; align-items: center; gap: 5px;">
                    <label for="filter_priority"><strong>Priorite:</strong></label>
                    <select name="filter_priority" id="filter_priority" style="padding: 6px; border-radius: 4px; border: 1px solid #ccc;">
                        <option value="">Toutes</option>

                        <option value="Low" <?= $filter_priority === 'Low' ? 'selected' : '' ?>>Faible</option>
                        <option value="Medium" <?= $filter_priority === 'Medium' ? 'selected' : '' ?>>Moyenne</option>
                        <option value="High" <?= $filter_priority === 'High' ? 'selected' : '' ?>>Haut</option>

                    </select>
                </div>

                <div style="display: flex; gap: 5px;">
                    <button type="submit" class="btn-primary" style="padding: 6px 15px;">Appliquer</button>
                    <a href="dashboard.php<?= isset($_GET['music_id']) ? '?music_id=' . htmlspecialchars($_GET['music_id']) : '' ?>" class="btn-danger" >Réinitialiser</a>
                </div>

            </form>

            <?php if(empty($tasks)): ?>
                <p>Aucune tache trouvee correspondant a vos criteres!!!</p>
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