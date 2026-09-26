<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $title = trim($_POST['title']);
        $due_date = $_POST['due_date'];
        $priority = $_POST['priority'] ?? 'Medium';
        $description = trim($_POST['description']);

        if (!empty($title) && !empty($due_date)) {
            $sql = "INSERT INTO tasks (student_id, title, description, priority, due_date) 
                    VALUES (:student_id, :title, :description, :priority, :due_date)";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':student_id' => $student_id,
                ':title'      => $title,
                ':description' => $description,
                ':priority'   => $priority,
                ':due_date'   => $due_date
            ]);
        }
    }

    if ($action === 'delete') {
        $task_id = intval($_POST['task_id']);

        $sql = "DELETE FROM tasks WHERE id = :id AND student_id = :student_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':id' => $task_id,
            ':student_id' => $student_id
        ]);
    }

    header("Location: dashboard.php#tasks");
    exit();
} else {
    header("Location: dashboard.php");
    exit();
}