<?php
require 'includes/config.php';
require 'includes/functions.php';
redirectIfNotLoggedIn();

// Fetch breads
$breads = $pdo->query("SELECT * FROM breads WHERE is_active = 1")->fetchAll(PDO::FETCH_ASSOC);

// Fetch standard ingredients for all breads (with image_path)
$breadIngredients = [];
foreach ($breads as $bread) {
    $stmt = $pdo->prepare("
        SELECT i.id, i.name, i.image_path
        FROM bread_ingredients bi
        JOIN ingredients i ON bi.ingredient_id = i.id
        WHERE bi.bread_id = ?
    ");
    $stmt->execute([$bread['id']]);
    $breadIngredients[$bread['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// Fetch all extra ingredients (with image_path)
$extra_ingredients = $pdo->query("SELECT * FROM ingredients WHERE is_standard = 0 AND is_active = 1")->fetchAll(PDO::FETCH_ASSOC);

// Fetch bread types (with image_path)
$bread_types = $pdo->query("SELECT * FROM bread_types WHERE is_active = 1")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Brede Yuru - Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <style>
        body {
             font-family: 'Poppins', sans-serif;
            margin: 0;
            background-image: url('assets/images/brede lights.png');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            color: #343a40;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header h2 {
            font-size: 2.2em;
            color: #ffc107;
            animation: fadeInDown 1s ease-out;
        }

        .nav-links {
            display: flex;                /* Use flexbox for alignment */
            justify-content: center;      /* Center horizontally */
            align-items: center;          /* Center vertically (if needed) */
            gap: 5px;                    /* Space between links */
            margin-bottom: 30px;
            flex-wrap: wrap;              /* Allow wrapping on small screens */
        }

        .nav-links a {
            font-weight: 500;
            text-decoration: none;
            padding: 8px 15px;
            background: linear-gradient(180deg, #ffd700, #b8860b);
            color: black;
            border: black;
            border-radius: 30px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 
                0 0 10px rgba(255,215,0,0.7),
                inset 0 1px 3px rgba(255,255,255,0.5),
                inset 0 -3px 5px rgba(0,0,0,0.2);
            transition:  0.3s ease, transform 0.2s ease;
            overflow: hidden;
            animation: goldPulse 2.5s infinite ease-in-out;
        }

        .nav-links a:hover {
            background: #ff6f61;
            color: #fff;
            transform: translateY(-2px);
        }


       .swiper {
            width: 630px;
            height: 730px;
            margin: 0 auto;
            
            overflow: hidden;
            animation: fadeIn 0.ms ease-out;
            background: transparent;
            display: flex; /* ensures content stays centered */
            align-items: center;
            justify-content: center;
        }

        .swiper-slide {
            display: flex;
            flex-direction: column; /* stack image and text vertically */
            align-items: center;
            justify-content: center;
            opacity: 1;
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .swiper-slide img {
            max-width: 80%; /* keeps image smaller than circle */
            max-height: 80%;
            object-fit: contain; /* keeps sandwich proportions */
            border-radius: 50%;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            transition: transform 0.5s ease;
            margin-bottom: 120px;
        }

       .info-box {
            position: absolute;
            margin-top: 680px;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: transparent;
            padding: 12px 20px;
            
            
            width: 180px;
            text-align: center;
        }

        .info-box h3 {
            font-size: 1.5em;
            margin: 0 0 8px 0;
            width: 150px;
            text-decoration: underline;
            background: linear-gradient(180deg, #ffd700, #b8860b);
            color: black;
            border: black;
            border-radius: 30px;
            font-weight: bold;
            cursor: pointer;
        }

        .info-box p {
            font-size: 1.5em;
            margin: 0 0 8px 0;
            width: 150px;
            text-decoration: underline;
            background: linear-gradient(180deg, #ffd700, #b8860b);
            color: black;
            border: black;
            border-radius: 30px;
            font-weight: bold;
            cursor: pointer;
        }

        .swipe h2 {
            margin-left: 150px;
            font-size: 1em;
            width: 150px;
            color: black;
            border: black;
            border-radius: 30px;
            font-weight: bold;
        }

       

        /* Tablets and smaller desktops */
        @media (max-width: 768px) {
            .swiper {
                width: 90vw;    /* 90% of viewport width */
                height: 90vw;   /* keep it square and circular */
                max-width: 480px; /* don’t get TOO big on tablets */
                max-height: 480px;
            }

            .swiper-slide img {
                max-width: 70%;
                max-height: 70%;
            }

            .info-box {
                margin-top: 0px;
            }
        }

        /* Phones and very small screens */
        @media (max-width: 480px) {
            .swiper {
                width: 80vw;
                height: 80vw;
                max-width: 320px;
                max-height: 320px;
            }

            .swiper-slide img {
                max-width: 65%;
                max-height: 65%;
            }

            .swiper-slide h3 {
                font-size: 1.2em;
            }

            .swiper-slide p {
                font-size: 1em;
            }
        }


        .section-container {
            display: flex;
            gap: 20px;
            max-width: 1100px;
            margin: 40px auto;
            flex-wrap: wrap;
        }

        .section-left, .section-right {
            flex: 1;
            min-width: 350px;
            background: #ffffff;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            animation: fadeInUp 1s ease-out;
        }

        .section-left h4, .section-right h4 {
            color: #ffc107;
            margin-bottom: 10px;
        }

        .ingredient-list, .extra-ingredient-list, .bread-type-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .ingredient-item, .ingredient-btn, .bread-type-btn {
            display: flex;
            align-items: center;
            background: #f1f3f5;
            color: #495057;
            padding: 8px 14px;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .ingredient-item img, .ingredient-btn img, .bread-type-btn img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
        }

        .ingredient-btn:hover, .bread-type-btn:hover {
            background: #ff6f61;
            color: #fff;
        }

        input[type="number"] {
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            border: 2px solid #ccc;
            border-radius: 10px;
            font-size: 1.1em;
        }

        button {
            position: relative; /* For shine stripe */
            width: 100%;
            padding: 14px;
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

        /* Shiny stripe effect */
        button::before {
            content: "";
            position: absolute;
            top: 0;
            left: -75%;
            width: 50%;
            height: 100%;
            background: linear-gradient(
                120deg,
                rgba(255,255,255,0.4) 0%,
                rgba(255,255,255,0.1) 40%,
                rgba(255,255,255,0) 100%
            );
            transform: skewX(-25deg);
        }

        /* Shine animation on hover */
        button:hover::before {
            animation: shine 0.8s forwards;
        }

        @keyframes shine {
            0% { left: -75%; }
            100% { left: 125%; }
        }

        button:hover {
            background: linear-gradient(180deg, #ffec8b, #daa520);
            transform: translateY(-2px);
        }

        .total-price {
            font-size: 1.5em;
            font-weight: bold;
            color: #ffc107;
            text-align: center;
            margin-top: 20px;
        }

        /* Customize Button */
        .customize-btn-container {
            text-align: center;
            margin-bottom: 15px;
        }

        .customize-btn {
           position: relative; /* For shine stripe */
            width: 100%;
            padding: 14px;
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
        }

        .customize-btn:hover {
            background: linear-gradient(135deg, #ffc107, #ff6f61);
            transform: translateY(-2px);
        }

        /* Modal Styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 999;
            left: 0; top: 0;
            width: 100%; height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.6);
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border-radius: 12px;
            max-width: 600px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
            animation: fadeInUp 0.5s ease;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover, .close:focus {
            color: #ffc107;
        }

        /* Highlight selected bread type */
        .bread-type-btn.active {
            background: #ff6f61;
            color: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            transform: scale(1.05);
        }

        /* Highlight selected extra ingredient */
        .ingredient-btn.active {
            background: #ffc107;
            color: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            transform: scale(1.05);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Hide Swiper arrows on mobile */
        @media (max-width: 768px) {
            .swiper-button-next,
            .swiper-button-prev {
                display: none;
            }
        }

    </style>
</head>
<body>

<div class="header">
    <h2>Welcome to Brede Yuru 🥪</h2>
</div>

<div class="nav-links">
    <a href="cart.php">🛒 Cart</a> |
    <a href="order_history.php">🧾 Orders</a> |
    <a href="logout.php">🚪 Logout</a>
</div>

<!-- Swiper -->
<div class="swiper">
    <div class="swiper-wrapper">
        <?php foreach ($breads as $bread): ?>
            <div class="swiper-slide" data-id="<?= $bread['id'] ?>" data-price="<?= $bread['price'] ?>">
                <img src="<?= $bread['image_path'] ?: 'assets/img/placeholder.png' ?>" alt="Bread">
                <div class="info-box">
                    <h3><?= htmlspecialchars($bread['name']) ?></h3>
                    <p>SRD <?= number_format($bread['price'], 2) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
</div>

<div class="swipe">
    <h2>(Swipe)</h2>
</div>

<div class="section-container">
    <div class="section-left">
        <h4>Standard Ingredients:</h4>
        <div class="ingredient-list" id="standardIngredients"></div>
    </div>
</div>

<!-- Button to open customization modal -->
    <div class="customize-btn-container">
        <button onclick="openModal()" class="customize-btn">🍞 Customize Sandwich</button>
    </div>

    <!-- Customization Modal -->
    <div id="customizeModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h4>Select Bread Type:</h4>
            <div class="bread-type-list">
                <?php foreach ($bread_types as $type): ?>
                    <div class="bread-type-btn" 
                        id="breadType-<?= $type['id'] ?>"
                        onclick="selectBreadType(<?= $type['id'] ?>, '<?= htmlspecialchars($type['name']) ?>')">
                        <img src="<?= $type['image_path'] ?: 'assets/img/default-ingredient.png' ?>" alt="<?= htmlspecialchars($type['name']) ?>">
                        <?= htmlspecialchars($type['name']) ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <p><strong>Selected Bread:</strong> <span id="selectedBreadType">None</span></p>

            <h4>Add Extra Ingredients:</h4>
            <div class="extra-ingredient-list">
                <?php foreach ($extra_ingredients as $extra): ?>
                    <div class="ingredient-btn" 
                        id="extraIngredient-<?= $extra['id'] ?>"
                        onclick="toggleExtraIngredient(<?= $extra['id'] ?>, '<?= $extra['name'] ?>', <?= $extra['price'] ?>)">
                        <img src="<?= $extra['image_path'] ?: 'assets/img/default-ingredient.png' ?>" alt="<?= htmlspecialchars($extra['name']) ?>">
                        + <?= htmlspecialchars($extra['name']) ?> (SRD <?= number_format($extra['price'], 2) ?>)
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>


<label>Quantity:</label>
        <input type="number" id="quantity" min="1" value="1">

        <div class="total-price">Total: SRD <span id="totalPrice">0.00</span></div>

            <button onclick="addToCart()">Add to Cart</button>
        </div>

<script>
    const breads = <?= json_encode($breads) ?>;
    const breadIngredients = <?= json_encode($breadIngredients) ?>;
    let currentBread = breads[0];
    let selectedExtras = [];
    let selectedBreadType = null;

   const swiper = new Swiper('.swiper', {
        slidesPerView: 1.2,
        spaceBetween: 20,
        centeredSlides: true,
        loop: false, // 👈 Turn OFF loop to avoid index mismatch
        navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
        on: {
            slideChange: () => {
                const activeIndex = swiper.activeIndex;
                currentBread = breads[activeIndex];
                renderStandardIngredients(currentBread.id);
                updateTotal();
            }
        }
    });


    function renderStandardIngredients(breadId) {
        const container = document.getElementById('standardIngredients');
        container.innerHTML = '';
        breadIngredients[breadId].forEach(ing => {
            const imgSrc = ing.image_path ? ing.image_path : 'assets/img/default-ingredient.png';
            container.innerHTML += `
                <div class="ingredient-item">
                    <img src="${imgSrc}" alt="${ing.name}">
                    ${ing.name}
                </div>
            `;
        });
    }

    function selectBreadType(id, name) {
        selectedBreadType = id;

        // Remove highlight from all bread types
        document.querySelectorAll('.bread-type-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        // Highlight the selected bread type
        document.getElementById(`breadType-${id}`).classList.add('active');

        // Update display
        document.getElementById('selectedBreadType').textContent = name;

        Swal.fire({
            toast: true, icon: 'success', title: name + ' selected!',
            showConfirmButton: false, timer: 1000, position: 'top-end'
        });
    }

    function toggleExtraIngredient(id, name, price) {
        const index = selectedExtras.findIndex(e => e.id === id);

        if (index === -1) {
            // Add to selected
            selectedExtras.push({ id, name, price });
            document.getElementById(`extraIngredient-${id}`).classList.add('active');
            Swal.fire({
                toast: true, icon: 'success', title: name + ' added!',
                showConfirmButton: false, timer: 1000, position: 'top-end'
            });
        } else {
            // Remove from selected
            selectedExtras.splice(index, 1);
            document.getElementById(`extraIngredient-${id}`).classList.remove('active');
            Swal.fire({
                toast: true, icon: 'info', title: name + ' removed!',
                showConfirmButton: false, timer: 1000, position: 'top-end'
            });
        }

        updateTotal();
    }

    function updateTotal() {
        const basePrice = parseFloat(currentBread.price);
        const extrasPrice = selectedExtras.reduce((sum, e) => sum + e.price, 0);
        const quantity = parseInt(document.getElementById('quantity').value) || 1;
        const total = (basePrice + extrasPrice) * quantity;
        document.getElementById('totalPrice').textContent = total.toFixed(2);
    }

    function addToCart() {
        if (!selectedBreadType) {
            Swal.fire({ icon: 'warning', title: 'Select a bread type first!' });
            return;
        }
        const quantity = parseInt(document.getElementById('quantity').value) || 1;
        const extras = selectedExtras.map(e => e.id);
        fetch('add_to_cart.php', {
            method: 'POST', headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                bread_id: currentBread.id, bread_type_id: selectedBreadType,
                extras, quantity
            })
        }).then(res => res.json()).then(data => {
            if (data.success) {
                Swal.fire({ icon: 'success', title: 'Added to cart!', timer: 1500, showConfirmButton: false });
                selectedExtras = []; selectedBreadType = null;
                document.getElementById('selectedBreadType').textContent = 'None';
                updateTotal();
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong.' });
            }
        });
    }

    document.getElementById('quantity').addEventListener('input', updateTotal);
    window.onload = () => {
        renderStandardIngredients(currentBread.id);
        updateTotal();
    };

    function openModal() {
        document.getElementById("customizeModal").style.display = "block";
    }

    function closeModal() {
        document.getElementById("customizeModal").style.display = "none";
    }

    // Close modal if clicked outside content
    window.onclick = function(event) {
        const modal = document.getElementById("customizeModal");
        if (event.target === modal) {
            modal.style.display = "none";
        }
    }

</script>
</body>
</html>
