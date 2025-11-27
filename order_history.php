<?php
require 'includes/config.php';
require 'includes/functions.php';
redirectIfNotLoggedIn();

$user_id = $_SESSION['user_id'];

// Fetch all orders
$orders = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$orders->execute([$user_id]);
$orders = $orders->fetchAll(PDO::FETCH_ASSOC);

// Fetch bread types
$bread_types = $pdo->query("SELECT id, name FROM bread_types WHERE is_active = 1")->fetchAll(PDO::FETCH_ASSOC);

// Functions
function getOrderDetails($pdo, $order_id) {
    $order_items_stmt = $pdo->prepare("
        SELECT oi.id as order_item_id, oi.quantity, oi.bread_id, b.name as bread_name, 
               bt.id as bread_type_id, bt.name as bread_type_name
        FROM order_items oi
        JOIN breads b ON oi.bread_id = b.id
        LEFT JOIN bread_types bt ON oi.bread_type_id = bt.id
        WHERE oi.order_id = ?
    ");
    $order_items_stmt->execute([$order_id]);
    $items = $order_items_stmt->fetchAll(PDO::FETCH_ASSOC);

    $ingredient_stmt = $pdo->prepare("
        SELECT i.id, i.name, oii.is_extra
        FROM order_item_ingredients oii
        JOIN ingredients i ON oii.ingredient_id = i.id
        WHERE oii.order_item_id = ?
    ");
    foreach ($items as &$item) {
        $ingredient_stmt->execute([$item['order_item_id']]);
        $item['ingredients'] = $ingredient_stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return $items;
}

// ✅ Handle reorder POST (fixed for JSON fetch)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['reorder']) && $data['reorder'] === true) {
        $bread_id = $data['bread_id'];
        $bread_type_id = $data['bread_type_id'];
        $quantity = $data['quantity'];
        $pickup_time = $data['pickup_time'];
        $ingredients = $data['ingredients'];

        // Get user info
        $stmt = $pdo->prepare("SELECT name, phone FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        // Insert new order
        $pdo->prepare("INSERT INTO orders (user_id, pickup_time, total_price) VALUES (?, ?, 0)")
            ->execute([$user_id, $pickup_time]);
        $order_id = $pdo->lastInsertId();

        // Insert order item
        $pdo->prepare("INSERT INTO order_items (order_id, bread_id, bread_type_id, quantity) VALUES (?, ?, ?, ?)")
            ->execute([$order_id, $bread_id, $bread_type_id, $quantity]);
        $order_item_id = $pdo->lastInsertId();

        // Insert ingredients and calculate total
        $total_price = 0;
        $stmt = $pdo->prepare("SELECT name, price FROM breads WHERE id = ?");
        $stmt->execute([$bread_id]);
        $bread = $stmt->fetch();
        $bread_name = $bread['name'];
        $bread_price = $bread['price'];

        $total_price += $bread_price * $quantity;

        foreach ($ingredients as $ing) {
            $pdo->prepare("INSERT INTO order_item_ingredients (order_item_id, ingredient_id, is_extra) VALUES (?, ?, ?)")
                ->execute([$order_item_id, $ing['id'], $ing['is_extra']]);

            if ($ing['is_extra']) {
                $stmt = $pdo->prepare("SELECT price FROM ingredients WHERE id = ?");
                $stmt->execute([$ing['id']]);
                $extra_price = $stmt->fetchColumn();
                $total_price += $extra_price * $quantity;
            }
        }

        // Update total price
        $pdo->prepare("UPDATE orders SET total_price = ? WHERE id = ?")->execute([$total_price, $order_id]);

        // Build WhatsApp message
        $admin_number = "5978858033"; // <-- replace with your admin number
        $pickup = date("d-m-Y H:i", strtotime($pickup_time));
        $ingredient_list = array_map(function ($ing) {
            return $ing['name'] . ($ing['is_extra'] ? " (extra)" : "");
        }, $ingredients);
        $message = "Hello, I placed a reorder:%0A"
            . "Name: {$user['name']}%0A"
            . "Phone: {$user['phone']}%0A"
            . "Pickup: $pickup%0A"
            . "Sandwich: x{$quantity} {$bread_name} with " . implode(", ", $ingredient_list) . "%0A"
            . "Total: SRD " . number_format($total_price, 2);

        echo json_encode(['success' => true, 'whatsapp_url' => "https://wa.me/$admin_number?text=" . urlencode($message)]);
        exit;
    }

    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Your Order History - Brede Yuru</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: black;
            color: #343a40;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h2 {
            color: #ffc107;
        }

        .floating-btn {
            position: fixed;
            bottom: auto;
            top: 20px;
            right: auto;
            left: 20px;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(180deg, #ffd700, #b8860b);
            color: black;
            border: 2px solid black;
            border-radius: 30px;
            font-size: 1.2em;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 
                0 0 10px rgba(255,215,0,0.7),
                inset 0 1px 3px rgba(255,255,255,0.5),
                inset 0 -3px 5px rgba(0,0,0,0.2);
            transition:  0.3s ease, transform 0.2s ease;
            overflow: hidden;
            animation: goldPulse 2.5s infinite ease-in-out;
            border: none;
            padding: 10px 20px;
            margin-top: 10px;
        }

        .floating-btn:hover {
            background: #e85d4f;
            transform: translateY(-3px) scale(1.05);
        }

        .order-box {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.1);
        }
        .order-box strong {
            color: #ffc107;
        }
        .order-box ul {
            margin-top: 10px;
            list-style: none;
            padding-left: 0;
        }
        .order-box ul li {
            margin-bottom: 5px;
        }
        .reorder-btn {
            background: linear-gradient(180deg, #ffd700, #b8860b);
            color: black;
            border: 2px solid black;
            border-radius: 30px;
            font-size: 1.2em;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 
                0 0 10px rgba(255,215,0,0.7),
                inset 0 1px 3px rgba(255,255,255,0.5),
                inset 0 -3px 5px rgba(0,0,0,0.2);
            transition:  0.3s ease, transform 0.2s ease;
            overflow: hidden;
            animation: goldPulse 2.5s infinite ease-in-out;
            border: none;
            padding: 10px 20px;
            margin-top: 10px;
        }
        .reorder-btn:hover {
            background: linear-gradient(135deg, #ffc107, #ff6f61);
        }

        /* Pulsing glow animation */
        @keyframes goldPulse {
            0% {
                box-shadow: 
                    0 0 10px rgba(255,215,0,0.7),
                    0 0 20px rgba(255,215,0,0.4),
                    inset 0 1px 3px rgba(255,255,255,0.5),
                    inset 0 -3px 5px rgba(0,0,0,0.2);
            }
            50% {
                box-shadow: 
                    0 0 20px rgba(255,223,0,0.9),
                    0 0 40px rgba(255,215,0,0.6),
                    inset 0 1px 3px rgba(255,255,255,0.6),
                    inset 0 -3px 6px rgba(0,0,0,0.3);
            }
            100% {
                box-shadow: 
                    0 0 10px rgba(255,215,0,0.7),
                    0 0 20px rgba(255,215,0,0.4),
                    inset 0 1px 3px rgba(255,255,255,0.5),
                    inset 0 -3px 5px rgba(0,0,0,0.2);
            }
        }
    </style>
</head>
<body>
<div class="header">
    <h2>Your Order History</h2>
    <a href="dashboard.php" class="floating-btn" title="Back to Dashboard">
        <i class="fas fa-arrow-left"></i>
    </a>
</div>

<?php if (empty($orders)): ?>
    <p>No orders found.</p>
<?php else: ?>
    <?php foreach ($orders as $order): ?>
        <div class="order-box">
            <p><strong>Pickup:</strong> <?= date("d M Y - H:i", strtotime($order['pickup_time'])) ?></p>
            <p><strong>Status:</strong> <?= ucfirst($order['status']) ?></p>
            <p><strong>Total:</strong> SRD <?= number_format($order['total_price'], 2) ?></p>
            <ul>
            <?php
                $items = getOrderDetails($pdo, $order['id']);
                foreach ($items as $item):
                    echo "<li><strong>{$item['bread_name']}</strong> (x{$item['quantity']})";
                        if (!empty($item['bread_type_name'])) {
                            echo " <em style='color:#888;'>[{$item['bread_type_name']}]</em>";
                        }
                    if ($item['ingredients']) {
                        echo "<ul>";
                        foreach ($item['ingredients'] as $ing) {
                            echo "<li>" . htmlspecialchars($ing['name']) . ($ing['is_extra'] ? " (extra)" : "") . "</li>";
                        }
                        echo "</ul>";
                    }
                    echo "</li>";
                endforeach;
            ?>
            </ul>
            <button class="reorder-btn" onclick='showReorderPopup(<?= json_encode($items[0]) ?>)'>Reorder</button>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function showReorderPopup(item) {
    let ingredientsHtml = item.ingredients.map(ing => `
        <label>
            <input type="checkbox" value="${ing.id}" ${ing.is_extra ? 'data-extra="1"' : 'data-extra="0"'} checked>
            ${ing.name} ${ing.is_extra ? '(extra)' : ''}
        </label><br>
    `).join('');

    Swal.fire({
        title: 'Customize & Reorder',
        html: `
            <h4>Bread Type</h4>
            <select id="breadType">
                <?php foreach ($bread_types as $bt): ?>
                    <option value="<?= $bt['id'] ?>"><?= htmlspecialchars($bt['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <h4>Ingredients</h4>
            ${ingredientsHtml}
            <h4>Quantity</h4>
            <input type="number" id="quantity" value="${item.quantity}" min="1" style="width: 100%; padding: 8px;">
            <h4>Pickup Time</h4>
            <input type="datetime-local" id="pickupTime" value="<?= date('Y-m-d\TH:i') ?>" style="width: 100%; padding: 8px;">
        `,
        showCancelButton: true,
        confirmButtonText: 'Place Order',
        preConfirm: () => {
            const selectedIngredients = [];
            document.querySelectorAll('input[type="checkbox"]:checked').forEach(cb => {
                selectedIngredients.push({
                    id: cb.value,
                    is_extra: cb.dataset.extra
                });
            });
            return {
                bread_id: item.bread_id,
                bread_type_id: document.getElementById('breadType').value,
                quantity: document.getElementById('quantity').value,
                pickup_time: document.getElementById('pickupTime').value,
                ingredients: selectedIngredients
            };
        }
    }).then(result => {
        if (result.isConfirmed) {
            fetch('', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ reorder: true, ...result.value })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Reorder placed!',
                    text: 'Redirecting to WhatsApp...',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = data.whatsapp_url;
                });
                } else {
                    Swal.fire('Error', 'Could not place reorder.', 'error');
                }
            });
        }
    });
}
</script>
</body>
</html>
