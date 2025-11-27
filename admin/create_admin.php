<?php
require '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if admin already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE is_admin = 1 LIMIT 1");
    $stmt->execute();
    $existingAdmin = $stmt->fetch();

    if ($existingAdmin) {
        $error = "An admin account already exists!";
    } else {
        // Insert admin user
        $stmt = $pdo->prepare("INSERT INTO users (name, phone, password, is_admin) VALUES (?, ?, ?, 1)");
        $stmt->execute([$name, $phone, $password]);
        $success = "Admin account created successfully!";
        header("refresh:3;url=login.php"); // Redirect to login after 3 seconds
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Admin - Brede Yuru</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .create-admin-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        .create-admin-container h2 {
            color: #007b5e;
        }
    </style>
</head>
<body>
    <div class="create-admin-container">
        <h2>Create Admin Account</h2>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p style='color:green;'>$success Redirecting to login...</p>"; ?>
        <?php if (!isset($success)): ?>
        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" required><br><br>
            <input type="text" name="phone" placeholder="Phone Number" required><br><br>
            <input type="password" name="password" placeholder="Password" required><br><br>
            <button type="submit">Create Admin</button>
        </form>
        <?php endif; ?>
        <p><a href="index.php">← Back to home</a></p>
    </div>
</body>
</html>
