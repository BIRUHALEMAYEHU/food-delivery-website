<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get basic user information
    $role = $_POST['role'];
    $fname = $_POST['fname'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        require_once '../includes/dbh.inc.php';

        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $_SESSION['error'] = "Email already exists. Please use a different email.";
            header("Location: signup.php?role=" . $role);
            exit();
        }

        // Start transaction
        $pdo->beginTransaction();

        // Insert user
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$fname, $email, password_hash($password, PASSWORD_DEFAULT), $role]);
        $userId = $pdo->lastInsertId();

        // Handle role-specific data
        if ($role === 'restaurant') {
            $restaurantName = $_POST['restaurant_name'];
            $phone = $_POST['phone'];
            $address = $_POST['address'];

            $stmt = $pdo->prepare("INSERT INTO restaurants (user_id, name, address, phone) VALUES (?, ?, ?, ?)");
            $stmt->execute([$userId, $restaurantName, $address, $phone]);
        }

        if ($role === 'delivery') {
            $phone = $_POST['phone'];
            $vehicleType = $_POST['vehicle_type'];

            $stmt = $pdo->prepare("INSERT INTO delivery_profiles (user_id, phone, vehicle_type) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $phone, $vehicleType]);
        }

        // Commit transaction
        $pdo->commit();

        // Set session variables
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $fname;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_role'] = $role;

        // Redirect based on role
        switch ($role) {
            case 'customer':
                header("Location: ../dashboard/customer-dashboard.php");
                break;
            case 'restaurant':
                header("Location: ../dashboard/restaurant-dashboard.php");
                break;
            case 'delivery':
                header("Location: ../dashboard/delivery-dashboard.php");
                break;
            default:
                header("Location: ../auth/login.php");
        }
        exit();

    } catch (PDOException $e) {
        // Rollback transaction on error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $_SESSION['error'] = "Registration failed: " . $e->getMessage();
        header("Location: signup.php?role=" . $role);
        exit();
    }
} else {
    header("Location: ../onboarding.php");
    exit();
}