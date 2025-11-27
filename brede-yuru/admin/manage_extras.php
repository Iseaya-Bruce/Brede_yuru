<?php
require '../includes/config.php';
require '../includes/functions.php';
require '../includes/auth.php'; // <-- loads redirectIfNotAdmin()
redirectIfNotAdmin();

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $ingredient_id = $_POST['ingredient_id'] ?? null;

    $photo_path = null;
    if (!empty($_FILES['photo']['name'])) {
        $target_dir = "../uploads/ingredients/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $photo_name = uniqid() . "_" . basename($_FILES['photo']['name']);
        $target_file = $target_dir . $photo_name;
        move_uploaded_file($_FILES['photo']['tmp_name'], $target_file);
        $photo_path = "uploads/ingredients/" . $photo_name;
    }

    if ($ingredient_id) {
        // Update existing
        if ($photo_path) {
            $stmt = $pdo->prepare("UPDATE ingredients SET name = ?, price = ?, image_path = ? WHERE id = ?");
            $stmt->execute([$name, $price, $photo_path, $ingredient_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE ingredients SET name = ?, price = ? WHERE id = ?");
            $stmt->execute([$name, $price, $ingredient_id]);
        }
        $success = "✅ Ingredient updated successfully!";
    } else {
        // Insert new
        $stmt = $pdo->prepare("INSERT INTO ingredients (name, price, image_path, is_active, is_standard) VALUES (?, ?, ?, 1, 0)");
        $stmt->execute([$name, $price, $photo_path]);
        $success = "✅ New ingredient added successfully!";
    }
}

// Delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM ingredients WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    $success = "🗑️ Ingredient deleted!";
}

// Get extras
$extras = $pdo->query("SELECT * FROM ingredients WHERE is_standard = 0 ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Extra Ingredients - Brede Yuru Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="../assets/css/style.css">
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
            font-size: 2.2em;
        }
        .container {
            max-width: 1100px;
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
        .ingredient-form {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 30px;
        }
        .ingredient-form h3 {
            color: #ffc107;
            margin-bottom: 15px;
        }
        .ingredient-form input, .ingredient-form button {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1em;
        }
        .ingredient-form button {
            background: #ff6f61;
            color: #fff;
            font-weight: bold;
        }
        .ingredient-form button:hover {
            background: #e85d4f;
        }
        .extras-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 20px;
        }
        .extra-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.1);
            padding: 15px;
            text-align: center;
            transition: transform 0.3s ease;
        }
        .extra-card:hover {
            transform: translateY(-4px);
        }
        .extra-card img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
            border: 2px solid #f1f1f1;
        }
        .extra-card h4 {
            margin-bottom: 5px;
            color: #ffc107;
        }
        .extra-card p {
            margin: 5px 0;
        }
        .card-actions {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }
        .edit-btn, .delete-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            color: #fff;
            cursor: pointer;
            text-decoration: none;
        }
        .edit-btn {
            background: #007bff;
        }
        .edit-btn:hover {
            background: #0056b3;
        }
        .delete-btn {
            background: #dc3545;
        }
        .delete-btn:hover {
            background: #c82333;
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
        <h2>Manage Extra Ingredients 🥪</h2>
    </div>

    <div class="container">
        <?php if (isset($success)) echo "<div class='success'>$success</div>"; ?>

        <div class="ingredient-form">
            <h3><?= isset($editIngredient) ? "Edit Ingredient" : "Add New Ingredient" ?></h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="ingredient_id" value="<?= $editIngredient['id'] ?? '' ?>">
                <input type="text" name="name" placeholder="Ingredient Name" required value="<?= htmlspecialchars($editIngredient['name'] ?? '') ?>">
                <input type="number" name="price" step="0.01" placeholder="Price (SRD)" required value="<?= htmlspecialchars($editIngredient['price'] ?? '') ?>">

                <input type="file" name="photo" accept="image/*" onchange="previewImage(event, 'editPreview<?= $bread['id'] ?>')">
                <?php if (isset($editIngredient) && $editIngredient['image_path']): ?>
                    <p>Current Image: <img src="../<?= $editIngredient['image_path'] ?>" alt="Ingredient"></p>
                <?php endif; ?>
                <button type="submit"><?= isset($editIngredient) ? "Update Ingredient" : "Add Ingredient" ?></button>
            </form>
        </div>

        <h3>Existing Extra Ingredients</h3>
        <div class="extras-list">
            <?php foreach ($extras as $extra): ?>
            <div class="extra-card">
                <img src="../<?= $extra['image_path'] ?: 'assets/img/placeholder.png' ?>" alt="<?= htmlspecialchars($extra['name']) ?>">
                <h4><?= htmlspecialchars($extra['name']) ?></h4>
                <p>Price: SRD <?= number_format($extra['price'], 2) ?></p>
                <div class="card-actions">
                    <a href="?edit=<?= $extra['id'] ?>" class="edit-btn">Edit</a>
                    <a href="?delete=<?= $extra['id'] ?>" class="delete-btn" onclick="return confirm('Delete this ingredient?');">Delete</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <p>
            <a href="dashboard.php" class="floating-btn" title="Back to Dashboard">
                <i class="fas fa-arrow-left"></i>
            </a>
        </p>
    </div>
</body>
</html>
