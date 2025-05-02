<?php
    require_once __DIR__ . '/../includes/session_manager.php';

    if(!isset($_SESSION['user_id'])) {
        header('Location: ../auth/login.php');
        exit();
    }

    $role = $_SESSION['user_role'];
    $current_page = basename($_SERVER['PHP_SELF']);

    // Define role-specific dashboards
    $role_dashboards = [
        'customer' => 'customer-dashboard.php',
        'restaurant' => 'restaurant-dashboard.php',
        'delivery' => 'delivery-dashboard.php'
    ];

    // If accessing the root dashboard directory or home.php, redirect to role-specific dashboard
    if($current_page === 'index.php' || $current_page === 'home.php' || $current_page === '') {
        if(isset($role_dashboards[$role])) {
            header('Location: ' . $role_dashboards[$role]);
            exit();
        } else {
            header('Location: ../auth/login.php');
            exit();
        }
    }

    // Verify user has access to the current page
    $allowed_pages = [
        'customer' => ['customer-dashboard.php'],
        'restaurant' => ['restaurant-dashboard.php'],
        'delivery' => ['delivery-dashboard.php']
    ];

    if(!in_array($current_page, $allowed_pages[$role])) {
        header('Location: ../auth/login.php');
        exit();
    }

    if ($role == "restaurant") {
        header('Location: ./restaurant/index.php'); 
        exit();
    }elseif ($role == "customer") {
        header('Location: ./customer/index.php'); 
        exit();
    }elseif ($role == "delivery") {
        header('Location: ./delivery/index.php'); 
        exit();
    }