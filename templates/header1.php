<?php
// Get the current page name for active menu highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Milk Market Connect</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/png" href="images/milk-icon.png">
    <style>
        /* Header styles */
        .header {
            background-color: #333333;
            padding: 1rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            color: white;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .logo span {
            color: #3498db;
        }

        /* Navigation styles */
        .nav {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }

        .nav-link {
            color: #ecf0f1;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .nav-link:hover {
            background-color: #34495e;
        }

        .nav-link.active {
            background-color: #3498db;
        }

        .auth-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .btn-login {
            background-color: #3498db;
            color: white;
        }

        .btn-signup {
            background-color: 4fa752;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .nav {
                flex-direction: column;
                gap: 0.5rem;
            }

            .auth-buttons {
                flex-direction: column;
                width: 100%;
            }

            .btn {
                display: block;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="/index.php" class="logo">Milk<span>Market</span></a>
                
                <nav class="nav">
                    <a href="/index.php" class="nav-link <?php echo $current_page === 'index.php' ? 'active' : ''; ?>">Home</a>
                    <a href="/pages/listings.php" class="nav-link <?php echo $current_page === 'listings.php' ? 'active' : ''; ?>">Milk Listings</a>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($_SESSION['user_role'] === 'farmer'): ?>
                            <a href="/pages/farmer/dashboard.php" class="nav-link">Dashboard</a>
                        <?php elseif ($_SESSION['user_role'] === 'plant'): ?>
                            <a href="/pages/plant/dashboard.php" class="nav-link">Dashboard</a>
                        <?php elseif ($_SESSION['user_role'] === 'admin'): ?>
                            <a href="/pages/admin/dashboard.php" class="nav-link">Dashboard</a>
                        <?php endif; ?>
                        <a href="/pages/logout.php" class="nav-link">Logout</a>
                    <?php else: ?>
                        <div class="auth-buttons">
                            <a href="/pages/login.php" class="btn btn-login">Login</a>
                            <a href="/pages/signup.php" class="btn btn-signup">Sign Up</a>
                        </div>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>
</body>
</html>
