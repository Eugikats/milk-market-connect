<?php
session_start();
include_once '../../config/db.php';

// Check if user is logged in and is a farmer
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'farmer') {
    header('Location: ../login.php?role=farmer');
    exit;
}

$farmerId = $_SESSION['user_id'];
$error = '';
$success = '';

// Fetch existing farmer data
$sql = "SELECT * FROM farmers WHERE id = $farmerId";
$result = mysqli_query($conn, $sql);
$farmer = mysqli_fetch_assoc($result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $milk_capacity = mysqli_real_escape_string($conn, $_POST['milk_capacity']);
    $preferred_price = mysqli_real_escape_string($conn, $_POST['preferred_price']);
    
    $sql = "UPDATE farmers SET 
            name = '$name',
            email = '$email',
            location = '$location',
            contact = '$contact',
            milk_capacity = '$milk_capacity',
            preferred_price = '$preferred_price'";
    
    // Only update password if a new one is provided
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql .= ", password = '$password'";
    }
    
    $sql .= " WHERE id = $farmerId";
    
    if (mysqli_query($conn, $sql)) {
        $success = "Profile updated successfully!";
        // Refresh farmer data
        $result = mysqli_query($conn, "SELECT * FROM farmers WHERE id = $farmerId");
        $farmer = mysqli_fetch_assoc($result);
    } else {
        $error = "Error updating profile: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Milk Market Connect</title>
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
            <h1>Edit Profile</h1>
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
                    <label>Name:</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($farmer['name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($farmer['email']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Location:</label>
                    <input type="text" name="location" value="<?php echo htmlspecialchars($farmer['location']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Contact:</label>
                    <input type="text" name="contact" value="<?php echo htmlspecialchars($farmer['contact']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Milk Capacity (liters/day):</label>
                    <input type="number" name="milk_capacity" value="<?php echo htmlspecialchars($farmer['milk_capacity']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Preferred Price (per liter):</label>
                    <input type="number" name="preferred_price" step="0.01" value="<?php echo htmlspecialchars($farmer['preferred_price']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>New Password (leave blank to keep current):</label>
                    <input type="password" name="password">
                </div>
                
                <div class="button-group">
                    <button type="submit" class="btn-primary">Save Changes</button>
                    <a href="dashboard.php" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html> 