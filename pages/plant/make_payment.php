<?php
session_start();

// Check if user is logged in and is a processing plant
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'plant') {
    header('Location: ../login.php?role=plant');
    exit;
}

include_once '../../config/db.php';

$plantId = $_SESSION['user_id'];
$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$error = '';
$success = '';

// Fetch order details and validate it belongs to this plant and is approved
$sql = "SELECT o.*, f.name as farmer_name, f.location as farmer_location 
        FROM orders o 
        JOIN farmers f ON o.farmer_id = f.id 
        WHERE o.id = $orderId 
        AND o.plant_id = $plantId 
        AND o.status = 'Approved'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) === 0) {
    header('Location: dashboard.php?error=Order not found or cannot be paid');
    exit;
}

$order = mysqli_fetch_assoc($result);
$totalAmount = $order['quantity'] * $order['price_per_liter'];

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentMethod = mysqli_real_escape_string($conn, $_POST['payment_method'] ?? '');
    
    if (!empty($paymentMethod)) {
        // Start transaction
        mysqli_begin_transaction($conn);
        
        try {
            // Insert payment record
            $paymentSql = "INSERT INTO payments (order_id, amount, payment_method, status) 
                          VALUES ($orderId, $totalAmount, '$paymentMethod', 'Successful')";
            mysqli_query($conn, $paymentSql);
            
            // Update order status
            $updateOrderSql = "UPDATE orders SET status = 'Completed' WHERE id = $orderId";
            mysqli_query($conn, $updateOrderSql);
            
            // Commit transaction
            mysqli_commit($conn);
            $success = "Payment processed successfully!";
            
        } catch (Exception $e) {
            // Rollback transaction on error
            mysqli_rollback($conn);
            $error = "Error processing payment: " . $e->getMessage();
        }
    } else {
        $error = "Please select a payment method.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make Payment - Milk Market Connect</title>
    <link rel="stylesheet" href="../../css/dashboard.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .order-details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .order-details p {
            margin: 5px 0;
        }

        .payment-methods {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .payment-method {
            flex: 1;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
        }

        .payment-method:hover {
            background: #f8f9fa;
        }

        .payment-method input[type="radio"] {
            margin-right: 5px;
        }

        .error-message {
            color: #dc3545;
            margin-bottom: 15px;
        }

        .success-message {
            color: #28a745;
            margin-bottom: 15px;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Make Payment</h1>
            <nav>
                <a href="dashboard.php">Back to Dashboard</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="form-container">
            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="order-details">
                <h2>Order Details</h2>
                <p><strong>Order ID:</strong> <?php echo $order['id']; ?></p>
                <p><strong>Farmer:</strong> <?php echo $order['farmer_name']; ?></p>
                <p><strong>Location:</strong> <?php echo $order['farmer_location']; ?></p>
                <p><strong>Quantity:</strong> <?php echo $order['quantity']; ?> liters</p>
                <p><strong>Price per Liter:</strong> UGX<?php echo number_format($order['price_per_liter'], 2); ?></p>
                <p><strong>Total Amount:</strong> UGX<?php echo number_format($totalAmount, 2); ?></p>
            </div>

            <form method="POST">
                <h3>Select Payment Method</h3>
                <div class="payment-methods">
                    <label class="payment-method">
                        <input type="radio" name="payment_method" value="Mobile Money" required>
                        Mobile Money
                    </label>
                    <label class="payment-method">
                        <input type="radio" name="payment_method" value="Bank Transfer" required>
                        Bank Transfer
                    </label>
                    <label class="payment-method">
                        <input type="radio" name="payment_method" value="Cash" required>
                        Cash
                    </label>
                </div>
                
                <div class="button-group">
                    <button type="submit" class="btn-primary">Process Payment</button>
                    <a href="dashboard.php" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html> 