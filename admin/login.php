<?php
// =====================================
// Initialize System
// =====================================
require_once '../config/db.php';
session_start(); // Default session timeout: 24 minutes (1440 seconds) as per PHP settings

// =====================================
// Language Detection
// =====================================
// Check URL parameter first, then cookie, default to Arabic
if (isset($_GET['lang']) && in_array($_GET['lang'], ['ar', 'en'])) {
    $lang_code = $_GET['lang'];
    setcookie('site_lang', $lang_code, time() + (86400 * 30), '/'); // 30 days
} elseif (isset($_COOKIE['site_lang'])) {
    $lang_code = $_COOKIE['site_lang'];
} else {
    $lang_code = 'ar';
}
$direction = ($lang_code === 'ar') ? 'rtl' : 'ltr';

// Load language file
if ($lang_code === 'en') {
    $lang = require_once '../languages/en.php';
} else {
    $lang = require_once '../languages/ar.php';
}

// =====================================
// Redirect if already logged in
// =====================================
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

// =====================================
// Handle Login
// =====================================
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = $lang['admin_required_fields'];
    } else {
        // Check credentials
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        if ($user && $password === $user['password']) {
            // Login successful
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $user['username'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = $lang['admin_invalid_login'];
        }
    }
}

// Language switch URL
$new_lang = ($lang_code === 'ar') ? 'en' : 'ar';
$switch_url = 'login.php?lang=' . $new_lang;
?>

<!DOCTYPE html>
<html lang="<?php echo $lang_code; ?>" dir="<?php echo $direction; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Title & Icon -->
    <title>SVU Events Guide | <?php echo $lang['admin_login_title']; ?></title>
    <link rel="icon" href="../assets/img/SVU-Events-icon.png" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

    <!-- Custom CSS (Page Specific) -->
    <style>
        :root {
            --primary-color: #071A39;
            --accent-color: #506382;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: sans-serif;
            background: #f0f2f5;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .login-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h2 {
            color: var(--primary-color);
            margin-bottom: 5px;
        }

        .login-header p { color: #6B7280; font-size: 0.9rem; }

        .form-group { margin-bottom: 20px; }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--primary-color);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .form-control:focus { outline: none; border-color: var(--accent-color); }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-login:hover { background: var(--accent-color); }

        .error-msg {
            background: #FEE2E2;
            color: #DC2626;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .lang-switch {
            position: absolute;
            top: 20px;
            left: 20px;
            background: var(--primary-color);
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .lang-switch:hover { background: var(--accent-color); }

        .home-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--accent-color);
            text-decoration: none;
        }

        .home-link:hover { color: var(--primary-color); }
    </style>
</head>

<body>
    <!-- Language Switcher -->
    <a href="<?php echo $switch_url; ?>" class="lang-switch"><?php echo $lang['admin_lang_switch']; ?></a>

    <!-- Login Form -->
    <div class="login-container">
        <div class="login-header">
            <h2>SVU Events Guide</h2>
            <p><?php echo $lang['admin_login_title']; ?></p>
        </div>

        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username"><?php echo $lang['admin_username']; ?></label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password"><?php echo $lang['admin_password']; ?></label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn-login"><?php echo $lang['admin_login_btn']; ?></button>
        </form>

        <a href="../index.php" class="home-link"><i class="fas fa-home"></i> <?php echo $lang['nav_home']; ?></a>
    </div>
</body>
</html>