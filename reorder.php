<?php
require 'includes/config.php';
require 'includes/functions.php';
redirectIfNotLoggedIn();

$order_item_id = intval($_GET['id'] ?? 0);
$user_id = $_SESSION['user_id'];

// Get order item details
$stmt = $pdo->prepare("
    SELECT bread_id, bread_type_id, quantity
    FROM order_items
    WHERE id = ? AND order_id IN (SELECT id FROM orders WHERE user_id = ?)
");
$stmt->execute([$order_item_id, $user_id]);
$order_item = $stmt->fetch();

if (!$order_item) {
    header("Location: order_history.php?error=notfound");
    exit;
}

// Create or get user's cart
$stmt = $pdo->prepare("SELECT id FROM cart WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart = $stmt->fetch();

if (!$cart) {
    $pdo->prepare("INSERT INTO cart (user_id) VALUES (?)")->execute([$user_id]);
    $cart_id = $pdo->lastInsertId();
} else {
    $cart_id = $cart['id'];
}

// Add to cart
$stmt = $pdo->prepare("
    INSERT INTO cart_items (cart_id, bread_id, bread_type_id, quantity)
    VALUES (?, ?, ?, ?)
");
$stmt->execute([
    $cart_id,
    $order_item['bread_id'],
    $order_item['bread_type_id'],
    $order_item['quantity']
]);
$cart_item_id = $pdo->lastInsertId();

// Add ingredients
$stmt = $pdo->prepare("
    SELECT ingredient_id, is_extra FROM order_item_ingredients WHERE order_item_id = ?
");
$stmt->execute([$order_item_id]);
$ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($ingredients as $ing) {
    $pdo->prepare("
        INSERT INTO cart_item_ingredients (cart_item_id, ingredient_id, is_extra)
        VALUES (?, ?, ?)
    ")->execute([$cart_item_id, $ing['ingredient_id'], $ing['is_extra']]);
}

header("Location: cart.php?reorder=success");
exit;
?>
