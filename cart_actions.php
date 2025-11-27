<?php
require 'includes/config.php';
require 'includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $cart_item_id = intval($_POST['cart_item_id']);

    if ($_POST['action'] === 'remove_item') {
    $cart_item_id = intval($_POST['cart_item_id']);

    // First delete ingredients linked to this cart item
    $stmt = $pdo->prepare("DELETE FROM cart_item_ingredients WHERE cart_item_id = ?");
    $stmt->execute([$cart_item_id]);

    // Now delete the cart item itself
    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE id = ?");
    if ($stmt->execute([$cart_item_id])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to remove cart item.']);
    }
    exit;
}


    if ($action === 'update_quantity') {
        $quantity = intval($_POST['quantity']);
        if ($quantity > 0) {
            $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE id = ? AND cart_id IN (SELECT id FROM cart WHERE user_id = ?)");
            $success = $stmt->execute([$quantity, $cart_item_id, $user_id]);
            echo json_encode(['success' => $success]);
            exit;
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid quantity']);
            exit;
        }
    }

    echo json_encode(['success' => false, 'error' => 'Invalid action']);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Invalid request']);
