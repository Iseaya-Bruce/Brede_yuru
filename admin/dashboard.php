<?php
require '../includes/config.php';
require '../includes/functions.php';
require '../includes/auth.php'; // <-- loads redirectIfNotAdmin()
redirectIfNotAdmin();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Admin Dashboard - Brede Yuru</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(90deg, #73f879ff, rgba(78, 75, 250, 1));
            padding: 20px;
            color: #343a40;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h2 {
            color: #ffc107;
            font-size: 2.4em;
        }
        .dashboard-container {
            max-width: 1000px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }
        .dashboard-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            text-align: center;
            padding: 25px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.2);
        }
        .dashboard-card i {
            font-size: 2.5em;
            color: #ffc107;
            margin-bottom: 15px;
        }
        .dashboard-card a {
            display: block;
            color: #495057;
            font-size: 1.2em;
            font-weight: 500;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .dashboard-card a:hover {
            color: #ffc107;
        }
        .logout-btn {
            display: inline-block;
            margin-top: 30px;
            padding: 10px 20px;
            background: linear-gradient(135deg, #ff6f61, #ffc107);
            color: white;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s ease;
        }
        .logout-btn:hover {
            background: linear-gradient(135deg, #ffc107, #ff6f61);
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <div class="header">
        <h2>Brede Yuru Admin Dashboard</h2>
    </div>

    <div class="dashboard-container">
        <div class="dashboard-card">
            <i class="fas fa-bread-slice"></i>
            <a href="manage_breads.php">Manage Breads</a>
        </div>
        <div class="dashboard-card">
            <i class="fas fa-layer-group"></i>
            <a href="manage_bread_type.php">Manage Bread Types</a>
        </div>
        <div class="dashboard-card">
            <i class="fas fa-carrot"></i>
            <a href="manage_ingredients.php">Manage Standard Ingredients</a>
        </div>
        <div class="dashboard-card">
            <i class="fas fa-plus-circle"></i>
            <a href="manage_extras.php">Manage Extra Ingredients</a>
        </div>
        <div class="dashboard-card">
            <i class="fas fa-clipboard-list"></i>
            <a href="orders.php">View Orders</a>
        </div>
    </div>

    <div style="text-align: center;">
        <a class="logout-btn" href="../logout.php">🚪 Logout</a>
    </div>
</body>
</html>
