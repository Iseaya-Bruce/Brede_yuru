<?php
require '../includes/config.php';
require '../includes/functions.php';
require '../includes/auth.php'; // <-- loads redirectIfNotAdmin()
redirectIfNotAdmin();

if (!$_SESSION['is_admin']) {
    echo "Access denied.";
    exit;
}

// Fetch orders
$orders = $pdo->query("
    SELECT o.*, u.name, u.phone
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

$order_items_stmt = $pdo->prepare("
    SELECT oi.id as order_item_id, b.name as bread_name, bt.name as bread_type_name, oi.quantity
    FROM order_items oi
    JOIN breads b ON oi.bread_id = b.id
    LEFT JOIN bread_types bt ON oi.bread_type_id = bt.id
    WHERE oi.order_id = ?
");

$ingredient_stmt = $pdo->prepare("
    SELECT i.name, oii.is_extra
    FROM order_item_ingredients oii
    JOIN ingredients i ON oii.ingredient_id = i.id
    WHERE oii.order_item_id = ?
");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?")->execute([$new_status, $order_id]);
    header("Location: orders.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Admin - All Orders</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(90deg, #73f879ff, rgba(78, 75, 250, 1));
            padding: 20px;
            color: #343a40;
        }
        h2 {
            text-align: center;
            color: #ffc107;
            margin-bottom: 30px;
            font-size: 2.4em;
        }
        .floating-btn {
            position: fixed;
            bottom: auto;
            top: 20px;
            right: auto;
            left: 20px;
            width: 50px;
            height: 50px;
            background: #ff6f61;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3em; /* icon size */
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.25);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .floating-btn:hover {
            background: #e85d4f;
            transform: translateY(-3px) scale(1.05);
        }
        .order-box {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            margin-bottom: 25px;
            padding: 20px;
            animation: fadeIn 0.8s ease-out;
        }
        .order-box strong {
            color: #495057;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 0.9em;
            font-weight: 500;
        }
        .status-pending { background: #ffc107; color: #212529; }
        .status-preparing { background: #fd7e14; color: #fff; }
        .status-ready { background: #28a745; color: #fff; }
        .status-completed { background: #6c757d; color: #fff; }

        .order-items {
            margin-top: 15px;
        }
        .order-items li {
            margin-bottom: 8px;
            color: #495057;
        }
        .order-items li strong {
            color: #ffc107;
        }
        .ingredient-list {
            list-style: none;
            margin-left: 15px;
            color: #6c757d;
        }
        .status-form {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }
        .status-form select {
            padding: 8px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        .status-form button {
            background: linear-gradient(135deg, #2b2d42, #4b5d67);
            color: #fff;
            border: none;
            border-radius: 25px;
            padding: 8px 15px;
            font-weight: 500;
            cursor: pointer;
            transition: 0.3s ease;
        }
        .status-form button:hover {
            background: linear-gradient(135deg, #4b5d67, #2b2d42);

        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
<a href="dashboard.php" class="floating-btn" title="Back to Dashboard">
        <i class="fas fa-arrow-left"></i>
</a>
<h2>All Orders</h2>

<?php foreach ($orders as $order): ?>
    <div class="order-box">
        <p><strong>Order #<?= $order['id'] ?></strong></p>
        <p><strong>Customer:</strong> <?= htmlspecialchars($order['name']) ?> (<?= $order['phone'] ?>)</p>
        <p><strong>Pickup:</strong> <?= date("d M Y - H:i", strtotime($order['pickup_time'])) ?></p>
        <p><strong>Status:</strong>
            <span class="status-badge status-<?= htmlspecialchars($order['status']) ?>">
                <?= ucfirst($order['status']) ?>
            </span>
        </p>
        <p><strong>Total:</strong> SRD <?= number_format($order['total_price'], 2) ?></p>

        <ul class="order-items">
            <?php
            $order_items_stmt->execute([$order['id']]);
            $items = $order_items_stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($items as $item) {
                echo "<li><strong>{$item['quantity']} × {$item['bread_name']}</strong>";
                if (!empty($item['bread_type_name'])) {
                    echo " <em style='color:#888;'>[{$item['bread_type_name']}]</em>";
                }
                echo "<ul class='ingredient-list'>";
                $ingredient_stmt->execute([$item['order_item_id']]);
                foreach ($ingredient_stmt->fetchAll(PDO::FETCH_ASSOC) as $ing) {
                    echo "<li>{$ing['name']}" . ($ing['is_extra'] ? " (extra)" : "") . "</li>";
                }
                echo "</ul></li>";
            }
            ?>
        </ul>

        <form method="POST" class="status-form">
            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
            <select name="status">
                <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="preparing" <?= $order['status'] === 'preparing' ? 'selected' : '' ?>>Preparing</option>
                <option value="ready" <?= $order['status'] === 'ready' ? 'selected' : '' ?>>Ready</option>
                <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
            </select>
            <button type="submit">Update</button>
        </form>
    </div>
<?php endforeach; ?>

</body>
</html>
