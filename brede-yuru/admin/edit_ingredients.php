<?php
require '../includes/config.php';
require '../includes/functions.php';
require '../includes/auth.php'; // <-- loads redirectIfNotAdmin()
redirectIfNotAdmin();

if (!$_SESSION['is_admin']) {
    echo "Access denied.";
    exit;
}

// Handle updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $is_standard = isset($_POST['is_standard']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $stmt = $pdo->prepare("UPDATE ingredients SET name=?, price=?, is_standard=?, is_active=? WHERE id=?");
    $stmt->execute([$name, $price, $is_standard, $is_active, $id]);
}

// At the top of breads.php/ingredients.php
if (isset($_POST['add_new'])) {
    $pdo->prepare("INSERT INTO breads (name, price) VALUES ('New Bread', 10.00)")->execute();
    header("Location: breads.php"); // refresh
}


// List all ingredients
$ingredients = $pdo->query("SELECT * FROM ingredients")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Manage Ingredients</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<h2>Ingredients</h2>
<a href="dashboard.php">← Back</a>

<?php foreach ($ingredients as $ing): ?>
    <form method="POST" style="border:1px solid #ccc; padding:10px; margin:10px;">
        <input type="hidden" name="id" value="<?= $ing['id'] ?>">
        <strong>ID: <?= $ing['id'] ?></strong><br>
        Name: <input type="text" name="name" value="<?= htmlspecialchars($ing['name']) ?>"><br>
        Price: SRD <input type="number" step="0.01" name="price" value="<?= $ing['price'] ?>"><br>
        Standard: <input type="checkbox" name="is_standard" <?= $ing['is_standard'] ? 'checked' : '' ?>><br>
        Active: <input type="checkbox" name="is_active" <?= $ing['is_active'] ? 'checked' : '' ?>><br>
        <button type="submit">Save</button>
    </form>
<?php endforeach; ?>

</body>
</html>
