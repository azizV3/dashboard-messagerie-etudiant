<?php

ob_start(); 
header('Content-Type: application/json');
//more secure
session_start();
require_once __DIR__ . '/../connect.php';

if (!isset($_SESSION['student_id'])) {
    ob_end_clean();
    echo json_encode(["status" => "error", "message" => "Not logged in"]);
    exit;
}

$sender_id = $_SESSION['student_id'];
$receiver_id = isset($_POST['receiver_id']) ? intval($_POST['receiver_id']) : 0;
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

if ($receiver_id > 0 && !empty($message)) {
    try {
        $sql = "INSERT INTO messages (sender_id, receiver_id, message_text, is_read) 
                VALUES (?, ?, ?, 0)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$sender_id, $receiver_id, $message]);

        ob_end_clean();
        echo json_encode(["status" => "success"]);
        
    } catch (PDOException $e) {
        ob_end_clean();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
} else {
    ob_end_clean();
    echo json_encode(["status" => "error", "message" => "Invalid recipient or empty message"]);
}
exit;