<?php
// pages/farmer/dashboard.php
session_start();

// Check if user is logged in and is a farmer
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'farmer') {
    header('Location: ../login.php?role=farmer');
    exit;
}

include_once '../../config/db.php';
$farmerId = $_SESSION['user_id'];

// Get farmer details
$sql = "SELECT * FROM farmers WHERE id = $farmerId";
$result = mysqli_query($conn, $sql);
$farmer = mysqli_fetch_assoc($result);

// Get farmer's milk listings
$listingsSql = "SELECT * FROM price_listings WHERE farmer_id = $farmerId ORDER BY valid_until DESC";
$listingsResult = mysqli_query($conn, $listingsSql);

// Get farmer's orders
$ordersSql = "SELECT o.*, p.name as plant_name FROM orders o 
              JOIN processing_plants p ON o.plant_id = p.id 
              WHERE o.farmer_id = $farmerId 
              ORDER BY o.created_at DESC";
$ordersResult = mysqli_query($conn, $ordersSql);

// Get farmer's payments
$paymentsSql = "SELECT pay.*, o.id as order_id, p.name as plant_name 
                FROM payments pay 
                JOIN orders o ON pay.order_id = o.id 
                JOIN processing_plants p ON o.plant_id = p.id 
                WHERE o.farmer_id = $farmerId 
                ORDER BY pay.created_at DESC";
$paymentsResult = mysqli_query($conn, $paymentsSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard - Milk Market Connect</title>
    <link rel="stylesheet" href="../../css/dashboard.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Farmer Dashboard</h1>
            <nav>
                <span>Welcome, <?php echo $_SESSION['user_name']; ?></span>
                <a href="../logout.php">Logout</a>
            </nav>
        </div>
    </header>
    
    <main class="container">
        <div class="tabs">
            <button class="tab-btn active" data-target="profile-tab">Profile</button>
            <button class="tab-btn" data-target="listings-tab">My Listings</button>
            <button class="tab-btn" data-target="orders-tab">Orders</button>
            <button class="tab-btn" data-target="payments-tab">Payments</button>
        </div>
        
        <section id="profile-tab" class="tab-content active">
            <h2>Your Profile</h2>
            <div class="profile-details">
                <p><strong>Name:</strong> <?php echo $farmer['name']; ?></p>
                <p><strong>Email:</strong> <?php echo $farmer['email']; ?></p>
                <p><strong>Location:</strong> <?php echo $farmer['location']; ?></p>
                <p><strong>Contact:</strong> <?php echo $farmer['contact']; ?></p>
                <p><strong>Milk Capacity:</strong> <?php echo $farmer['milk_capacity']; ?> liters/day</p>
                <p><strong>Preferred Price:</strong> UGX<?php echo number_format($farmer['preferred_price'], 2); ?> per liter</p>
            </div>
            <a href="edit_profile.php" class="btn">Edit Profile</a>
        </section>
        
        <section id="listings-tab" class="tab-content">
            <h2>Your Milk Listings</h2>
            <a href="add_listing.php" class="btn">Add New Listing</a>
            
            <table>
                <thead>
                    <tr>
                        <th>Price per Liter</th>
                        <th>Valid Until</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($listingsResult) > 0) {
                        while ($listing = mysqli_fetch_assoc($listingsResult)) {
                            echo "<tr>";
                            echo "<td>UGX" . number_format($listing['price_per_liter'], 2) . "</td>";
                            echo "<td>" . $listing['valid_until'] . "</td>";
                            echo "<td>" . $listing['status'] . "</td>";
                            echo "<td>" . date('M d, Y', strtotime($listing['created_at'])) . "</td>";
                            echo "<td>
                                    <a href='edit_listing.php?id=" . $listing['id'] . "' class='btn-small'>Edit</a>
                                    <a href='delete_listing.php?id=" . $listing['id'] . "' class='btn-small btn-danger' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No listings found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
        
        <section id="orders-tab" class="tab-content">
            <h2>Your Orders</h2>
            <table>
                <thead>
                    <tr>
                        <th>Plant</th>
                        <th>Quantity</th>
                        <th>Price per Liter</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($ordersResult) > 0) {
                        while ($order = mysqli_fetch_assoc($ordersResult)) {
                            $total = $order['quantity'] * $order['price_per_liter'];
                            echo "<tr>";
                            echo "<td>" . $order['plant_name'] . "</td>";
                            echo "<td>" . $order['quantity'] . " liters</td>";
                            echo "<td>UGX" . number_format($order['price_per_liter'], 2) . "</td>";
                            echo "<td>UGX" . number_format($total, 2) . "</td>";
                            echo "<td>" . $order['status'] . "</td>";
                            echo "<td>" . date('M d, Y', strtotime($order['created_at'])) . "</td>";
                            echo "<td>";
                            if ($order['status'] === 'Pending') {
                                echo "<a href='update_order.php?id=" . $order['id'] . "&action=approve' class='btn-small'>Approve</a> ";
                                echo "<a href='update_order.php?id=" . $order['id'] . "&action=reject' class='btn-small btn-danger'>Reject</a>";
                            } else {
                                echo "<span class='status-badge'>" . $order['status'] . "</span>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No orders found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
        
        <section id="payments-tab" class="tab-content">
            <h2>Your Payments</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Plant</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($paymentsResult) > 0) {
                        while ($payment = mysqli_fetch_assoc($paymentsResult)) {
                            echo "<tr>";
                            echo "<td>" . $payment['order_id'] . "</td>";
                            echo "<td>" . $payment['plant_name'] . "</td>";
                            echo "<td>UGX" . number_format($payment['amount'], 2) . "</td>";
                            echo "<td>" . $payment['payment_method'] . "</td>";
                            echo "<td>" . $payment['status'] . "</td>";
                            echo "<td>" . date('M d, Y', strtotime($payment['created_at'])) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No payments found</td></tr>";
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
    </script>
</body>
</html>
