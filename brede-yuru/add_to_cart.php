<?php
require 'includes/config.php';
require 'includes/functions.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$bread_id = $data['bread_id'];
$bread_type_id = $data['bread_type_id']; // ✅ New: get selected bread type
$extras = $data['extras'] ?? [];
$quantity = $data['quantity'] ?? 1;

$user_id = $_SESSION['user_id'];

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

// ✅ Add cart item with bread_type_id
$pdo->prepare("INSERT INTO cart_items (cart_id, bread_id, bread_type_id, quantity) VALUES (?, ?, ?, ?)")
    ->execute([$cart_id, $bread_id, $bread_type_id, $quantity]);

$cart_item_id = $pdo->lastInsertId();

// Add extras
foreach ($extras as $ingredient_id) {
    $pdo->prepare("INSERT INTO cart_item_ingredients (cart_item_id, ingredient_id, is_extra) VALUES (?, ?, 1)")
        ->execute([$cart_item_id, $ingredient_id]);
}

echo json_encode(['success' => true]);
