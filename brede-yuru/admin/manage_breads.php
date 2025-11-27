<?php
require '../includes/config.php';
require '../includes/functions.php';
require '../includes/auth.php'; // <-- loads redirectIfNotAdmin()
redirectIfNotAdmin();

$success = null;

// Handle adding new bread
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $photo_path = null;

    if (!empty($_FILES['photo']['name'])) {
        $target_dir = "../uploads/breads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $photo_name = uniqid() . "_" . basename($_FILES['photo']['name']);
        $target_file = $target_dir . $photo_name;
        move_uploaded_file($_FILES['photo']['tmp_name'], $target_file);
        $photo_path = "uploads/breads/" . $photo_name;
    }

    $stmt = $pdo->prepare("INSERT INTO breads (name, price, image_path, is_active) VALUES (?, ?, ?, 1)");
    $stmt->execute([$name, $price, $photo_path]);

    $success = "✅ New bread added successfully!";
}

// Handle bread updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $bread_id = $_POST['bread_id'];
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);

    $photo_path = null;
    if (!empty($_FILES['photo']['name'])) {
        $target_dir = "../uploads/breads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $photo_name = uniqid() . "_" . basename($_FILES['photo']['name']);
        $target_file = $target_dir . $photo_name;
        move_uploaded_file($_FILES['photo']['tmp_name'], $target_file);
        $photo_path = "uploads/breads/" . $photo_name;
    }

    if ($photo_path) {
        $stmt = $pdo->prepare("UPDATE breads SET name = ?, price = ?, image_path = ? WHERE id = ?");
        $stmt->execute([$name, $price, $photo_path, $bread_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE breads SET name = ?, price = ? WHERE id = ?");
        $stmt->execute([$name, $price, $bread_id]);
    }

    $success = "✅ Bread updated successfully!";
}

// Handle activate/deactivate
if (isset($_GET['toggle'])) {
    $bread_id = intval($_GET['toggle']);
    $stmt = $pdo->prepare("SELECT is_active FROM breads WHERE id = ?");
    $stmt->execute([$bread_id]);
    $bread = $stmt->fetch();

    if ($bread) {
        $new_status = $bread['is_active'] ? 0 : 1;
        $stmt = $pdo->prepare("UPDATE breads SET is_active = ? WHERE id = ?");
        $stmt->execute([$new_status, $bread_id]);
        $success = $new_status ? "✅ Bread activated!" : "🛑 Bread deactivated!";
    }
    header("Location: manage_breads.php");
    exit;
}

// Get all breads
$breads = $pdo->query("SELECT * FROM breads ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head><meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Manage Breads - Brede Yuru Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .success {
            text-align: center;
            background: #d4edda;
            color: #155724;
            padding: 10px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .bread-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
        }
        .bread-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            padding: 15px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .bread-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.2);
        }
        .bread-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 10px;
        }
        .bread-card h3 {
            color: #ffc107;
            margin-bottom: 5px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 12px;
            margin-bottom: 10px;
            font-size: 0.9em;
            color: #fff;
        }
        .active { background: #28a745; }
        .inactive { background: #dc3545; }
        form input, form button, .toggle-btn {
            width: 100%;
            margin: 5px 0;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1em;
        }
        .toggle-btn {
            background: #ffc107;
            color: #fff;
            font-weight: bold;
        }
        .toggle-btn:hover {
            background: #ff9800;
        }
        .save-btn {
            background: #ff6f61;
            color: #fff;
            font-weight: bold;
        }
        .save-btn:hover {
            background: #e85d4f;
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
    </style>
</head>
<body>
    <div class="header">
        <h2>Manage Breads 🥖</h2>
    </div>

    <div class="container">
        <?php if ($success): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>

        <h3>Add New Bread</h3>
        <div class="bread-card">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <input type="text" name="name" placeholder="Bread Name" required>
                <input type="number" name="price" step="0.01" placeholder="Price (SRD)" required>

                <input type="file" name="photo" accept="image/*" onchange="previewImage(event, 'editPreview<?= $bread['id'] ?>')">
                <img id="addPreview" src="../assets/img/placeholder.png" alt="Preview">
                <button type="submit" class="save-btn">Add Bread</button>
            </form>
        </div>

        <h3>Edit Existing Breads</h3>
        <div class="bread-grid">
            <?php foreach ($breads as $bread): ?>
            <div class="bread-card">
                <h3><?= htmlspecialchars($bread['name']) ?></h3>
                <span class="status-badge <?= $bread['is_active'] ? 'active' : 'inactive' ?>">
                    <?= $bread['is_active'] ? 'Active' : 'Inactive' ?>
                </span>
                <img src="../<?= $bread['image_path'] ?: 'assets/img/placeholder.png' ?>" alt="Bread Image">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="bread_id" value="<?= $bread['id'] ?>">
                    <input type="text" name="name" value="<?= htmlspecialchars($bread['name']) ?>" required>
                    <input type="number" name="price" step="0.01" value="<?= $bread['price'] ?>" required>
                    <input type="file" name="photo" accept="image/*" capture="environment" onchange="previewImage(event, 'editPreview<?= $bread['id'] ?>')">
                    <button type="submit" class="save-btn">Save Changes</button>
                    <a href="?toggle=<?= $bread['id'] ?>" class="toggle-btn"><?= $bread['is_active'] ? 'Deactivate' : 'Activate' ?></a>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
        <p>
            <a href="dashboard.php" class="floating-btn" title="Back to Dashboard">
                <i class="fas fa-arrow-left"></i>
            </a>
        </p>
    </div>

    <script>
    function previewImage(event, previewId) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById(previewId);
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
    </script>
</body>
</html>
