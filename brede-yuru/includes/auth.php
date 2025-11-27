<?php
/**
 * Authentication Helper Functions for Brede Yuru
 * Handles user authentication, session management, and security
 */

require_once 'functions.php'; // ✅ Pull in isLoggedIn() and redirectIfNotLoggedIn()
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if current user is an admin
 * @return bool
 */
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

/**
 * Redirect non-admin users away from admin-only pages
 */
function redirectIfNotAdmin() {
    if (!isLoggedIn() || !isAdmin()) {
        header("Location: ../login.php");
        exit;
    }
}

/**
 * Attempt to log in a user
 * @param string $phone
 * @param string $password
 * @return bool True if login successful
 */
function attemptLogin($phone, $password) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT id, name, password, is_admin FROM users WHERE phone = ?");
    $stmt->execute([$phone]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true); // Prevent session fixation
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['is_admin'] = (bool)$user['is_admin'];
        $_SESSION['last_activity'] = time();
        return true;
    }

    return false;
}

/**
 * Register a new user
 * @param string $name
 * @param string $phone
 * @param string $password
 * @return array [success: bool, message: string]
 */
function registerUser($name, $phone, $password) {
    global $pdo;

    if (empty($name) || empty($phone) || empty($password)) {
        return ['success' => false, 'message' => 'All fields are required.'];
    }

    if (strlen($password) < 6) {
        return ['success' => false, 'message' => 'Password must be at least 6 characters.'];
    }

    $stmt = $pdo->prepare("SELECT id FROM users WHERE phone = ?");
    $stmt->execute([$phone]);
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'Phone number already registered.'];
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, phone, password) VALUES (?, ?, ?)");
    if ($stmt->execute([$name, $phone, $hashedPassword])) {
        return ['success' => true, 'message' => 'Registration successful.'];
    } else {
        return ['success' => false, 'message' => 'Registration failed. Please try again.'];
    }
}

/**
 * Log out the current user
 */
function logout() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }
    session_destroy();
}

/**
 * Check for inactive session and logout if needed
 * @param int $timeout Seconds of inactivity (default 30 min)
 */
function checkSessionTimeout($timeout = 1800) {
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
        logout();
        header("Location: ../login.php?timeout=1");
        exit;
    }
    $_SESSION['last_activity'] = time();
}

/**
 * Get authenticated user ID
 * @return int|null
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get authenticated user name
 * @return string|null
 */
function getCurrentUserName() {
    return $_SESSION['user_name'] ?? null;
}

/**
 * Redirect to originally requested page after login
 */
function redirectAfterLogin() {
    if (!empty($_SESSION['login_redirect'])) {
        $redirect = $_SESSION['login_redirect'];
        unset($_SESSION['login_redirect']);
        header("Location: $redirect");
        exit();
    }

    if (isAdmin()) {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../templates/dashboard.php");
    }
    exit();
}
?>
