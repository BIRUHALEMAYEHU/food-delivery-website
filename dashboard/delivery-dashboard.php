<?php
    include './middleware.php';
    require_once '../includes/dbh.inc.php';

    $delivery_id = $_SESSION['user_id'];
    $name = $_SESSION['user_name'];

    // Fetch delivery person's orders
    $stmt = $pdo->prepare("SELECT o.*, r.name as restaurant_name, u.name as customer_name, u.email as customer_email 
                          FROM orders o 
                          JOIN restaurants r ON o.restaurant_id = r.id 
                          JOIN users u ON o.customer_id = u.id 
                          WHERE o.delivery_id = ? 
                          ORDER BY o.created_at DESC");
    $stmt->execute([$delivery_id]);
    $orders = $stmt->fetchAll();

    // Fetch available orders
    $availableStmt = $pdo->prepare("SELECT o.*, r.name as restaurant_name, u.name as customer_name 
                                   FROM orders o 
                                   JOIN restaurants r ON o.restaurant_id = r.id 
                                   JOIN users u ON o.customer_id = u.id 
                                   WHERE o.status = 'ready' AND o.delivery_id IS NULL 
                                   ORDER BY o.created_at ASC");
    $availableStmt->execute();
    $available_orders = $availableStmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delivery Dashboard - Sweet Bites</title>
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

        .tabs.is-toggle a {
            border-radius: 20px;
            margin: 0 0.5rem;
            font-weight: 500;
        }

        .tabs.is-toggle a.is-active {
            background-color: var(--primary);
            border-color: var(--primary);
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

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
        }

        .status-ready { background-color: #3298dc; color: white; }
        .status-picked_up { background-color: #ff7b25; color: white; }
        .status-delivered { background-color: #48c774; color: white; }

        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
        }

        .stats-label {
            color: #666;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        .map-container {
            height: 300px;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .map-container iframe {
            width: 100%;
            height: 100%;
            border: none;
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
                <h1 class="title is-2 has-text-white">Welcome, <?= htmlspecialchars($name) ?>! 🛵</h1>
                <p class="subtitle is-5 has-text-white">Ready to deliver some delicious food?</p>
            </div>

            <!-- Quick Stats -->
            <div class="columns mb-6">
                <div class="column is-3">
                    <div class="stats-card">
                        <div class="stats-number"><?= count($orders) ?></div>
                        <div class="stats-label">Total Deliveries</div>
                    </div>
                </div>
                <div class="column is-3">
                    <div class="stats-card">
                        <div class="stats-number">4.9</div>
                        <div class="stats-label">Rating</div>
                    </div>
                </div>
                <div class="column is-3">
                    <div class="stats-card">
                        <div class="stats-number">98%</div>
                        <div class="stats-label">On-time Delivery</div>
                    </div>
                </div>
                <div class="column is-3">
                    <div class="stats-card">
                        <div class="stats-number">$<?= number_format(array_sum(array_column($orders, 'total')) * 0.1, 2) ?></div>
                        <div class="stats-label">Total Earnings</div>
                    </div>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <div class="tabs is-toggle is-fullwidth is-large mb-6">
                <ul>
                    <li class="is-active"><a href="#available">Available Orders</a></li>
                    <li><a href="#my-deliveries">My Deliveries</a></li>
                    <li><a href="#profile">Profile</a></li>
                </ul>
            </div>

            <!-- Available Orders Section -->
            <div id="available" class="dashboard-section">
                <h2 class="title is-4 mb-4">Available Orders</h2>
                <?php if (empty($available_orders)): ?>
                    <div class="has-text-centered py-6">
                        <span class="icon is-large has-text-grey-light">
                            <i class="fas fa-box fa-3x"></i>
                        </span>
                        <p class="mt-3">No orders available at the moment.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($available_orders as $order): ?>
                        <div class="order-card">
                            <div class="columns is-vcentered">
                                <div class="column">
                                    <h3 class="title is-5">Order #<?= $order['id'] ?></h3>
                                    <p class="subtitle is-6">
                                        <span class="icon has-text-grey">
                                            <i class="fas fa-store"></i>
                                        </span>
                                        <?= htmlspecialchars($order['restaurant_name']) ?>
                                    </p>
                                    <p class="is-size-7 has-text-grey">
                                        <span class="icon has-text-grey">
                                            <i class="fas fa-user"></i>
                                        </span>
                                        <?= htmlspecialchars($order['customer_name']) ?>
                                    </p>
                                </div>
                                <div class="column is-narrow">
                                    <span class="tag is-large is-info">Ready for Pickup</span>
                                </div>
                                <div class="column is-narrow">
                                    <p class="has-text-weight-bold">$<?= number_format($order['total'], 2) ?></p>
                                    <p class="is-size-7 has-text-grey"><?= date('M d, Y', strtotime($order['created_at'])) ?></p>
                                </div>
                                <div class="column is-narrow">
                                    <button class="button is-primary">
                                        <span class="icon">
                                            <i class="fas fa-check"></i>
                                        </span>
                                        <span>Accept Order</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- My Deliveries Section -->
            <div id="my-deliveries" class="dashboard-section" style="display: none;">
                <h2 class="title is-4 mb-4">My Deliveries</h2>
                <?php if (empty($orders)): ?>
                    <div class="has-text-centered py-6">
                        <span class="icon is-large has-text-grey-light">
                            <i class="fas fa-motorcycle fa-3x"></i>
                        </span>
                        <p class="mt-3">No deliveries yet.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card">
                            <div class="columns is-vcentered">
                                <div class="column">
                                    <h3 class="title is-5">Order #<?= $order['id'] ?></h3>
                                    <p class="subtitle is-6">
                                        <span class="icon has-text-grey">
                                            <i class="fas fa-store"></i>
                                        </span>
                                        <?= htmlspecialchars($order['restaurant_name']) ?>
                                    </p>
                                    <p class="is-size-7 has-text-grey">
                                        <span class="icon has-text-grey">
                                            <i class="fas fa-user"></i>
                                        </span>
                                        <?= htmlspecialchars($order['customer_name']) ?>
                                    </p>
                                </div>
                                <div class="column is-narrow">
                                    <span class="tag is-large is-<?= $order['status'] ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </div>
                                <div class="column is-narrow">
                                    <p class="has-text-weight-bold">$<?= number_format($order['total'], 2) ?></p>
                                    <p class="is-size-7 has-text-grey"><?= date('M d, Y', strtotime($order['created_at'])) ?></p>
                                </div>
                                <div class="column is-narrow">
                                    <div class="buttons">
                                        <button class="button is-small is-info">
                                            <span class="icon">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </span>
                                        </button>
                                        <button class="button is-small is-success">
                                            <span class="icon">
                                                <i class="fas fa-check"></i>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Profile Section -->
            <div id="profile" class="dashboard-section" style="display: none;">
                <h2 class="title is-4 mb-4">Delivery Profile</h2>
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
                    <div class="field">
                        <label class="label">Phone Number</label>
                        <div class="control has-icons-left">
                            <input class="input" type="tel" name="phone" placeholder="Enter your phone number">
                            <span class="icon is-small is-left">
                                <i class="fas fa-phone"></i>
                            </span>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Vehicle Type</label>
                        <div class="control has-icons-left">
                            <div class="select is-fullwidth">
                                <select name="vehicle_type">
                                    <option value="motorcycle">Motorcycle</option>
                                    <option value="bicycle">Bicycle</option>
                                    <option value="car">Car</option>
                                </select>
                            </div>
                            <span class="icon is-small is-left">
                                <i class="fas fa-motorcycle"></i>
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
    </script>
</body>
</html> 