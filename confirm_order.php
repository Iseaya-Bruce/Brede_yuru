<?php
require 'includes/config.php';
require 'includes/functions.php';
redirectIfNotLoggedIn();

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_type  = $_POST['order_type'] ?? 'pickup';  // pickup | delivery
    $street_name = $_POST['street_name'] ?? null;
    $street_fee  = (float) ($_POST['street_fee'] ?? 0);
    $pickup_time = $_POST['pickup_time'];

    // Get user info
    $stmt = $pdo->prepare("SELECT name, phone FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    // Get cart
    $stmt = $pdo->prepare("SELECT id FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $cart = $stmt->fetch();
    if (!$cart) {
        echo json_encode(['success' => false, 'error' => 'Cart not found.']);
        exit;
    }
    $cart_id = $cart['id'];

    // Get cart items
    $cart_items_stmt = $pdo->prepare("
        SELECT ci.id as cart_item_id, b.id as bread_id, b.name as bread_name, b.price as bread_price,
               ci.bread_type_id, ci.quantity
        FROM cart_items ci
        JOIN breads b ON ci.bread_id = b.id
        WHERE ci.cart_id = ?
    ");
    $cart_items_stmt->execute([$cart_id]);
    $items = $cart_items_stmt->fetchAll(PDO::FETCH_ASSOC);

    $ingredient_stmt = $pdo->prepare("
        SELECT i.id, i.name, cii.is_extra
        FROM cart_item_ingredients cii
        JOIN ingredients i ON cii.ingredient_id = i.id
        WHERE cii.cart_item_id = ?
    ");

    // Store order
    $total_price = 0;
    $pdo->prepare("INSERT INTO orders (user_id, pickup_time, total_price) VALUES (?, ?, 0)")
        ->execute([$user_id, $pickup_time]);
    $order_id = $pdo->lastInsertId();

    $whatsapp_items = [];

    foreach ($items as $item) {
        $bread_name = $item['bread_name'];
        $bread_type = !empty($item['bread_type_name']) ? " ({$item['bread_type_name']})" : "";
        $quantity = $item['quantity'];
        
        // Insert order item
        $pdo->prepare("INSERT INTO order_items (order_id, bread_id, bread_type_id, quantity, price) VALUES (?, ?, ?, ?, ?)")
            ->execute([
                $order_id,
                $item['bread_id'],
                $item['bread_type_id'],
                $item['quantity'],
                $item['bread_price']
            ]);
        $order_item_id = $pdo->lastInsertId();

        $item_total = $item['bread_price'] * $item['quantity'];
        $ingredient_stmt->execute([$item['cart_item_id']]);
        $ingredients = $ingredient_stmt->fetchAll(PDO::FETCH_ASSOC);

        $ingredient_text = [];

        foreach ($ingredients as $ing) {
            $pdo->prepare("INSERT INTO order_item_ingredients (order_item_id, ingredient_id, is_extra) VALUES (?, ?, ?)")
                ->execute([$order_item_id, $ing['id'], $ing['is_extra']]);

            if ($ing['is_extra']) {
                $price_stmt = $pdo->prepare("SELECT price FROM ingredients WHERE id = ?");
                $price_stmt->execute([$ing['id']]);
                $extra_price = $price_stmt->fetchColumn();
                $item_total += $extra_price * $item['quantity'];
            }
            $ingredient_text[] = $ing['name'] . ($ing['is_extra'] ? " (extra)" : "");
        }

            $whatsapp_items[] = "{$item['bread_name']} x{$item['quantity']} (" . implode(", ", $ingredient_text) . ")";
            $total_price += $item_total;
        }

        // ✅ Separate sandwich subtotal
        $sandwich_total = $total_price;

        // ✅ Add delivery fee if needed
        $delivery_fee = 0;
        if ($order_type === 'delivery' && $street_name && $street_fee > 0) {
            $delivery_fee = $street_fee;
        }

        // ✅ Final grand total
        $grand_total = $sandwich_total + $delivery_fee;

        // Update total in DB
        $pdo->prepare("UPDATE orders SET total_price = ? WHERE id = ?")->execute([$grand_total, $order_id]);

        // Clear cart
        $pdo->prepare("DELETE FROM cart_item_ingredients WHERE cart_item_id IN (SELECT id FROM cart_items WHERE cart_id = ?)")->execute([$cart_id]);
        $pdo->prepare("DELETE FROM cart_items WHERE cart_id = ?")->execute([$cart_id]);
        $pdo->prepare("DELETE FROM cart WHERE id = ?")->execute([$cart_id]);

        // ✅ Build WhatsApp message with 3 totals
        $admin_number = "5977149293"; 
        $pickup = date("d-m-Y H:i", strtotime($pickup_time));
        $message = "Hello Brede-yuru😊\n\n"
            . "I placed an order:\n"
            . "Name: {$user['name']}\n"
            . "Phone: {$user['phone']}\n"
            . "Pickup: $pickup\n\n"
            . "Items: " . implode(", ", $whatsapp_items) . "\n\n"
            . "Sandwich Total: SRD " . number_format($sandwich_total, 2) . "\n"
            . "Delivery Fee: SRD " . number_format($delivery_fee, 2) . "\n"
            . "Grand Total: SRD " . number_format($grand_total, 2);

        // Append pickup/delivery note
        if ($order_type === 'pickup') {
            $message .= "\n\nNote: I will pick this order up.";
        } else {
            $message .= "\n\nDelivery to: $street_name";
        }

        $location_option = $_POST['location_option'] ?? 'whatsapp';
        $lat = $_POST['lat'] ?? null;
        $lng = $_POST['lng'] ?? null;

        if ($order_type === 'delivery') {
            if ($location_option === 'map' && $lat && $lng) {
                $message .= "\n\n📍 Delivery Location: https://maps.google.com/?q={$lat},{$lng}";
            } else {
                $message .= "\n\n📍 Please send your live location in WhatsApp.";
            }
        }

    $wa_url = "https://wa.me/$admin_number?text=" . urlencode($message);

    echo json_encode([
        'success' => true,
        'whatsapp_url' => $wa_url,
        'redirect_url' => 'dashboard.php'
    ]);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Invalid request']);
exit;
