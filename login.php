<?php
require 'includes/config.php';
require 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = $_POST['identifier']; // name or phone
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE phone = ? OR name = ?");
    $stmt->execute([$identifier, $identifier]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['is_admin'] = $user['is_admin'];

        if ($user['is_admin'] == 1) {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: dashboard.php");
        }
        exit;
    } else {
        $error = "Incorrect name/phone or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Brede Yuru - Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
   <style>
    body {
        margin: 0;
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(90deg, #73f879ff, rgba(78, 75, 250, 1));
        overflow: hidden;
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
    .login-container {
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
    .login-container h2 {
        color: #ffc107;
        margin-bottom: 20px;
        font-size: 2em;
    }
    .login-container input,
    .login-container button {
        width: 100%;
        margin: 10px 0;
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 1.1em;
    }
    .login-container button {
        background: linear-gradient(135deg, #ff6f61, #ffc107);
        color: white;
        border: none;
        cursor: pointer;
        transition: 0.3s ease;
    }
    .login-container button:hover {
        background: linear-gradient(135deg, #ffc107, #ff6f61);
        transform: translateY(-2px);
    }
    .show-password {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #ffc107;
        font-weight: bold;
    }
    .login-container .error {
        color: red;
        margin: 10px 0;
        font-size: 0.95em;
    }
    .login-container p a {
        color: #ffc107;
        text-decoration: none;
    }
    .login-container p a:hover {
        text-decoration: underline;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
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

<div class="login-container">
    <h2>Login to Brede Yuru 🥪</h2>
    <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
    <form method="POST">
        <input type="text" name="identifier" placeholder="Name or Phone" required><br>
        <div class="password-field" style="position:relative;">
            <input type="password" name="password" id="password" placeholder="Password" required>
            <span class="show-password" onclick="togglePassword()">Show</span>
        </div><br>
        <button type="submit">Login</button>
    </form>
    <p>No account? <a href="register.php">Register</a></p>
    <p>
        <a href="index.php" class="floating-btn" title="Back to Dashboard">
        <i class="fas fa-arrow-left"></i>
    </a>
    </p>
</div>

<script>
function togglePassword() {
    const pwd = document.getElementById('password');
    const toggle = document.querySelector('.show-password');
    if (pwd.type === 'password') {
        pwd.type = 'text';
        toggle.textContent = 'Hide';
    } else {
        pwd.type = 'password';
        toggle.textContent = 'Show';
    }
}
</script>

</body>
</html>
