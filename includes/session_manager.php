<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set session timeout to 40 seconds
$timeout = 40;

// Check if the last activity timestamp exists
if (isset($_SESSION['last_activity'])) {
    // Calculate time difference
    $time_diff = time() - $_SESSION['last_activity'];
    
    // Debug log
    error_log("Session check - Time difference: " . $time_diff . " seconds");
    
    // If more than timeout seconds have passed, destroy the session
    if ($time_diff > $timeout) {
        error_log("Session expired - Destroying session");
        
        // Unset all session variables
        $_SESSION = array();
        
        // Destroy the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-3600, '/');
        }
        
        // Destroy the session
        session_destroy();
        
        // Redirect to login page
        header("Location: /food-delivery-website/auth/login.php?error=session_expired");
        exit();
    }
}

// Update last activity timestamp
$_SESSION['last_activity'] = time();

// Function to check if session is active
function isSessionActive() {
    return isset($_SESSION['user_id']) && isset($_SESSION['last_activity']);
}

// Function to get remaining session time in seconds
function getRemainingSessionTime() {
    global $timeout;
    if (isset($_SESSION['last_activity'])) {
        $time_diff = time() - $_SESSION['last_activity'];
        return max(0, $timeout - $time_diff);
    }
    return 0;
} 