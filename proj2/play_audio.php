<?php
require_once 'connect.php';
$new_id = 1;

if (isset($_POST['current_id']) && isset($_POST['action'])) {
    $current_id = intval($_POST['current_id']);
    $action = $_POST['action'];

    $filter_priority = $_POST['filter_priority'] ?? '';
    $search_keyword = $_POST['search_keyword'] ?? '';

    if ($action === 'next') {
        $stmt = $conn->prepare("SELECT id FROM music WHERE id > ? ORDER BY id ASC LIMIT 1");
        $stmt->execute([$current_id]);
    } else {
        $stmt = $conn->prepare("SELECT id FROM music WHERE id < ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$current_id]);
    }

    $result = $stmt->fetch();
    $new_id = $result ? $result['id'] : $current_id;

    $query_params = [
        'music_id' => $new_id,
        'filter_priority' => $filter_priority,
        'search_keyword' => $search_keyword
    ];

    header("Location: dashboard.php?" . http_build_query($query_params));
    exit();
}

header("Location: dashboard.php");
exit();
?>