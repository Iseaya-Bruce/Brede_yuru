<?php
require 'includes/config.php';
require 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (name, phone, password) VALUES (?, ?, ?)");
    $stmt->execute([$name, $phone, $password]);

    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Brede Yuru - Register</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    /* Global settings */
    body {
        margin: 0;
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(90deg, #73f879ff, rgba(78, 75, 250, 1));
        overflow-x: hidden;
    }
    .emoji {
        position: fixed;
        font-size: 2.5em;
        pointer-events: none;
        opacity: 0.8;
        animation: float 12s linear infinite;
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

    @keyframes float {
        0% {
            transform: translateY(100vh) rotate(0deg);
            opacity: 0;
        }
        50% {
            opacity: 1;
        }
        100% {
            transform: translateY(-120vh) rotate(360deg);
            opacity: 0;
        }
    }

    /* Register container */
    .register-container {
        position: relative;
        z-index: 2;
        max-width: 400px;
        margin: 120px auto;
        background: #fff;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        text-align: center;
        animation: fadeInUp 1s ease-out;
    }
    .register-container h2 {
        color: #ffc107;
        margin-bottom: 20px;
        font-size: 2em;
    }
    .register-container input,
    .register-container button {
        width: 100%;
        margin: 10px 0;
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 1.1em;
    }
    .register-container button {
        background: linear-gradient(135deg, #ff6f61, #ffc107);
        color: white;
        border: none;
        cursor: pointer;
        transition: 0.3s ease;
    }
    .register-container button:hover {
        background: linear-gradient(135deg, #ffc107, #ff6f61);
        transform: translateY(-2px);
    }
    .register-container p a {
        color: #ffc107;
        text-decoration: none;
    }
    .register-container p a:hover {
        text-decoration: underline;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Responsive design for mobile */
    @media screen and (max-width: 567px) {
        .register-container {
            margin: 60px 15px;
            padding: 20px;
        }
        .register-container h2 {
            font-size: 1.8em;
        }
        .register-container input,
        .register-container button {
            font-size: 1em;
            padding: 10px;
        }
    }
</style>

</head>
<body>

<?php
// Generate 20 floating emojis at random positions
$emojis = ['🥪', '🥬', '🍅', '🧀', '🥓', '🍞'];
for ($i = 0; $i < 20; $i++):
    $emoji = $emojis[array_rand($emojis)];
    $left = rand(0, 100);
    $delay = rand(0, 10);
    $size = rand(20, 40) / 10; // Scale from 2.0em to 4.0em
?>
    <div class="emoji" style="left:<?= $left ?>%; animation-delay:<?= $delay ?>s; font-size:<?= $size ?>em;">
        <?= $emoji ?>
    </div>
<?php endfor; ?>

<div class="register-container">
    <h2>Create Your Account</h2>
    <form method="POST">
        <input type="text" name="name" placeholder="Full Name" required><br>
        <input type="text" name="phone" placeholder="Phone Number" required><br>
        <input type="password" name="password" placeholder="Create Password" required><br>
        <button type="submit">Register</button>
    </form>
    <p style="margin-top: 10px;">Already have an account? <a href="login.php">Login here</a></p>
    <p>
        <a href="index.php" class="floating-btn" title="Back to Dashboard">
        <i class="fas fa-arrow-left"></i>
    </a>
    </p>
</div>

</body>
</html>
