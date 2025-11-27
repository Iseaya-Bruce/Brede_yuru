<?php
require 'includes/config.php';
require 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Not logged in']);
        exit;
    } else {
        redirectIfNotLoggedIn();
    }
}

$user_id = $_SESSION['user_id'];

// Handle AJAX POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');

    $action = $_POST['action'];
    $cart_item_id = intval($_POST['cart_item_id']);

    if ($action === 'remove_item') {
        $stmt = $pdo->prepare("DELETE FROM cart_items WHERE id = ? AND cart_id IN (SELECT id FROM cart WHERE user_id = ?)");
        $success = $stmt->execute([$cart_item_id, $user_id]);
        echo json_encode(['success' => $success]);
        exit;
    }

    if ($action === 'update_quantity') {
        $new_quantity = intval($_POST['quantity']);
        if ($new_quantity > 0) {
            $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE id = ? AND cart_id IN (SELECT id FROM cart WHERE user_id = ?)");
            $success = $stmt->execute([$new_quantity, $cart_item_id, $user_id]);
            echo json_encode(['success' => $success]);
            exit;
        }
    }

    echo json_encode(['success' => false, 'error' => 'Invalid action']);
    exit;
}


// Get cart ID
$stmt = $pdo->prepare("SELECT id FROM cart WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart = $stmt->fetch();

if (!$cart) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Your Cart - Brede Yuru</title>
        <link rel="stylesheet" href="assets/css/style.css">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
        <title>Empty Cart</title>
        <style>
            html, body {
                margin: 0;
                padding: 0;
                overflow-x: hidden;
                width: 100vw;
                height: 100vh;
                font-family: 'Poppins', sans-serif;
                background: linear-gradient(135deg, #e4c267ff, #d4b21aff);
                display: flex;
                justify-content: center;
                align-items: center;
                color: #343a40;
                text-align: center;
                box-sizing: border-box;
            }

            .empty-cart {
                background: #ffffff;
                padding: 40px 20px;
                border-radius: 30px;
                box-shadow: 0 8px 30px rgba(0,0,0,0.12);
                width: 90vw;
                max-width: 620px;
                box-sizing: border-box;
                position: relative;
                z-index: 2;
            }

            .empty-cart h1 {
                font-size: 2.4rem;
                margin-top: 20px;
                font-weight: 700;
                color: #c9b611ff;
                line-height: 1.2;
            }

            .empty-cart p {
                font-size: 1.25rem;
                color: #55585a;
                margin-bottom: 30px;
                font-weight: 500;
                line-height: 1.5;
            }

            .empty-cart a {
                display: inline-block;
                padding: 14px 28px;
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
                transition: 0.3s ease, transform 0.2s ease;
                animation: goldPulse 2.5s infinite ease-in-out;
            }

            .empty-cart a:hover {
                background: #e63946;
                box-shadow: 0 6px 16px rgba(230,57,70,0.6);
                transform: translateY(-2px);
            }

            @keyframes goldPulse {
                0%, 100% { box-shadow: 0 0 10px rgba(255,215,0,0.7); }
                50% { box-shadow: 0 0 25px rgba(255,215,0,1); }
            }

            /* 🎬 Tumbleweed animation */
            .tumbleweed {
                position: absolute;
                bottom: 10px;
                left: -100px;
                width: 200px;
                height: 200px;
                background: url('assets/images/tumbleweed.png') no-repeat center/contain;
                animation: roll 12s linear infinite;
                z-index: 1;
                opacity: 0.8;
            }

            .cactus {
                position: absolute;
                bottom: 0;
                width: 40px;
                height: 300px;
                background: #2e8b57;
                border-radius: 20px;
            }
            .cactus::before {
                content: '';
                position: absolute;
                top: 20px;
                left: -20px;
                width: 30px;
                height: 60px;
                background: #2e8b57;
                border-radius: 15px;
            }
            .cactus::after {
                content: '';
                position: absolute;
                top: 40px;
                right: -20px;
                width: 30px;
                height: 50px;
                background: #2e8b57;
                border-radius: 15px;
            }

            @keyframes roll {
                0% { transform: translateX(-100px) rotate(0deg); opacity: 0; }
                10% { opacity: 1; }
                50% { transform: translateX(50vw) rotate(720deg); }
                90% { opacity: 1; }
                100% { transform: translateX(110vw) rotate(1440deg); opacity: 0; }
            }

            /* Responsive */
            @media (max-width: 480px) {
                .empty-cart {
                    padding: 20px 15px;
                    width: 95vw;
                }
                .empty-cart h1 { font-size: 1.8rem; }
                .empty-cart p { font-size: 1rem; }
                .empty-cart a { padding: 10px 20px; font-size: 1em; }
                .tumbleweed { width: 80px; height: 80px; }
                .cactus { width: 40px; height: 100px; }
            }
        </style>
        </head>
        <body>
            <div class="empty-cart">
                <h1>Your cart is empty 😕</h1>
                <p>Looks like you haven’t added any sandwiches yet.</p>
                <a href="dashboard.php">← Back to Dashboard</a>
            </div>

            <div class="cactus" style="left: 10%;"></div>
            <div class="cactus" style="right: 15%;"></div>

            <!-- Rolling tumbleweed -->
            <div class="tumbleweed"></div>
        </body>
        </html>
    <?php
    exit;
}

$cart_id = $cart['id'];

// Fetch items and ingredients
$stmt = $pdo->prepare("
    SELECT ci.id as cart_item_id, ci.quantity, b.name as bread_name, b.price as bread_price, b.image_path,
           bt.name as bread_type_name
    FROM cart_items ci
    JOIN breads b ON ci.bread_id = b.id
    LEFT JOIN bread_types bt ON ci.bread_type_id = bt.id
    WHERE ci.cart_id = ?
");
$stmt->execute([$cart_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get ingredients per item
$ingredient_stmt = $pdo->prepare("
    SELECT i.name, i.price, cii.is_extra
    FROM cart_item_ingredients cii
    JOIN ingredients i ON cii.ingredient_id = i.id
    WHERE cii.cart_item_id = ?
");

$total = 0;

// Calculate cart total for live preview
$stmt = $pdo->prepare("
    SELECT ci.quantity, b.price,
           (SELECT SUM(i.price * ci.quantity) 
            FROM cart_item_ingredients cii 
            JOIN ingredients i ON cii.ingredient_id = i.id
            WHERE cii.cart_item_id = ci.id AND cii.is_extra = 1) as extras
    FROM cart_items ci
    JOIN breads b ON ci.bread_id = b.id
    WHERE ci.cart_id = (SELECT id FROM cart WHERE user_id = ?)
");
$stmt->execute([$_SESSION['user_id']]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$cart_total = 0;
foreach ($rows as $row) {
    $base = $row['price'] * $row['quantity'];
    $extras = $row['extras'] ?? 0;
    $cart_total += $base + $extras;
    $cart_total = $cart_total ?? 0;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Your Cart - Brede Yuru</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: black;
            padding: 20px;
            color: #343a40;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h2 {
            color: #ffc107;
            font-size: 2.2em;
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


        .cart-container {
            max-width: 950px;
            margin: 0 auto;
        }

        .cart-box {
            background: #fff;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.3s ease;
        }

        .cart-box:hover {
            transform: translateY(-3px);
        }

        .cart-box img {
            width: 120px;
            height: 120px;
            border-radius: 12px;
            object-fit: cover;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .cart-details {
            flex: 1;
        }

        .cart-details h3 {
            margin: 0;
            color: #ffc107;
        }

        .cart-details p {
            margin: 5px 0;
            color: #495057;
        }

        .cart-details ul {
            list-style: none;
            padding: 0;
            margin: 10px 0;
        }

        .cart-details ul li {
            margin: 3px 0;
            color: #6c757d;
        }

        .item-total {
            font-weight: bold;
            color: #ffc107;
            margin-top: 10px;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }

        .quantity-control input[type="number"] {
            width: 60px;
            text-align: center;
            border: 2px solid #ccc;
            border-radius: 8px;
            padding: 6px;
            margin-left: 10px;
            margin-right: 10px;
        }

        .remove-btn {
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
            margin-left: 10px;
           
        }

        .remove-btn:hover {
            background: #e63946;
        }

        .grand-total-bar form {
            display: inline;
            align-items: center;
            background-color: #3e3be6ff;
            gap: 6px; /* space between input and button */
        }

        .grand-total-bar strong {
            color: white;
        }

        .grand-total-bar input[type="datetime-local"] {
            padding: 14px 16px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 6px;
            background: #ffffffff;
            outline: none;
            height: 32px;
        }

        .grand-total-bar input[type="datetime-local"]:focus {
            border-color: #ff6f61;
            box-shadow: 0 0 4px rgba(255, 111, 97, 0.4);
        }

        /* Modal Background */
        .modal {
            display: none;
            position: fixed;
            z-index: 100;
            padding-top: 120px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }

        /* Modal Content Box */
        .modal-content {
            background-color: #fff;
            margin: auto;
            padding: 20px;
            border-radius: 10px;
            width: 300px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            text-align: center;
        }

        /* Close Button */
        .close-btn {
            position: absolute;
            right: 20px;
            top: 15px;
            font-size: 24px;
            cursor: pointer;
        }

        /* Input inside modal */
        .modal-content input[type="datetime-local"] {
            padding: 8px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 6px;
            width: 100%;
            margin-bottom: 12px;
        }

        /* Checkout Button */
        .checkout-btn {
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

        .checkout-btn:hover {
            background: #e65a50;
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
    <h2>Your Cart</h2>
    <a href="dashboard.php" class="floating-btn" title="Back to Dashboard">
        <i class="fas fa-arrow-left"></i>
    </a>
</div>

<div class="cart-container">
    <?php if (empty($items)): ?>
        <div class="empty-cart">
            <h1>Your cart is empty 😕</h1>
            <p>Looks like you haven’t added any sandwiches yet.</p>
            <a href="dashboard.php">← Back to Dashboard</a>
        </div>
    <?php else: ?>
        <?php foreach ($items as $item): ?>
            <div class="cart-box" data-id="<?= $item['cart_item_id'] ?>">
                <img src="<?= htmlspecialchars($item['image_path'] ?: 'assets/img/placeholder.png') ?>" alt="<?= htmlspecialchars($item['bread_name']) ?>">
                <div class="cart-details">
                    <h3><?= htmlspecialchars($item['bread_name']) ?></h3>
                    <p>Type: <?= htmlspecialchars($item['bread_type_name'] ?? 'N/A') ?></p>
                    <ul>
                        <?php
                            $ingredient_stmt->execute([$item['cart_item_id']]);
                            $ingredients = $ingredient_stmt->fetchAll(PDO::FETCH_ASSOC);
                            $item_total = $item['bread_price'] * $item['quantity'];
                            foreach ($ingredients as $ing):
                                if ($ing['is_extra']) $item_total += ($ing['price'] * $item['quantity']);
                        ?>
                            <li><?= htmlspecialchars($ing['name']) ?><?= $ing['is_extra'] ? " (+SRD " . number_format($ing['price'], 2) . ")" : "" ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="remove-btn" onclick="removeItem(<?= $item['cart_item_id'] ?>)">Remove</button>
                    <div class="item-total">Item Total: SRD <?= number_format($item_total, 2) ?></div>
                </div>
            </div>
            <?php $total += $item_total; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php if (!empty($items)): ?>
    <div class="grand-total-bar">
        <strong>Grand Total: SRD <?= number_format($total, 2) ?></strong>
        <button type="button" class="checkout-btn" id="openPickupModal">Confirm Order</button>
    </div>
<?php endif; ?>

<!-- Pickup Time Modal -->
<div id="pickupModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" id="closePickupModal">&times;</span>
        <h2>Select Pickup Time</h2>
        <form id="cartForm" method="POST">
            <label for="pickup_time">Pickup Time:</label>
            <input type="datetime-local" id="pickup_time" name="pickup_time" required>

            <h4>Order Type</h4>
            <label>
                <input type="radio" name="order_type" value="pickup" checked onclick="toggleDelivery(false)">
                Pickup
            </label>
            <label>
                <input type="radio" name="order_type" value="delivery" onclick="toggleDelivery(true)">
                Delivery
            </label>

            <div id="deliveryOptions" style="display: none; margin-top: 10px;">
                <label for="street">Select Street (Delivery):</label>
                <select id="street" name="street" onchange="updateDeliveryFee()">
                    <option value="">-- Choose a street --</option>
                    <option value="Steenslag weg|10">Steenslag weg - SRD 10</option>
                    <option value="Simon Sadoekweg|15">Simon Sadoekweg - SRD 15</option>
                    <option value="Kobaltweg|15">Kobaltweg - SRD 15</option>
                    <option value="Kiezelweg|20">Kiezelweg - SRD 20</option>
                    <option value="Noord straat|20">Noord straat - SRD 20</option>
                    <option value="Bananesteeg|20">Bananesteeg - SRD 20</option>
                    <option value="Meursweg|30">Meursweg - SRD 30</option>
                </select>
            </div>

            <!-- Hidden inputs for backend -->
            <input type="hidden" id="streetName" name="street_name">
            <input type="hidden" id="streetFee" name="street_fee">

            <!-- Live price summary -->
            <div id="priceSummary" style="margin-top: 15px; font-weight: bold;">
                Sandwich Total: SRD <span id="baseTotal"><?= number_format($cart_total, 2) ?></span><br>
                Delivery Fee: SRD <span id="deliveryFee">0.00</span><br>
                <hr>
                Grand Total: SRD <span id="grandTotal"><?= number_format($cart_total, 2) ?></span>
            </div>

            <h4>Location Option</h4>
            <label>
            <input type="radio" name="location_option" value="whatsapp" checked onclick="toggleMap(false)">
            Send live location in WhatsApp
            </label>
            <br>
            <label>
            <input type="radio" name="location_option" value="map" onclick="toggleMap(true)">
            Choose location on map
            </label>

            <div id="mapContainer" style="display:none; margin-top:10px;">
            <div id="map" style="height:300px;"></div>
            <input type="hidden" name="lat" id="lat">
            <input type="hidden" name="lng" id="lng">
            </div>

            <!-- Confirm Order button -->
            <button type="button" id="confirmOrderBtn">Confirm Order</button>
        </form>
    </div>
</div>


<!-- Your JavaScript and SweetAlert scripts here -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
<!-- Load Leaflet CSS/JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    let baseTotal = <?= $cart_total ?>;
    let deliveryFee = 0;

    function toggleDelivery(show) {
        document.getElementById('deliveryOptions').style.display = show ? 'block' : 'none';
        if (!show) {
            deliveryFee = 0;
            document.getElementById('street').value = "";
            document.getElementById('streetName').value = "";
            document.getElementById('streetFee').value = 0;
            updateTotals();
        }
    }

    function updateDeliveryFee() {
        let streetSelect = document.getElementById('street').value;
        if (streetSelect) {
            let parts = streetSelect.split("|");
            document.getElementById('streetName').value = parts[0];
            document.getElementById('streetFee').value = parts[1];
            deliveryFee = parseFloat(parts[1]);
        } else {
            deliveryFee = 0;
            document.getElementById('streetName').value = "";
            document.getElementById('streetFee').value = 0;
        }
        updateTotals();
    }

    function updateTotals() {
        document.getElementById('deliveryFee').innerText = deliveryFee.toFixed(2);
        document.getElementById('grandTotal').innerText = (baseTotal + deliveryFee).toFixed(2);
    }

    let map, marker;

    function toggleMap(show) {
    document.getElementById('mapContainer').style.display = show ? 'block' : 'none';

    if (show && !map) {
        map = L.map('map').setView([5.852, -55.203], 13); // Default coords Suriname
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        map.on('click', function(e) {
        const { lat, lng } = e.latlng;
        if (marker) marker.setLatLng(e.latlng);
        else marker = L.marker(e.latlng).addTo(map);

        document.getElementById('lat').value = lat;
        document.getElementById('lng').value = lng;
        });
    }
    }

    document.getElementById("confirmOrderBtn").addEventListener("click", () => {
        const form = document.getElementById("cartForm");
        const formData = new FormData(form);

        fetch("confirm_order.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // ✅ WhatsApp opens in new tab
                window.open(data.whatsapp_url, "_blank"); 

                // ✅ Current tab goes to dashboard
                window.location.href = data.redirect_url; 
            } else {
                alert("Error: " + data.error);
            }
        })
        .catch(err => {
            console.error("Order failed:", err);
            alert("Something went wrong!");
        });
    });

     // Modal open/close — run on page load
    const modal = document.getElementById("pickupModal");
    const openBtn = document.getElementById("openPickupModal");
    const closeBtn = document.getElementById("closePickupModal");

    // Open modal
    openBtn.addEventListener("click", () => {
        modal.style.display = "block";
    });

    // Close modal
    closeBtn.addEventListener("click", () => {
        modal.style.display = "none";
    });

    // Close modal if click outside
    window.addEventListener("click", (event) => {
        if (event.target === modal) modal.style.display = "none";
    });
    
    // Your existing JS functions (removeItem, updateQuantity, modal handlers, etc.)
    function updateQuantity(itemId, qty) {
        fetch('', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ action: 'update_quantity', cart_item_id: itemId, quantity: qty })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) location.reload();
            else Swal.fire('Error', 'Could not update quantity', 'error');
        });
    }

    

    // Remove item
    function removeItem(itemId) {
        fetch('cart_actions.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                action: 'remove_item',
                cart_item_id: itemId
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Removed!', 'Item removed from cart.', 'success').then(() => {
                    document.querySelector('.cart-box[data-id="' + itemId + '"]').remove();
                });
            } else {
                Swal.fire('Error', data.error || 'Could not remove item.', 'error');
            }
        })
        .catch(err => Swal.fire('Error', 'Request failed.', 'error'));
    }

</script>
</body>
</html>
