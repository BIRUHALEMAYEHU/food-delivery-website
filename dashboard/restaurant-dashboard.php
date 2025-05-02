<?php
    include './middleware.php';

    require_once '../includes/dbh.inc.php';

    $restaurant_id = $_SESSION['user_id'];
    $name = $_SESSION['user_name'];

    // Fetch food items
    $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE restaurant_id = ?");
    $stmt->execute([$restaurant_id]);
    $foods = $stmt->fetchAll();

    // Fetch orders
    $orderStmt = $pdo->prepare("SELECT o.*, u.name as customer_name, u.email as customer_email 
                               FROM orders o 
                               JOIN users u ON o.customer_id = u.id 
                               WHERE o.restaurant_id = ? 
                               ORDER BY o.created_at DESC");
    $orderStmt->execute([$restaurant_id]);
    $orders = $orderStmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Restaurant Dashboard - Sweet Bites</title>
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

        .menu-item-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }

        .menu-item-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
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

        .status-placed { background-color: #ffdd57; color: #363636; }
        .status-ready { background-color: #3298dc; color: white; }
        .status-picked_up { background-color: #ff7b25; color: white; }
        .status-delivered { background-color: #48c774; color: white; }

        .food-image {
            width: 100px;
            height: 100px;
            border-radius: 12px;
            object-fit: cover;
        }

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
                <h1 class="title is-2 has-text-white">Welcome, <?= htmlspecialchars($name) ?>! 👨‍🍳</h1>
                <p class="subtitle is-5 has-text-white">Manage your restaurant and orders</p>
            </div>

            <!-- Quick Stats -->
            <div class="columns mb-6">
                <div class="column is-3">
                    <div class="stats-card">
                        <div class="stats-number"><?= count($orders) ?></div>
                        <div class="stats-label">Total Orders</div>
                    </div>
                </div>
                <div class="column is-3">
                    <div class="stats-card">
                        <div class="stats-number"><?= count($foods) ?></div>
                        <div class="stats-label">Menu Items</div>
                    </div>
                </div>
                <div class="column is-3">
                    <div class="stats-card">
                        <div class="stats-number">4.8</div>
                        <div class="stats-label">Average Rating</div>
                    </div>
                </div>
                <div class="column is-3">
                    <div class="stats-card">
                        <div class="stats-number">98%</div>
                        <div class="stats-label">Order Completion</div>
                    </div>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <div class="tabs is-toggle is-fullwidth is-large mb-6">
                <ul>
                    <li class="is-active"><a href="#profile">Profile</a></li>
                    <li><a href="#menu">Menu</a></li>
                    <li><a href="#orders">Orders</a></li>
                </ul>
            </div>

            <!-- Profile Section -->
            <div id="profile" class="dashboard-section">
                <h2 class="title is-4 mb-4">Restaurant Profile</h2>
                <form action="update_profile.php" method="POST">
                    <div class="field">
                        <label class="label">Restaurant Name</label>
                        <div class="control has-icons-left">
                            <input class="input" type="text" name="name" value="<?= htmlspecialchars($name) ?>" required>
                            <span class="icon is-small is-left">
                                <i class="fas fa-store"></i>
                            </span>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Description</label>
                        <div class="control">
                            <textarea class="textarea" name="description" placeholder="Tell customers about your restaurant..."></textarea>
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

            <!-- Menu Section -->
            <div id="menu" class="dashboard-section" style="display: none;">
                <div class="level mb-4">
                    <div class="level-left">
                        <h2 class="title is-4">Menu Items</h2>
                    </div>
                    <div class="level-right">
                        <button class="button is-primary" onclick="document.getElementById('add-food-modal').classList.add('is-active')">
                            <span class="icon">
                                <i class="fas fa-plus"></i>
                            </span>
                            <span>Add New Item</span>
                        </button>
                    </div>
                </div>

                <?php if (empty($foods)): ?>
                    <div class="has-text-centered py-6">
                        <span class="icon is-large has-text-grey-light">
                            <i class="fas fa-utensils fa-3x"></i>
                        </span>
                        <p class="mt-3">No menu items yet. Add your first item!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($foods as $food): ?>
                        <div class="menu-item-card">
                            <div class="columns is-vcentered">
                                <div class="column is-2">
                                    <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=200&q=80" 
                                         alt="<?= htmlspecialchars($food['name']) ?>" 
                                         class="food-image">
                                </div>
                                <div class="column">
                                    <h3 class="title is-5"><?= htmlspecialchars($food['name']) ?></h3>
                                    <p class="subtitle is-6 has-text-grey"><?= htmlspecialchars($food['description']) ?></p>
                                </div>
                                <div class="column is-2">
                                    <p class="has-text-weight-bold">$<?= number_format($food['price'], 2) ?></p>
                                    <span class="tag is-<?= $food['available'] ? 'success' : 'danger' ?>">
                                        <?= $food['available'] ? 'Available' : 'Unavailable' ?>
                                    </span>
                                </div>
                                <div class="column is-2">
                                    <div class="buttons">
                                        <button class="button is-small is-info">
                                            <span class="icon">
                                                <i class="fas fa-edit"></i>
                                            </span>
                                        </button>
                                        <button class="button is-small is-danger">
                                            <span class="icon">
                                                <i class="fas fa-trash"></i>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Orders Section -->
            <div id="orders" class="dashboard-section" style="display: none;">
                <h2 class="title is-4 mb-4">Recent Orders</h2>
                <?php if (empty($orders)): ?>
                    <div class="has-text-centered py-6">
                        <span class="icon is-large has-text-grey-light">
                            <i class="fas fa-receipt fa-3x"></i>
                        </span>
                        <p class="mt-3">No orders yet.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card">
                            <div class="columns is-vcentered">
                                <div class="column">
                                    <h3 class="title is-5">Order #<?= $order['id'] ?></h3>
                                    <p class="subtitle is-6">
                                        <span class="icon has-text-grey">
                                            <i class="fas fa-user"></i>
                                        </span>
                                        <?= htmlspecialchars($order['customer_name']) ?>
                                    </p>
                                    <p class="is-size-7 has-text-grey">
                                        <span class="icon has-text-grey">
                                            <i class="fas fa-envelope"></i>
                                        </span>
                                        <?= htmlspecialchars($order['customer_email']) ?>
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
                                                <i class="fas fa-eye"></i>
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
