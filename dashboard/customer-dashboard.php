<?php
    include './middleware.php';
    require_once '../includes/dbh.inc.php';

    $user_id = $_SESSION['user_id'];
    $name = $_SESSION['user_name'];

    // Fetch user's orders
    $stmt = $pdo->prepare("SELECT o.*, r.name as restaurant_name 
                          FROM orders o 
                          JOIN restaurants r ON o.restaurant_id = r.id 
                          WHERE o.customer_id = ? 
                          ORDER BY o.created_at DESC");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll();

    // Fetch nearby restaurants
    $restaurantStmt = $pdo->prepare("SELECT * FROM restaurants ORDER BY RAND() LIMIT 6");
    $restaurantStmt->execute();
    $restaurants = $restaurantStmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Dashboard - Sweet Bites</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #ff7b25;
            --primary-light: #ff9d5c;
            --background: #EDEDEDFF;
            --text: #363636;
            --card-bg: #ffffff;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--background);
            color: var(--text);
        }

        .dashboard-section { 
            margin-bottom: 2rem;
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            padding: 2rem;
        }

        .restaurant-card { 
            transition: transform 0.3s ease;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        .restaurant-card:hover { 
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        }

        .restaurant-image {
            height: 200px;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .restaurant-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0.1), rgba(0,0,0,0.4));
        }

        .navbar {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
            padding: 1rem 2rem;
        }

        .welcome-banner {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            padding: 3rem 2rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .welcome-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
        }

        .status-delivered {
            background-color: #48c774;
            color: white;
        }

        .status-pending {
            background-color: #ffdd57;
            color: #363636;
        }

        .status-processing {
            background-color: #3298dc;
            color: white;
        }

        .tabs.is-toggle a {
            border-radius: 20px;
            margin: 0 0.5rem;
            font-weight: 500;
        }

        .tabs.is-toggle a.is-active {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .profile-section {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        .order-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }

        .order-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <a class="navbar-item" href="../index.php">
                <h1 class="title is-4 has-text-primary">Sweet Bites</h1>
            </a>
        </div>
        <div class="navbar-end">
            <div class="navbar-item">
                <div class="buttons">
                    <a href="logout.php" class="button is-light">
                        <span class="icon">
                            <i class="fas fa-sign-out-alt"></i>
                        </span>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <section class="section">
        <div class="container">
            <!-- Welcome Banner -->
            <div class="welcome-banner">
                <h1 class="title is-2 has-text-white">Welcome back, <?= htmlspecialchars($name) ?>! 👋</h1>
                <p class="subtitle is-5 has-text-white">What would you like to order today?</p>
            </div>

            <!-- Navigation Tabs -->
            <div class="tabs is-toggle is-fullwidth is-large mb-6">
                <ul>
                    <li class="is-active"><a href="#profile">Profile</a></li>
                    <li><a href="#orders">My Orders</a></li>
                    <li><a href="#restaurants">Restaurants</a></li>
                </ul>
            </div>

            <!-- Profile Section -->
            <div id="profile" class="dashboard-section">
                <h2 class="title is-4 mb-4">My Profile</h2>
                <form action="update_profile.php" method="POST">
                    <div class="field">
                        <label class="label">Full Name</label>
                        <div class="control has-icons-left">
                            <input class="input" type="text" name="name" value="<?= htmlspecialchars($name) ?>" required>
                            <span class="icon is-small is-left">
                                <i class="fas fa-user"></i>
                            </span>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Email</label>
                        <div class="control has-icons-left">
                            <input class="input" type="email" name="email" value="<?= htmlspecialchars($_SESSION['user_email']) ?>" required>
                            <span class="icon is-small is-left">
                                <i class="fas fa-envelope"></i>
                            </span>
                        </div>
                    </div>
                    <button class="button is-primary">
                        <span class="icon">
                            <i class="fas fa-save"></i>
                        </span>
                        <span>Update Profile</span>
                    </button>
                </form>
            </div>

            <!-- Orders Section -->
            <div id="orders" class="dashboard-section" style="display: none;">
                <h2 class="title is-4 mb-4">My Orders</h2>
                <?php if (empty($orders)): ?>
                    <div class="has-text-centered py-6">
                        <span class="icon is-large has-text-grey-light">
                            <i class="fas fa-receipt fa-3x"></i>
                        </span>
                        <p class="mt-3">You haven't placed any orders yet.</p>
                        <a href="#restaurants" class="button is-primary mt-4">Browse Restaurants</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card">
                            <div class="columns is-vcentered">
                                <div class="column">
                                    <h3 class="title is-5">Order #<?= $order['id'] ?></h3>
                                    <p class="subtitle is-6"><?= htmlspecialchars($order['restaurant_name']) ?></p>
                                </div>
                                <div class="column is-narrow">
                                    <span class="tag is-large is-<?= $order['status'] === 'delivered' ? 'success' : 'warning' ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </div>
                                <div class="column is-narrow">
                                    <p class="has-text-weight-bold">$<?= number_format($order['total'], 2) ?></p>
                                    <p class="is-size-7 has-text-grey"><?= date('M d, Y', strtotime($order['created_at'])) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Restaurants Section -->
            <div id="restaurants" class="dashboard-section" style="display: none;">
                <h2 class="title is-4 mb-4">Featured Restaurants</h2>
                <div class="columns is-multiline">
                    <?php foreach ($restaurants as $restaurant): ?>
                        <div class="column is-4">
                            <div class="restaurant-card">
                                <div class="restaurant-image" style="background-image: url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80')">
                                </div>
                                <div class="card-content">
                                    <div class="content">
                                        <h3 class="title is-4"><?= htmlspecialchars($restaurant['name']) ?></h3>
                                        <p class="subtitle is-6">
                                            <span class="icon has-text-warning">
                                                <i class="fas fa-star"></i>
                                            </span>
                                            <span>4.5</span>
                                            <span class="ml-2 has-text-grey">
                                                <i class="fas fa-clock"></i> 30-45 min
                                            </span>
                                        </p>
                                        <p class="has-text-grey">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <?= htmlspecialchars($restaurant['address']) ?>
                                        </p>
                                        <a href="../restaurant.php?id=<?= $restaurant['id'] ?>" class="button is-primary is-fullwidth mt-4">
                                            <span class="icon">
                                                <i class="fas fa-utensils"></i>
                                            </span>
                                            <span>View Menu</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Tab switching functionality
        document.querySelectorAll('.tabs a').forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Update active tab
                document.querySelectorAll('.tabs li').forEach(li => li.classList.remove('is-active'));
                this.parentElement.classList.add('is-active');
                
                // Show corresponding section
                const targetId = this.getAttribute('href').substring(1);
                document.querySelectorAll('.dashboard-section').forEach(section => {
                    section.style.display = 'none';
                });
                document.getElementById(targetId).style.display = 'block';
            });
        });

        // Session timeout handling
        let sessionTimeout;
        const TIMEOUT_DURATION = 40000; // 40 seconds in milliseconds
        const WARNING_DURATION = 10000; // Show warning 10 seconds before timeout

        function resetSessionTimer() {
            clearTimeout(sessionTimeout);
            sessionTimeout = setTimeout(showTimeoutWarning, TIMEOUT_DURATION - WARNING_DURATION);
        }

        function showTimeoutWarning() {
            // Create warning notification
            const notification = document.createElement('div');
            notification.className = 'notification is-warning is-light';
            notification.style.position = 'fixed';
            notification.style.top = '20px';
            notification.style.right = '20px';
            notification.style.zIndex = '1000';
            notification.innerHTML = `
                <button class="delete"></button>
                Your session will expire in 10 seconds. Click anywhere to stay logged in.
            `;
            document.body.appendChild(notification);

            // Add click handler to dismiss notification and reset timer
            notification.querySelector('.delete').addEventListener('click', () => {
                notification.remove();
                resetSessionTimer();
            });

            // Set timeout for actual logout
            setTimeout(() => {
                window.location.href = 'logout.php';
            }, WARNING_DURATION);
        }

        // Reset timer on user activity
        ['click', 'mousemove', 'keypress'].forEach(event => {
            document.addEventListener(event, resetSessionTimer);
        });

        // Initialize timer
        resetSessionTimer();

        // Add periodic check for server-side session status
        setInterval(() => {
            fetch('check_session.php')
                .then(response => response.json())
                .then(data => {
                    if (!data.active) {
                        window.location.href = 'logout.php';
                    }
                })
                .catch(error => console.error('Session check failed:', error));
        }, 5000); // Check every 5 seconds
    </script>
</body>
</html> 