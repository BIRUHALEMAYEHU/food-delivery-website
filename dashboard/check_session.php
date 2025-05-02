<?php
require_once '../includes/session_manager.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if session is active
$response = [
    'active' => isSessionActive(),
    'remaining_time' => getRemainingSessionTime()
];

echo json_encode($response); 