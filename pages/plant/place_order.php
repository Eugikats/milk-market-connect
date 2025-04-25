<?php
// pages/plant/place_order.php
session_start();

// Check if user is logged in and is a processing plant
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'plant') {
    header('Location: ../login.php?role=plant');
    exit;
}

include_once '../../config/db.php';
$plantId = $_SESSION['user_id'];
$listingId = $_GET['listing_id'] ?? 0;

// Get listing details
$sql = "SELECT pl.*, f.name as farmer_name, f.id as farmer_id 
        FROM price_listings pl 
        JOIN farmers f ON pl.farmer_id = f.id 
        WHERE pl.id = $listingId AND pl.status = 'Active' AND pl.valid_until >= CURDATE()";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    header('Location: dashboard.php?error=Listing not found or expired');
    exit;
}

$listing = mysqli_fetch_assoc($result);

// Process order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantity = $_POST['quantity'] ?? 0;
    $farmerId = $_POST['farmer_id'] ?? 0;
    $pricePerLiter = $_POST['price_per_liter'] ?? 0;
    
    if ($quantity > 0 && $farmerId && $pricePerLiter > 0) {
        $sql = "INSERT INTO orders (farmer_id, plant_id, quantity, price_per_liter, status) 
                VALUES ('$farmerId', '$plantId', '$quantity', '$pricePerLiter', 'Pending')";
        
        if (mysqli_query($conn, $sql)) {
            header('Location: dashboard.php?success=1');
            exit;
        } else {
            header('Location: place_order.php?listing_id=' . $listingId . '&error=' . urlencode(mysqli_error($conn)));
            exit;
        }
    } else {
        header('Location: place_order.php?listing_id=' . $listingId . '&error=Invalid input');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Order - Milk Market Connect</title>
    <link rel="stylesheet" href="../../css/dashboard.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Place Order</h1>
            <nav>
                <a href="dashboard.php">Back to Dashboard</a>
                <a href="../logout.php">Logout</a>
            </nav>
        </div>
    </header>
    
    <main class="container">
        <section class="order-form-section">
            <h2>Place Order from <?php echo $listing['farmer_name']; ?></h2>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message"><?php echo $_GET['error']; ?></div>
            <?php endif; ?>
            
            <div class="listing-details">
                <p><strong>Farmer:</strong> <?php echo $listing['farmer_name']; ?></p>
                <p><strong>Price per Liter:</strong> UGX<?php echo number_format($listing['price_per_liter'], 2); ?></p>
                <p><strong>Valid Until:</strong> <?php echo $listing['valid_until']; ?></p>
            </div>
            
            <form method="post">
                <div class="form-group">
                    <label for="quantity">Quantity (liters)</label>
                    <input type="number" id="quantity" name="quantity" min="1" required>
                </div>
                
                <input type="hidden" name="farmer_id" value="<?php echo $listing['farmer_id']; ?>">
                <input type="hidden" name="price_per_liter" value="<?php echo $listing['price_per_liter']; ?>">
                
                <div class="form-group">
                    <label>Total Cost: UGX<span id="total-cost">0.00</span></label>
                </div>
                
                <button type="submit" class="btn">Place Order</button>
            </form>
        </section>
    </main>
    
    <footer>
        <div class="container">
            <p>&copy; 2025 Milk Market Connect</p>
        </div>
    </footer>
    
    <script>
        const quantityInput = document.getElementById('quantity');
        const totalCostSpan = document.getElementById('total-cost');
        const pricePerLiter = <?php echo $listing['price_per_liter']; ?>;
        
        quantityInput.addEventListener('input', updateTotalCost);
        
        function updateTotalCost() {
            const quantity = parseFloat(quantityInput.value) || 0;
            const totalCost = quantity * pricePerLiter;
            totalCostSpan.textContent = totalCost.toFixed(2);
        }
    </script>
</body>
</html>
