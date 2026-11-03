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
// Fetch Existing Event by ID
// =====================================
$event = null;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM events WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => (int)$_GET['id']]);
        $event = $stmt->fetch();
    } catch (PDOException $e) {
        $event = null;
    }
}

// Redirect if event not found
if (!$event) {
    header('Location: dashboard.php?msg=error');
    exit;
}

// =====================================
// Handle Update Event (POST)
// =====================================
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category    = trim($_POST['category'] ?? '');
    $location    = trim($_POST['location'] ?? '');
    $event_date  = trim($_POST['event_date'] ?? '');
    $title_en       = trim($_POST['title_en'] ?? '');
    $description_en = trim($_POST['description_en'] ?? '');
    $category_en    = $category; // Same ID
    $location_en    = $location; // Same ID

    // Validate required fields
    if ($title === '' || $title_en === '' || $category === '' || $category_en === '' || $location === '' || $location_en === '' || $event_date === '') {
        $error = $lang['admin_required_fields'];
    } else {
        // Default: keep existing image
        $image = $event['image'];

        // If new image uploaded, replace it
        if (!empty($_FILES['image']['name'])) {
            $target_dir = '../assets/img/events/';
            $image = time() . '_' . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . $image);
        }

        // Update database
        try {
            $stmt = $pdo->prepare(
                "UPDATE events 
                 SET title = :t, description = :d, category = :c, location = :l, event_date = :ed, image = :img,
                     title_en = :te, description_en = :de, category_en = :ce, location_en = :le
                 WHERE id = :id"
            );
            $stmt->execute([
                ':t'   => $title,
                ':d'   => $description,
                ':c'   => $category,
                ':l'   => $location,
                ':ed'  => $event_date,
                ':img' => $image,
                ':te'  => $title_en,
                ':de'  => $description_en,
                ':ce'  => $category_en,
                ':le'  => $location_en,
                ':id'  => $event['id'],
            ]);

            // Redirect with success
            header('Location: dashboard.php?msg=updated');
            exit;
        } catch (PDOException $e) {
            $error = $lang['admin_error'] . ': ' . $e->getMessage();
        }
    }
}

// Language switcher URL
$new_lang = ($lang_code === 'ar') ? 'en' : 'ar';
$switch_url = 'edit_event.php?id=' . $event['id'] . '&lang=' . $new_lang;
?>

<!DOCTYPE html>
<html lang="<?php echo $lang_code; ?>" dir="<?php echo $direction; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Title & Icon -->
    <title>SVU Events Guide | <?php echo $lang['admin_edit_event']; ?></title>
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
            max-width: 700px;
            margin: 30px auto;
            padding: 0 20px;
        }

        /* Form Card */
        .form-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        }

        .form-card h1 {
            color: var(--primary-color);
            margin-bottom: 25px;
        }

        /* Form Elements */
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
            font-family: inherit;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent-color);
        }

        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }

        /* Current Image Preview */
        .current-image {
            margin-bottom: 10px;
            padding: 10px;
            background: #F9FAFB;
            border-radius: 5px;
        }

        .current-image img {
            max-height: 100px;
            border-radius: 5px;
        }

        .current-image span {
            display: block;
            color: var(--text-secondary);
            font-size: 0.85rem;
            margin-top: 5px;
        }

        /* Form Actions */
        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        .btn-save {
            background: var(--primary-color);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-family: inherit;
            font-size: 1rem;
            transition: background 0.3s;
        }

        .btn-save:hover { background: var(--accent-color); }

        .btn-cancel {
            background: #e5e7eb;
            color: #374151;
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.3s;
        }

        .btn-cancel:hover { background: #d1d5db; }

        /* Error Message */
        .error-msg {
            background: #FEE2E2;
            color: #991B1B;
            padding: 12px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 0.9rem;
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
            <a href="dashboard.php"><?php echo $lang['admin_dashboard']; ?></a>
            <a href="logout.php"><?php echo $lang['admin_logout']; ?></a>
        </div>
    </div>

    <!-- ===================================== -->
    <!-- Main Content                          -->
    <!-- ===================================== -->
    <div class="container">
        <div class="form-card">
            <!-- Page Title -->
            <h1><?php echo $lang['admin_edit_event']; ?></h1>

            <!-- Error Display -->
            <?php if ($error): ?>
                <div class="error-msg"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Edit Event Form -->
            <form method="POST" enctype="multipart/form-data" action="edit_event.php?id=<?php echo $event['id']; ?>">
                <div class="form-group">
                    <label><?php echo $lang['admin_title']; ?> *</label>
                    <input type="text" name="title" class="form-control" required
                           value="<?php echo htmlspecialchars($event['title']); ?>">
                </div>

                <div class="form-group">
                    <label><?php echo $lang['admin_description']; ?></label>
                    <textarea name="description" class="form-control"><?php echo htmlspecialchars($event['description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label><?php echo $lang['admin_category']; ?> *</label>
                    <select name="category" class="form-control" required>
                        <option value="">-- <?php echo $lang['admin_category']; ?> --</option>
                        <?php
                        $cats = $pdo->query("SELECT * FROM categories ORDER BY name_ar ASC")->fetchAll();
                        foreach ($cats as $c):
                            $selected = ($event['category'] == $c['id']) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $c['id']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($c['name_ar']); ?> | <?php echo htmlspecialchars($c['name_en']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label><?php echo $lang['admin_location']; ?> *</label>
                    <select name="location" class="form-control" required>
                        <option value="">-- <?php echo $lang['admin_location']; ?> --</option>
                        <?php
                        $locs = $pdo->query("SELECT * FROM locations ORDER BY name_ar ASC")->fetchAll();
                        foreach ($locs as $l):
                            $selected = ($event['location'] == $l['id']) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $l['id']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($l['name_ar']); ?> | <?php echo htmlspecialchars($l['name_en']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label><?php echo $lang['admin_title_en']; ?> *</label>
                    <input type="text" name="title_en" class="form-control" required
                           value="<?php echo htmlspecialchars($event['title_en'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label><?php echo $lang['admin_description_en']; ?></label>
                    <textarea name="description_en" class="form-control"><?php echo htmlspecialchars($event['description_en'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label><?php echo $lang['admin_date']; ?> *</label>
                    <input type="date" name="event_date" class="form-control" required
                           value="<?php echo $event['event_date']; ?>">
                </div>

                <div class="form-group">
                    <label><?php echo $lang['admin_image']; ?></label>
                    <!-- Current Image Preview -->
                    <?php if ($event['image']): ?>
                        <div class="current-image">
                            <img src="../assets/img/events/<?php echo htmlspecialchars($event['image']); ?>" alt="Current">
                            <span><?php echo $lang['admin_current_image']; ?>: <?php echo htmlspecialchars($event['image']); ?></span>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>

                <!-- Form Buttons -->
                <div class="form-actions">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> <?php echo $lang['admin_save']; ?>
                    </button>
                    <a href="dashboard.php" class="btn-cancel">
                        <i class="fas fa-times"></i> <?php echo $lang['admin_cancel']; ?>
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>