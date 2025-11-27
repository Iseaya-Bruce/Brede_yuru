<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'config.php';

// Get cart count
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT SUM(quantity) FROM cart_items WHERE cart_id = (SELECT id FROM cart WHERE user_id = ?)");
    $stmt->execute([$_SESSION['user_id']]);
    $cart_count = $stmt->fetchColumn() ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Brede Yuru'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Header Styling */
        header {
            background: linear-gradient(135deg, #ff6f61, #ffc107);
            color: #fff;
            padding: 15px 25px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 999;
        }
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }
        .logo a {
            color: #fff;
            font-size: 1.8rem;
            font-weight: 700;
            text-decoration: none;
        }
        .logo a::after {
            content: ' 🥪';
            font-size: 1.4rem;
        }
        .nav-links {
            display: flex;
            gap: 20px;
            list-style: none;
        }
        .nav-links li a {
            color: #fff;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 500;
            padding: 8px 15px;
            border-radius: 20px;
            transition: background 0.3s ease;
        }
        .nav-links li a:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        .burger {
            display: none;
            cursor: pointer;
            font-size: 1.5rem;
            color: #fff;
        }

        /* Floating Cart */
        .cart-icon {
            position: relative;
            margin-left: 15px;
            font-size: 1.4rem;
        }
        .cart-count {
            position: absolute;
            top: -8px;
            right: -10px;
            background: #28a745;
            color: #fff;
            font-size: 0.75rem;
            padding: 2px 6px;
            border-radius: 50%;
            font-weight: bold;
        }

        /* Mobile */
        @media (max-width: 768px) {
            .nav-links {
                position: fixed;
                right: -100%;
                top: 60px;
                background: #ff6f61;
                flex-direction: column;
                gap: 15px;
                width: 200px;
                padding: 20px;
                border-radius: 10px 0 0 10px;
                transition: right 0.3s ease;
            }
            .nav-links.active {
                right: 0;
            }
            .burger {
                display: block;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <a href="index.php">Brede Yuru</a>
            </div>
            <ul class="nav-links">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="order_history.php">Orders</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="cart.php" class="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <?php if ($cart_count > 0): ?>
                        <span class="cart-count"><?= $cart_count ?></span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
            <div class="burger" onclick="toggleMenu()">
                <i class="fas fa-bars"></i>
            </div>
        </nav>
    </header>
    <main>

<script>
function toggleMenu() {
    document.querySelector('.nav-links').classList.toggle('active');
}
</script>
