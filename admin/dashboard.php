<?php
// =====================================
// Initialize System
// =====================================
require_once '../config/db.php';
session_start(); // Default session timeout: 24 minutes (1440 seconds) as per PHP settings

// =====================================
// Auth Check
// =====================================
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

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
// Fetch All Events
// =====================================
try {
    $stmt = $pdo->query("SELECT * FROM events ORDER BY event_date DESC");
    $events = $stmt->fetchAll();
} catch (PDOException $e) {
    $events = [];
}

// Language switcher URL
$new_lang = ($lang_code === 'ar') ? 'en' : 'ar';
$switch_url = 'dashboard.php?lang=' . $new_lang;
?>

<!DOCTYPE html>
<html lang="<?php echo $lang_code; ?>" dir="<?php echo $direction; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Title & Icon -->
    <title><?php echo $lang['nav_admin']; ?> | <?php echo $lang['site_title']; ?></title>
    <link rel="icon" href="../assets/img/SVU-Events-icon.png" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

    <!-- Custom CSS (Page Specific) -->
    <style>
        :root {
            --primary-color: #071A39;
            --accent-color: #506382;
            --text-secondary: #6B7280;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
        }

        /* Top Bar */
        .top-bar {
            background: var(--primary-color);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .top-bar h2 { font-size: 1.2rem; }

        .top-bar-actions { display: flex; gap: 15px; align-items: center; }

        .top-bar-actions a {
            color: white;
            text-decoration: none;
            padding: 6px 14px;
            border-radius: 4px;
            border: 1px solid white;
            transition: background 0.3s;
            font-size: 0.9rem;
            font-family: inherit;
        }

        .top-bar-actions a:hover {
            background: var(--accent-color);
            border-color: var(--accent-color);
        }

        /* Container */
        .container {
            max-width: 1100px;
            margin: 30px auto;
            padding: 0 20px;
        }

        /* Dashboard Header */
        .dash-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .dash-header h1 {
            color: var(--primary-color);
            font-size: 1.8rem;
        }

        .btn-add {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--primary-color);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 600;
            font-family: inherit;
            transition: background 0.3s;
        }

        .btn-add:hover { background: var(--accent-color); }

        /* Messages */
        .message {
            padding: 12px 18px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }

        .message.success {
            background: #D1FAE5;
            color: #065F46;
            border: 1px solid #A7F3D0;
        }

        .message.error {
            background: #FEE2E2;
            color: #991B1B;
            border: 1px solid #FECACA;
        }

        /* Table Container */
        .table-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background: var(--primary-color);
            color: white;
            padding: 14px 15px;
            text-align: <?php echo ($lang_code === 'ar') ? 'right' : 'left'; ?>;
            font-size: 0.9rem;
            font-weight: 600;
        }

        tbody td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            font-size: 0.9rem;
        }

        tbody tr:hover td {
            background: #F9FAFB;
        }

        /* Action Buttons */
        .actions {
            display: flex;
            gap: 8px;
        }

        .btn-edit, .btn-delete {
            padding: 5px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 600;
            font-family: inherit;
            border: none;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-edit {
            background: #E0F2FE;
            color: #0369A1;
        }

        .btn-edit:hover { background: #BAE6FD; }

        .btn-delete {
            background: #FEE2E2;
            color: #991B1B;
        }

        .btn-delete:hover { background: #FECACA; }

        /* No Events */
        .no-events {
            text-align: center;
            padding: 50px;
            color: var(--text-secondary);
            font-size: 1.1rem;
        }

        /* Footer */
        .dash-footer {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            color: var(--text-secondary);
            font-size: 0.85rem;
        }
    </style>
</head>

<body>
    <!-- ===================================== -->
    <!-- Top Navigation Bar                     -->
    <!-- ===================================== -->
    <div class="top-bar">
        <h2>SVU Events Guide</h2>
        <div class="top-bar-actions">
            <a href="<?php echo $switch_url; ?>"><?php echo $lang['admin_lang_switch']; ?></a>
            <a href="../index.php"><i class="fas fa-home"></i> <?php echo $lang['nav_home']; ?></a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> <?php echo $lang['admin_logout']; ?></a>
        </div>
    </div>

    <!-- ===================================== -->
    <!-- Main Content                          -->
    <!-- ===================================== -->
    <div class="container">
        <!-- Dashboard Header -->
        <div class="dash-header">
            <h1><?php echo $lang['admin_manage']; ?></h1>
            <a href="add_event.php" class="btn-add">
                <i class="fas fa-plus"></i> <?php echo $lang['admin_add_event']; ?>
            </a>
        </div>

        <!-- System Messages -->
        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'added'): ?>
            <div class="message success"><i class="fas fa-check-circle"></i> <?php echo $lang['admin_event_added']; ?></div>
        <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
            <div class="message success"><i class="fas fa-check-circle"></i> <?php echo $lang['admin_event_updated']; ?></div>
        <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
            <div class="message success"><i class="fas fa-check-circle"></i> <?php echo $lang['admin_event_deleted']; ?></div>
        <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'error'): ?>
            <div class="message error"><i class="fas fa-exclamation-circle"></i> <?php echo $lang['admin_error']; ?></div>
        <?php endif; ?>

        <!-- Events Table -->
        <div class="table-container">
            <?php if (count($events) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo $lang['admin_title']; ?></th>
                            <th><?php echo $lang['admin_category']; ?></th>
                            <th><?php echo $lang['admin_location']; ?></th>
                            <th><?php echo $lang['admin_date']; ?></th>
                            <th><?php echo $lang['admin_actions']; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>
                            <tr>
                                <td><?php echo $event['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($event['title']); ?></strong>
                                    <?php if (!empty($event['title_en'])): ?>
                                        <br><small style="color:#999;"><?php echo htmlspecialchars($event['title_en']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($event['category']); ?></td>
                                <td><?php echo htmlspecialchars($event['location']); ?></td>
                                <td><?php echo $event['event_date']; ?></td>
                                <td class="actions">
                                    <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="btn-edit">
                                        <i class="fas fa-edit"></i> <?php echo $lang['admin_edit']; ?>
                                    </a>
                                    <a href="delete_event.php?id=<?php echo $event['id']; ?>" class="btn-delete"
                                       onclick="return confirm('<?php echo $lang['admin_confirm_delete']; ?>')">
                                        <i class="fas fa-trash"></i> <?php echo $lang['admin_delete']; ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <!-- No Events Placeholder -->
                <div class="no-events">
                    <i class="fas fa-calendar-times" style="font-size: 3rem; color: #ccc; display: block; margin-bottom: 15px;"></i>
                    <?php echo $lang['admin_no_events']; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <div class="dash-footer">
            &copy; <?php echo date('Y'); ?> SVU Events Guide - Admin Panel
        </div>
    </div>

    <!-- Custom JS (Page Specific) -->
    <script>
        // =====================================
        // Auto-hide system messages
        // =====================================
        setTimeout(function() {
            var msg = document.querySelector('.message');
            if (msg) {
                msg.style.transition = 'opacity 0.5s ease';
                msg.style.opacity = '0';
                setTimeout(function() { msg.remove(); }, 500);
            }
        }, 5000); // Hide after 5 seconds
    </script>
</body>
</html>