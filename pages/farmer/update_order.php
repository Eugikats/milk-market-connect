<?php
session_start();

// Check if user is logged in and is a farmer
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'farmer') {
    header('Location: ../login.php?role=farmer');
    exit;
}

include_once '../../config/db.php';

$farmerId = $_SESSION['user_id'];
$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Validate the order belongs to this farmer and is in Pending status
$sql = "SELECT * FROM orders WHERE id = $orderId AND farmer_id = $farmerId AND status = 'Pending'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $newStatus = '';
    
    switch ($action) {
        case 'approve':
            $newStatus = 'Approved';
            break;
        case 'reject':
            $newStatus = 'Cancelled';
            break;
        default:
            header('Location: dashboard.php?error=Invalid action');
            exit;
    }
    
    // Update the order status
    $updateSql = "UPDATE orders SET status = '$newStatus' WHERE id = $orderId AND farmer_id = $farmerId";
    
    if (mysqli_query($conn, $updateSql)) {
        header('Location: dashboard.php?success=Order ' . strtolower($newStatus) . ' successfully');
    } else {
        header('Location: dashboard.php?error=' . urlencode('Error updating order: ' . mysqli_error($conn)));
    }
} else {
    header('Location: dashboard.php?error=Order not found or cannot be modified');
}
exit;
?> 