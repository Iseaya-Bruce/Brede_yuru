<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Admin authentication
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit();
}

// Get order ID
if (!isset($_GET['id'])) {
    header('Location: orders.php');
    exit();
}

$orderId = (int)$_GET['id'];

// Get order details
$stmt = $pdo->prepare("
    SELECT o.*, u.name as customer_name, u.phone as customer_phone 
    FROM orders o JOIN users u ON o.user_id = u.id 
    WHERE o.id = ?
");
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: orders.php');
    exit();
}

// Get order items
$stmt = $pdo->prepare("
    SELECT oi.*, b.name as bread_name, b.price as bread_price
    FROM order_items oi JOIN breads b ON oi.bread_id = b.id
    WHERE oi.order_id = ?
");
$stmt->execute([$orderId]);
$items = $stmt->fetchAll();

// Get ingredients for each item
foreach ($items as &$item) {
    $stmt = $pdo->prepare("
        SELECT i.name, i.price, oii.is_extra
        FROM order_item_ingredients oii
        JOIN ingredients i ON oii.ingredient_id = i.id
        WHERE oii.order_item_id = ?
    ");
    $stmt->execute([$item['id']]);
    $item['ingredients'] = $stmt->fetchAll();
}

$page_title = "Order #$orderId Details";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= $page_title ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="admin-container">
        <div class="order-header">
            <h2>Order #<?= $orderId ?></h2>
            <a href="orders.php" class="btn">Back to Orders</a>
        </div>
        
        <div class="order-info">
            <div class="info-card">
                <h3>Customer Information</h3>
                <p><strong>Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($order['customer_phone']) ?></p>
            </div>
            
            <div class="info-card">
                <h3>Order Information</h3>
                <p><strong>Order Date:</strong> <?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></p>
                <p><strong>Pickup Time:</strong> <?= date('M j, Y g:i A', strtotime($order['pickup_time'])) ?></p>
                <p><strong>Status:</strong> 
                    <span class="status-badge <?= $order['status'] ?>">
                        <?= ucfirst($order['status']) ?>
                    </span>
                </p>
            </div>
        </div>
        
        <div class="order-items">
            <h3>Order Items</h3>
            
            <?php foreach ($items as $item): ?>
                <div class="order-item">
                    <div class="item-header">
                        <h4><?= htmlspecialchars($item['bread_name']) ?> x<?= $item['quantity'] ?></h4>
                        <p>SRD <?= number_format($item['bread_price'] * $item['quantity'], 2) ?></p>
                    </div>
                    
                    <?php if (!empty($item['ingredients'])): ?>
                        <div class="item-ingredients">
                            <h5>Ingredients:</h5>
                            <ul>
                                <?php foreach ($item['ingredients'] as $ingredient): ?>
                                    <li>
                                        <?= htmlspecialchars($ingredient['name']) ?>
                                        <?php if ($ingredient['is_extra']): ?>
                                            <span class="extra-price">
                                                +SRD <?= number_format($ingredient['price'], 2) ?>
                                            </span>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="order-total">
            <p><strong>Total:</strong> SRD <?= number_format($order['total'], 2) ?></p>
        </div>
        
        <div class="order-actions">
            <form method="POST" action="orders.php">
                <input type="hidden" name="order_id" value="<?= $orderId ?>">
                <select name="status">
                    <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="preparing" <?= $order['status'] === 'preparing' ? 'selected' : '' ?>>Preparing</option>
                    <option value="ready" <?= $order['status'] === 'ready' ? 'selected' : '' ?>>Ready</option>
                    <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                </select>
                <button type="submit" name="update_status" class="btn">Update Status</button>
            </form>
            
            <a href="https://wa.me/<?= $order['customer_phone'] ?>" class="btn whatsapp-btn" target="_blank">
                Contact Customer
            </a>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>