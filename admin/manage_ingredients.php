<?php
require '../includes/config.php';
require '../includes/functions.php';
require '../includes/auth.php'; // <-- loads redirectIfNotAdmin()
redirectIfNotAdmin();

$success = null;

// Handle adding a new ingredient
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_ingredient'])) {
    $name = trim($_POST['name']);
    $is_standard = isset($_POST['is_standard']) ? 1 : 0;

    if ($name !== "") {
        $photo_path = null;

        if (!empty($_FILES['photo']['name'])) {
            $target_dir = "../uploads/ingredients/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

            $photo_name = uniqid() . "_" . basename($_FILES['photo']['name']);
            $target_file = $target_dir . $photo_name;

            if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
                $photo_path = "uploads/ingredients/" . $photo_name;
            }
        }

        $stmt = $pdo->prepare("INSERT INTO ingredients (name, is_standard, image_path, is_active) VALUES (?, ?, ?, 1)");
        $stmt->execute([$name, $is_standard, $photo_path]);

        $success = "✅ New ingredient added successfully!";
    } else {
        $success = "⚠️ Ingredient name cannot be empty.";
    }
}

// Save ingredient assignments to breads
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bread_id']) && isset($_POST['save_ingredients'])) {
    $bread_id = intval($_POST['bread_id']);
    $assigned_ingredients = $_POST['ingredients'] ?? [];

    $stmt = $pdo->prepare("DELETE FROM bread_ingredients WHERE bread_id = ?");
    $stmt->execute([$bread_id]);

    $stmt = $pdo->prepare("INSERT INTO bread_ingredients (bread_id, ingredient_id) VALUES (?, ?)");
    foreach ($assigned_ingredients as $ing_id) {
        $stmt->execute([$bread_id, $ing_id]);
    }

    $success = "✅ Ingredients updated for this bread!";
}

// Get all breads
$breads = $pdo->query("SELECT * FROM breads ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

// Get all ingredients
$ingredients = $pdo->query("SELECT * FROM ingredients ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// If editing specific bread
$editBread = null;
$editBreadIngredients = [];
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editBreadId = intval($_GET['edit']);
    $stmt = $pdo->prepare("SELECT * FROM breads WHERE id = ?");
    $stmt->execute([$editBreadId]);
    $editBread = $stmt->fetch();

    $stmt = $pdo->prepare("SELECT ingredient_id FROM bread_ingredients WHERE bread_id = ?");
    $stmt->execute([$editBreadId]);
    $editBreadIngredients = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'ingredient_id');
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Manage Ingredients - Brede Yuru Admin</title>
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
        .add-ingredient-form {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 25px;
        }
        .add-ingredient-form h3 {
            color: #ffc107;
            margin-bottom: 15px;
        }
        .add-ingredient-form input, .add-ingredient-form select, .add-ingredient-form button {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1em;
        }
        .add-ingredient-form button {
            background: #ff6f61;
            color: #fff;
            font-weight: bold;
        }
        .add-ingredient-form button:hover {
            background: #e85d4f;
        }
        .bread-list, .ingredient-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        .bread-card, .ingredient-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.1);
            padding: 15px;
            text-align: center;
            transition: transform 0.3s ease;
        }
        .bread-card:hover, .ingredient-card:hover {
            transform: translateY(-4px);
        }
        .bread-card h4, .ingredient-card h4 {
            margin-bottom: 10px;
            color: #ffc107;
        }
        .ingredient-card img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 10px;
        }
        .checkbox label {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .save-btn {
            display: block;          /* make it block so it fills width properly */
            width: 100%;
            background: #28a745;
            color: #fff;
            font-weight: bold;
            border: none;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
            text-align: center;      /* center text inside the button */
            box-sizing: border-box;  /* include padding in width */
            margin-top: 10px;        /* spacing from price or other elements */
            text-decoration: none;   /* remove underline for <a> */
        }

        .save-btn:hover {
            background: #218838;
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
        <h2>Manage Ingredients 🥬</h2>
    </div>

    <div class="container">
        <?php if ($success): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>

        <!-- Add New Ingredient -->
        <div class="add-ingredient-form">
            <h3>Add New Ingredient</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="text" name="name" placeholder="Ingredient Name" required>
                <select name="is_standard">
                    <option value="1">Standard Ingredient</option>
                    <option value="0">Extra Ingredient</option>
                </select>
                <input type="file" name="photo" accept="image/*" onchange="previewImage(event, 'editPreview<?= $bread['id'] ?>')">
                <button type="submit" name="add_ingredient">Add Ingredient</button>
            </form>
        </div>

        <?php if (!$editBread): ?>
            <h3>Assign Ingredients to Breads</h3>
            <div class="bread-list">
                <?php foreach ($breads as $bread): ?>
                <div class="bread-card">
                    <h4><?= htmlspecialchars($bread['name']) ?></h4>
                    <p>SRD <?= number_format($bread['price'], 2) ?></p>
                    <a class="save-btn" href="?edit=<?= $bread['id'] ?>">Edit Ingredients</a>
                </div>
                <?php endforeach; ?>
            </div>
            <p>
                <a href="dashboard.php" class="floating-btn" title="Back to Dashboard">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </p>
        <?php else: ?>
            <h3>Edit Ingredients for: <?= htmlspecialchars($editBread['name']) ?></h3>
            <form method="POST">
                <input type="hidden" name="bread_id" value="<?= $editBread['id'] ?>">
                <div class="ingredient-list">
                    <?php foreach ($ingredients as $ing): ?>
                    <div class="ingredient-card">
                        <img src="../<?= $ing['image_path'] ?: 'assets/img/placeholder.png' ?>" alt="<?= htmlspecialchars($ing['name']) ?>">
                        <h4><?= htmlspecialchars($ing['name']) ?></h4>
                        <label>
                            <input type="checkbox" name="ingredients[]" value="<?= $ing['id'] ?>"
                                <?= in_array($ing['id'], $editBreadIngredients) ? 'checked' : '' ?>>
                            Add to Bread
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="submit" name="save_ingredients" class="save-btn">Save Ingredients</button>
            </form>
            <p>
                <a href="manage_ingredients.php" class="floating-btn" title="Back to Dashboard">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </p>
        <?php endif; ?>
    </div>
</body>
</html>
