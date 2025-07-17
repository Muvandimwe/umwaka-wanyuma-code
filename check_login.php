<?php
session_start();
include_once '../includes/db_config.php';

header('Content-Type: application/json');

echo json_encode([
    'logged_in' => is_logged_in(),
    'user_type' => $_SESSION['user_type'] ?? null,
    'user_id' => $_SESSION['user_id'] ?? null
]);
?>
