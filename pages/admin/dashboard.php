<?php
// pages/admin/dashboard.php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php?role=admin');
    exit;
}

include_once '../../config/db.php';

// Get system statistics
$statsSql = "SELECT 
    (SELECT COUNT(*) FROM farmers) as total_farmers,
    (SELECT COUNT(*) FROM processing_plants) as total_plants,
    (SELECT COUNT(*) FROM orders) as total_orders,
    (SELECT COUNT(*) FROM price_listings WHERE status = 'Active') as active_listings,
    (SELECT COUNT(*) FROM payments) as total_payments,
    (SELECT SUM(amount) FROM payments WHERE status = 'Completed') as total_revenue";
$statsResult = mysqli_query($conn, $statsSql);
$stats = mysqli_fetch_assoc($statsResult);

// Get all farmers
$farmersSql = "SELECT * FROM farmers ORDER BY created_at DESC";
$farmersResult = mysqli_query($conn, $farmersSql);

// Get all processing plants
$plantsSql = "SELECT * FROM processing_plants ORDER BY created_at DESC";
$plantsResult = mysqli_query($conn, $plantsSql);

// Get recent orders
$ordersSql = "SELECT o.*, f.name as farmer_name, p.name as plant_name 
              FROM orders o 
              JOIN farmers f ON o.farmer_id = f.id 
              JOIN processing_plants p ON o.plant_id = p.id 
              ORDER BY o.created_at DESC LIMIT 10";
$ordersResult = mysqli_query($conn, $ordersSql);

// Get recent payments
$paymentsSql = "SELECT pay.*, o.id as order_id, f.name as farmer_name, p.name as plant_name 
                FROM payments pay 
                JOIN orders o ON pay.order_id = o.id 
                JOIN farmers f ON o.farmer_id = f.id 
                JOIN processing_plants p ON o.plant_id = p.id 
                ORDER BY pay.created_at DESC LIMIT 10";
$paymentsResult = mysqli_query($conn, $paymentsSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Milk Market Connect</title>
    <link rel="stylesheet" href="../../css/dashboard.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Admin Dashboard</h1>
            <nav>
                <span>Welcome, <?php echo $_SESSION['user_name']; ?></span>
                <a href="../logout.php">Logout</a>
            </nav>
        </div>
    </header>
    
    <main class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Farmers</h3>
                <p class="stat-number"><?php echo $stats['total_farmers']; ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Processing Plants</h3>
                <p class="stat-number"><?php echo $stats['total_plants']; ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Orders</h3>
                <p class="stat-number"><?php echo $stats['total_orders']; ?></p>
            </div>
            <div class="stat-card">
                <h3>Active Listings</h3>
                <p class="stat-number"><?php echo $stats['active_listings']; ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Payments</h3>
                <p class="stat-number"><?php echo $stats['total_payments']; ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Revenue</h3>
                <p class="stat-number">UGX<?php echo number_format($stats['total_revenue'] ?? 0, 2); ?></p>
            </div>
        </div>
        
        <div class="tabs">
            <button class="tab-btn active" data-target="farmers-tab">Farmers</button>
            <button class="tab-btn" data-target="plants-tab">Processing Plants</button>
            <button class="tab-btn" data-target="orders-tab">Recent Orders</button>
            <button class="tab-btn" data-target="payments-tab">Recent Payments</button>
        </div>
        
        <section id="farmers-tab" class="tab-content active">
            <div class="section-header">
                <h2>All Farmers</h2>
                <a href="add_farmer.php" class="btn-primary">Add New Farmer</a>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Location</th>
                            <th>Contact</th>
                            <th>Joined Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($farmersResult) > 0) {
                            while ($farmer = mysqli_fetch_assoc($farmersResult)) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($farmer['name']) . "</td>";
                                echo "<td>" . htmlspecialchars($farmer['email']) . "</td>";
                                echo "<td>" . htmlspecialchars($farmer['location']) . "</td>";
                                echo "<td>" . htmlspecialchars($farmer['contact']) . "</td>";
                                echo "<td>" . date('M d, Y', strtotime($farmer['created_at'])) . "</td>";
                                echo "<td class='actions'>
                                        <a href='edit_user.php?type=farmer&id=" . $farmer['id'] . "' class='btn-small btn-edit'>Edit</a>
                                        <a href='view_user.php?type=farmer&id=" . $farmer['id'] . "' class='btn-small btn-view'>View</a>
                                        <button onclick='confirmDelete(\"farmer\", " . $farmer['id'] . ")' class='btn-small btn-delete'>Delete</button>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='no-data'>No farmers found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
        
        <section id="plants-tab" class="tab-content">
            <div class="section-header">
                <h2>All Processing Plants</h2>
                <a href="add_plant.php" class="btn-primary">Add New Plant</a>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Location</th>
                            <th>Contact</th>
                            <th>Capacity</th>
                            <th>Joined Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($plantsResult) > 0) {
                            while ($plant = mysqli_fetch_assoc($plantsResult)) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($plant['name']) . "</td>";
                                echo "<td>" . htmlspecialchars($plant['email']) . "</td>";
                                echo "<td>" . htmlspecialchars($plant['location']) . "</td>";
                                echo "<td>" . htmlspecialchars($plant['contact']) . "</td>";
                                echo "<td>" . htmlspecialchars($plant['processing_capacity']) . " liters/day</td>";
                                echo "<td>" . date('M d, Y', strtotime($plant['created_at'])) . "</td>";
                                echo "<td class='actions'>
                                        <a href='edit_user.php?type=plant&id=" . $plant['id'] . "' class='btn-small btn-edit'>Edit</a>
                                        <a href='view_user.php?type=plant&id=" . $plant['id'] . "' class='btn-small btn-view'>View</a>
                                        <button onclick='confirmDelete(\"plant\", " . $plant['id'] . ")' class='btn-small btn-delete'>Delete</button>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' class='no-data'>No processing plants found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
        
        <section id="orders-tab" class="tab-content">
            <h2>Recent Orders</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Farmer</th>
                        <th>Plant</th>
                        <th>Quantity</th>
                        <th>Price/Liter</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($ordersResult) > 0) {
                        while ($order = mysqli_fetch_assoc($ordersResult)) {
                            $total = $order['quantity'] * $order['price_per_liter'];
                            echo "<tr>";
                            echo "<td>" . $order['id'] . "</td>";
                            echo "<td>" . $order['farmer_name'] . "</td>";
                            echo "<td>" . $order['plant_name'] . "</td>";
                            echo "<td>" . $order['quantity'] . " liters</td>";
                            echo "<td>UGX" . number_format($order['price_per_liter'], 2) . "</td>";
                            echo "<td>UGX" . number_format($total, 2) . "</td>";
                            echo "<td>" . $order['status'] . "</td>";
                            echo "<td>" . date('M d, Y', strtotime($order['created_at'])) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No recent orders found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
        
        <section id="payments-tab" class="tab-content">
            <h2>Recent Payments</h2>
            <table>
                <thead>
                    <tr>
                        <th>Payment ID</th>
                        <th>Order ID</th>
                        <th>Farmer</th>
                        <th>Plant</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($paymentsResult) > 0) {
                        while ($payment = mysqli_fetch_assoc($paymentsResult)) {
                            echo "<tr>";
                            echo "<td>" . $payment['id'] . "</td>";
                            echo "<td>" . $payment['order_id'] . "</td>";
                            echo "<td>" . $payment['farmer_name'] . "</td>";
                            echo "<td>" . $payment['plant_name'] . "</td>";
                            echo "<td>UGX" . number_format($payment['amount'], 2) . "</td>";
                            echo "<td>" . $payment['payment_method'] . "</td>";
                            echo "<td>" . $payment['status'] . "</td>";
                            echo "<td>" . date('M d, Y', strtotime($payment['created_at'])) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No recent payments found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </main>
    
    <footer>
        <div class="container">
            <p>&copy; 2025 Milk Market Connect</p>
        </div>
    </footer>
    
    <script>
        // Tab functionality
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                // Remove active class from all buttons and contents
                tabBtns.forEach(b => b.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                
                // Add active class to clicked button and target content
                btn.classList.add('active');
                const targetId = btn.dataset.target;
                document.getElementById(targetId).classList.add('active');
            });
        });

        // Delete confirmation function
        function confirmDelete(type, id) {
            if (confirm(`Are you sure you want to delete this ${type}?`)) {
                window.location.href = `delete_user.php?type=${type}&id=${id}`;
            }
        }
    </script>

    <style>
        /* Add these styles to your dashboard.css file */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }

        .table-responsive {
            overflow-x: auto;
            margin-bottom: 20px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .data-table th,
        .data-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .data-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .data-table tbody tr:hover {
            background-color: #f5f5f5;
        }

        .actions {
            white-space: nowrap;
        }

        .btn-small {
            padding: 5px 10px;
            margin: 0 2px;
            border-radius: 3px;
            font-size: 0.9em;
            text-decoration: none;
            cursor: pointer;
            border: none;
        }

        .btn-edit {
            background-color: #ffc107;
            color: #000;
        }

        .btn-view {
            background-color: #17a2b8;
            color: white;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
        }

        .no-data {
            text-align: center;
            color: #666;
            padding: 20px;
        }
    </style>
</body>
</html>
