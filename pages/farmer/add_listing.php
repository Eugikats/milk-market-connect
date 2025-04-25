<?php
// pages/farmer/add_listing.php
session_start();

// Check if user is logged in and is a farmer
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'farmer') {
    header('Location: ../login.php?role=farmer');
    exit;
}

include_once '../../config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $farmerId = $_SESSION['user_id'];
    $price = $_POST['price'] ?? 0;
    $validUntil = $_POST['valid_until'] ?? '';
    
    if ($price > 0 && $validUntil) {
        $sql = "INSERT INTO price_listings (farmer_id, price_per_liter, valid_until, status) 
                VALUES ('$farmerId', '$price', '$validUntil', 'Active')";
        
        if (mysqli_query($conn, $sql)) {
            $success = "Listing added successfully!";
        } else {
            $error = "Error adding listing: " . mysqli_error($conn);
        }
    } else {
        $error = "Please provide valid price and expiration date.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Listing - Milk Market Connect</title>
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

        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
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
            <h1>Add New Listing</h1>
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

            <form method="POST">
                <div class="form-group">
                    <label>Price per Liter ($):</label>
                    <input type="number" name="price" step="0.01" min="0.01" required>
                </div>
                
                <div class="form-group">
                    <label>Valid Until:</label>
                    <input type="date" name="valid_until" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                
                <div class="button-group">
                    <button type="submit" class="btn-primary">Add Listing</button>
                    <a href="dashboard.php" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
