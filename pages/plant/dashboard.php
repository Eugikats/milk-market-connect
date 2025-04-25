<?php
// pages/plant/dashboard.php
session_start();

// Check if user is logged in and is a processing plant
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'plant') {
    header('Location: ../login.php?role=plant');
    exit;
}

include_once '../../config/db.php';
$plantId = $_SESSION['user_id'];

// Get plant details
$sql = "SELECT * FROM processing_plants WHERE id = $plantId";
$result = mysqli_query($conn, $sql);
$plant = mysqli_fetch_assoc($result);

// Get available milk listings
$listingsSql = "SELECT pl.*, f.name as farmer_name, f.location as farmer_location 
                FROM price_listings pl 
                JOIN farmers f ON pl.farmer_id = f.id 
                WHERE pl.status = 'Active' AND pl.valid_until >= CURDATE() 
                ORDER BY pl.price_per_liter ASC";
$listingsResult = mysqli_query($conn, $listingsSql);

// Get plant's orders
$ordersSql = "SELECT o.*, f.name as farmer_name, f.location as farmer_location 
              FROM orders o 
              JOIN farmers f ON o.farmer_id = f.id 
              WHERE o.plant_id = $plantId 
              ORDER BY o.created_at DESC";
$ordersResult = mysqli_query($conn, $ordersSql);

// Get plant's payments
$paymentsSql = "SELECT pay.*, o.id as order_id, f.name as farmer_name 
                FROM payments pay 
                JOIN orders o ON pay.order_id = o.id 
                JOIN farmers f ON o.farmer_id = f.id 
                WHERE o.plant_id = $plantId 
                ORDER BY pay.created_at DESC";
$paymentsResult = mysqli_query($conn, $paymentsSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing Plant Dashboard - Milk Market Connect</title>
    <link rel="stylesheet" href="../../css/dashboard.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Processing Plant Dashboard</h1>
            <nav>
                <span>Welcome, <?php echo $_SESSION['user_name']; ?></span>
                <a href="../logout.php">Logout</a>
            </nav>
        </div>
    </header>
    
    <main class="container">
        <div class="tabs">
            <button class="tab-btn active" data-target="profile-tab">Profile</button>
            <button class="tab-btn" data-target="listings-tab">Available Milk</button>
            <button class="tab-btn" data-target="orders-tab">Orders</button>
            <button class="tab-btn" data-target="payments-tab">Payments</button>
        </div>
        
        <section id="profile-tab" class="tab-content active">
            <h2>Your Profile</h2>
            <div class="profile-details">
                <p><strong>Name:</strong> <?php echo $plant['name']; ?></p>
                <p><strong>Email:</strong> <?php echo $plant['email']; ?></p>
                <p><strong>Location:</strong> <?php echo $plant['location']; ?></p>
                <p><strong>Contact:</strong> <?php echo $plant['contact']; ?></p>
                <p><strong>Processing Capacity:</strong> <?php echo $plant['processing_capacity']; ?> liters/day</p>
                <!-- <p><strong>Preferred Price:</strong> UGX<?php echo number_format($plant['preferred_price'], 2); ?> per liter</p> -->
            </div>
            <a href="edit_profile.php" class="btn">Edit Profile</a>
        </section>
        
        <section id="listings-tab" class="tab-content">
            <h2>Available Milk Listings</h2>
            <table>
                <thead>
                    <tr>
                        <th>Farmer</th>
                        <th>Location</th>
                        <th>Price per Liter</th>
                        <th>Valid Until</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($listingsResult) > 0) {
                        while ($listing = mysqli_fetch_assoc($listingsResult)) {
                            echo "<tr>";
                            echo "<td>" . $listing['farmer_name'] . "</td>";
                            echo "<td>" . $listing['farmer_location'] . "</td>";
                            echo "<td>UGX" . number_format($listing['price_per_liter'], 2) . "</td>";
                            echo "<td>" . $listing['valid_until'] . "</td>";
                            echo "<td>
                                    <a href='place_order.php?listing_id=" . $listing['id'] . "' class='btn-small'>Place Order</a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No available milk listings found</td></tr>";
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
                        <th>Farmer</th>
                        <th>Location</th>
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
                            echo "<td>" . $order['farmer_name'] . "</td>";
                            echo "<td>" . $order['farmer_location'] . "</td>";
                            echo "<td>" . $order['quantity'] . " liters</td>";
                            echo "<td>UGX" . number_format($order['price_per_liter'], 2) . "</td>";
                            echo "<td>UGX" . number_format($total, 2) . "</td>";
                            echo "<td>" . $order['status'] . "</td>";
                            echo "<td>" . date('M d, Y', strtotime($order['created_at'])) . "</td>";
                            echo "<td>";
                            if ($order['status'] === 'Approved') {
                                echo "<a href='make_payment.php?order_id=" . $order['id'] . "' class='btn-small'>Make Payment</a>";
                            } else {
                                echo "<span class='status-badge'>" . $order['status'] . "</span>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No orders found</td></tr>";
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
                        <th>Farmer</th>
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
                            echo "<td>" . $payment['farmer_name'] . "</td>";
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
