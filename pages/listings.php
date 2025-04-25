<?php
// pages/listings.php
include_once '../config/db.php';

// Get all active listings
$sql = "SELECT pl.id, f.name AS farmer_name, f.location, pl.price_per_liter, pl.valid_until 
        FROM price_listings pl 
        JOIN farmers f ON pl.farmer_id = f.id 
        WHERE pl.status = 'Active' AND pl.valid_until >= CURDATE()
        ORDER BY pl.price_per_liter ASC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Milk Listings - Milk Market Connect</title>
    <link rel="stylesheet" href="../css/listings.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Available Milk Listings</h1>
            <nav>
                <a href="../index.php">Home</a>
                <a href="login.php">Login</a>
            </nav>
        </div>
    </header>
    
    <main class="container">
        <table class="listings-table">
            <thead>
                <tr>
                    <th>Farmer</th>
                    <th>Location</th>
                    <th>Price (per liter)</th>
                    <th>Valid Until</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['farmer_name'] . "</td>";
                        echo "<td>" . $row['location'] . "</td>";
                        echo "<td>UGX" . number_format($row['price_per_liter'], 2) . "</td>";
                        echo "<td>" . $row['valid_until'] . "</td>";
                        echo "<td><a href='login.php?role=plant' class='btn-order'>Place Order</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No listings available</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </main>
    
    <footer>
        <div class="container">
            <p>&copy; 2025 Milk Market Connect</p>
        </div>
    </footer>
</body>
</html>