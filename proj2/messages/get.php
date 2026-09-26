<?php
ob_start(); 
header('Content-Type: application/json');

session_start();
require_once __DIR__ . '/../connect.php';

if (!isset($_SESSION['student_id'])) {
    ob_end_clean();
    echo json_encode([]);
    exit;
}

$my_id = $_SESSION['student_id'];
$contact_id = isset($_GET['contact_id']) ? intval($_GET['contact_id']) : 0;

if ($contact_id > 0) {
    $sql = "SELECT m.*,s_sender.full_name AS sender_name, 
               s_receiver.full_name AS receiver_name
        FROM messages m
        JOIN students s_sender ON m.sender_id = s_sender.id
        JOIN students s_receiver ON m.receiver_id = s_receiver.id 
            WHERE (m.sender_id = ? AND m.receiver_id = ?) 
            OR (m.sender_id = ? AND m.receiver_id = ?) 
            ORDER BY created_at ASC";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$my_id, $contact_id, $contact_id, $my_id]);  
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    ob_end_clean();
    echo json_encode($messages);
} else {
    ob_end_clean();
    echo json_encode([]);
}
exit;