<?php
// =====================================
// Initialize System
// =====================================
require_once '../config/db.php';

session_start();

// =====================================
// Auth Check
// =====================================
if (
    !isset($_SESSION['admin_logged_in']) ||
    $_SESSION['admin_logged_in'] !== true
) {
    header('Location: login.php');
    exit;
}

// =====================================
// Language Detection
// =====================================
if (
    isset($_GET['lang']) &&
    in_array($_GET['lang'], ['ar', 'en'], true)
) {
    $lang_code = $_GET['lang'];

    setcookie(
        'site_lang',
        $lang_code,
        time() + (86400 * 30),
        '/'
    );

} elseif (
    isset($_COOKIE['site_lang']) &&
    in_array($_COOKIE['site_lang'], ['ar', 'en'], true)
) {
    $lang_code = $_COOKIE['site_lang'];

} else {
    $lang_code = 'ar';
}

$direction = ($lang_code === 'ar') ? 'rtl' : 'ltr';

// Load language file
$lang = require (
    $lang_code === 'en'
        ? '../languages/en.php'
        : '../languages/ar.php'
);

// =====================================
// Initialize Variables
// =====================================
$error = '';

$title = '';
$description = '';
$category = '';
$location = '';
$event_date = '';
$image = '';
$title_en = '';
$description_en = '';
$category_en = '';
$location_en = '';

// =====================================
// Handle Add Event (POST)
// =====================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get form inputs
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    $category = trim($_POST['category'] ?? '');
    $location = trim($_POST['location'] ?? '');

    $event_date = trim($_POST['event_date'] ?? '');

    // Image URL instead of file upload
    $image = trim($_POST['image'] ?? '');

    $title_en = trim($_POST['title_en'] ?? '');
    $description_en = trim($_POST['description_en'] ?? '');

    // Get English category and location names
    try {

        $stmt = $pdo->prepare("
            SELECT name_en
            FROM categories
            WHERE id = :id
        ");

        $stmt->execute([':id' => $category]);

        $category_en = $stmt->fetchColumn();

        $stmt = $pdo->prepare("
            SELECT name_en
            FROM locations
            WHERE id = :id
        ");

        $stmt->execute([':id' => $location]);

        $location_en = $stmt->fetchColumn();

    } catch (PDOException $e) {

        $error = $lang['admin_error'];
    }

    // =====================================
    // Validate Required Fields
    // =====================================
    if ($error === '') {

        if (
            $title === '' ||
            $title_en === '' ||
            $category === '' ||
            $location === '' ||
            $event_date === ''
        ) {
            $error = $lang['admin_required_fields'];
        }

        // Validate image URL if provided
        elseif (
            $image !== '' &&
            (
                !filter_var($image, FILTER_VALIDATE_URL) ||
                !in_array(
                    strtolower(parse_url($image, PHP_URL_SCHEME) ?? ''),
                    ['http', 'https'],
                    true
                )
            )
        ) {
            $error = 'Please enter a valid image URL.';
        }

        // Validate event date
        elseif (
            !DateTime::createFromFormat('Y-m-d', $event_date) ||
            DateTime::createFromFormat('Y-m-d', $event_date)->format('Y-m-d') !== $event_date
        ) {
            $error = 'Please enter a valid event date.';
        }
    }

    // =====================================
    // Insert Event
    // =====================================
    if ($error === '') {

        try {

            // Start transaction
            $pdo->beginTransaction();

            // Generate unique event ID
            $updateCounter = $pdo->prepare("
                UPDATE event_id_counter
                SET next_id = next_id + 1
                WHERE counter_name = 'events'
            ");

            $updateCounter->execute();

            if ($updateCounter->rowCount() !== 1) {
                throw new Exception(
                    'Event ID counter was not initialized.'
                );
            }

            // Get generated ID
            $stmt = $pdo->prepare("
                SELECT next_id
                FROM event_id_counter
                WHERE counter_name = 'events'
            ");

            $stmt->execute();

            $newId = $stmt->fetchColumn();

            if ($newId === false) {
                throw new Exception('Unable to generate event ID.');
            }

            // Insert event into database
            $stmt = $pdo->prepare("
                INSERT INTO events (
                    id,
                    title,
                    description,
                    category,
                    location,
                    event_date,
                    image,
                    title_en,
                    description_en,
                    category_en,
                    location_en
                )
                VALUES (
                    :id,
                    :title,
                    :description,
                    :category,
                    :location,
                    :event_date,
                    :image,
                    :title_en,
                    :description_en,
                    :category_en,
                    :location_en
                )
            ");

            $stmt->execute([

                ':id' => $newId,

                ':title' => $title,
                ':description' => $description,

                ':category' => $category,
                ':location' => $location,

                ':event_date' => $event_date,

                // Save URL directly
                ':image' => ($image !== '') ? $image : null,

                ':title_en' => $title_en,
                ':description_en' => $description_en,

                ':category_en' => $category_en,
                ':location_en' => $location_en
            ]);

            // Commit transaction
            $pdo->commit();

            // Redirect after success
            header('Location: dashboard.php?msg=added');
            exit;

        } catch (Throwable $e) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            error_log($e->getMessage());

            $error = $lang['admin_error'] . ': ' . $e->getMessage();
        }
    }
}

// =====================================
// Language Switcher
// =====================================
$new_lang = ($lang_code === 'ar') ? 'en' : 'ar';

$switch_url = 'add_event.php?lang=' . $new_lang;

// =====================================
// Fetch Categories and Locations
// =====================================
$cats = $pdo->query("
    SELECT *
    FROM categories
    ORDER BY name_ar ASC
")->fetchAll(PDO::FETCH_ASSOC);

$locs = $pdo->query("
    SELECT *
    FROM locations
    ORDER BY name_ar ASC
")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang_code); ?>"
      dir="<?php echo $direction; ?>">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        SVU Events Guide |
        <?php echo htmlspecialchars($lang['admin_add_event']); ?>
    </title>

    <link rel="icon" href="../assets/img/SVU-Events-icon.png">

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

    <style>

        :root {
            --primary-color: #071A39;
            --accent-color: #506382;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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

        .top-bar h2 {
            font-size: 1.2rem;
        }

        .top-bar-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .top-bar-actions a {
            color: white;
            text-decoration: none;
            padding: 6px 14px;
            border-radius: 4px;
            border: 1px solid white;
            transition: background 0.3s;
            font-size: 0.9rem;
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

        .form-group {
            margin-bottom: 20px;
        }

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

        /* Image Preview */

        .image-preview {
            margin-top: 12px;
            display: none;
        }

        .image-preview img {
            max-width: 100%;
            max-height: 220px;
            border-radius: 8px;
            border: 1px solid #ddd;
            object-fit: contain;
        }

        .image-help {
            display: block;
            margin-top: 7px;
            color: #64748b;
            font-size: 0.85rem;
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

        .btn-save:hover {
            background: var(--accent-color);
        }

        .btn-cancel {
            background: #e5e7eb;
            color: #374151;
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.3s;
        }

        .btn-cancel:hover {
            background: #d1d5db;
        }

        /* Error Message */

        .error-msg {
            background: #FEE2E2;
            color: #991B1B;
            padding: 12px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        @media (max-width: 600px) {

            .top-bar {
                padding: 15px;
                flex-direction: column;
                gap: 15px;
            }

            .top-bar-actions {
                flex-wrap: wrap;
                justify-content: center;
            }

            .form-card {
                padding: 20px;
            }

        }

    </style>

</head>

<body>

    <!-- ===================================== -->
    <!-- Top Navigation Bar -->
    <!-- ===================================== -->

    <div class="top-bar">

        <h2>SVU Events Guide</h2>

        <div class="top-bar-actions">

            <a href="<?php echo htmlspecialchars($switch_url); ?>">
                <?php echo htmlspecialchars($lang['admin_lang_switch']); ?>
            </a>

            <a href="dashboard.php">
                <?php echo htmlspecialchars($lang['admin_dashboard']); ?>
            </a>

            <a href="logout.php">
                <?php echo htmlspecialchars($lang['admin_logout']); ?>
            </a>

        </div>

    </div>

    <!-- ===================================== -->
    <!-- Main Content -->
    <!-- ===================================== -->

    <div class="container">

        <div class="form-card">

            <h1>
                <?php echo htmlspecialchars($lang['admin_add_event']); ?>
            </h1>

            <!-- Error Display -->

            <?php if ($error): ?>

                <div class="error-msg">
                    <?php echo htmlspecialchars($error); ?>
                </div>

            <?php endif; ?>

            <!-- Add Event Form -->

            <form method="POST" action="add_event.php">

                <!-- Arabic Title -->

                <div class="form-group">

                    <label>
                        <?php echo htmlspecialchars($lang['admin_title']); ?> *
                    </label>

                    <input
                        type="text"
                        name="title"
                        class="form-control"
                        value="<?php echo htmlspecialchars($title); ?>"
                        required
                    >

                </div>

                <!-- Arabic Description -->

                <div class="form-group">

                    <label>
                        <?php echo htmlspecialchars($lang['admin_description']); ?>
                    </label>

                    <textarea
                        name="description"
                        class="form-control"
                    ><?php echo htmlspecialchars($description); ?></textarea>

                </div>

                <!-- Category -->

                <div class="form-group">

                    <label>
                        <?php echo htmlspecialchars($lang['admin_category']); ?> *
                    </label>

                    <select name="category" class="form-control" required>

                        <option value="">
                            -- <?php echo htmlspecialchars($lang['admin_category']); ?> --
                        </option>

                        <?php foreach ($cats as $c): ?>

                            <option
                                value="<?php echo htmlspecialchars($c['id']); ?>"
                                <?php echo ((string)$category === (string)$c['id']) ? 'selected' : ''; ?>
                            >
                                <?php echo htmlspecialchars($c['name_ar']); ?>
                                |
                                <?php echo htmlspecialchars($c['name_en']); ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <!-- Location -->

                <div class="form-group">

                    <label>
                        <?php echo htmlspecialchars($lang['admin_location']); ?> *
                    </label>

                    <select name="location" class="form-control" required>

                        <option value="">
                            -- <?php echo htmlspecialchars($lang['admin_location']); ?> --
                        </option>

                        <?php foreach ($locs as $l): ?>

                            <option
                                value="<?php echo htmlspecialchars($l['id']); ?>"
                                <?php echo ((string)$location === (string)$l['id']) ? 'selected' : ''; ?>
                            >
                                <?php echo htmlspecialchars($l['name_ar']); ?>
                                |
                                <?php echo htmlspecialchars($l['name_en']); ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <!-- English Title -->

                <div class="form-group">

                    <label>
                        <?php echo htmlspecialchars($lang['admin_title_en']); ?> *
                    </label>

                    <input
                        type="text"
                        name="title_en"
                        class="form-control"
                        value="<?php echo htmlspecialchars($title_en); ?>"
                        required
                    >

                </div>

                <!-- English Description -->

                <div class="form-group">

                    <label>
                        <?php echo htmlspecialchars($lang['admin_description_en']); ?>
                    </label>

                    <textarea
                        name="description_en"
                        class="form-control"
                    ><?php echo htmlspecialchars($description_en); ?></textarea>

                </div>

                <!-- Event Date -->

                <div class="form-group">

                    <label>
                        <?php echo htmlspecialchars($lang['admin_date']); ?> *
                    </label>

                    <input
                        type="date"
                        name="event_date"
                        class="form-control"
                        value="<?php echo htmlspecialchars($event_date); ?>"
                        required
                    >

                </div>

                <!-- Image URL -->

                <div class="form-group">

                    <label>
                        <?php echo htmlspecialchars($lang['admin_image']); ?>
                        (Image URL)
                    </label>

                    <input
                        type="url"
                        name="image"
                        id="imageUrl"
                        class="form-control"
                        placeholder="https://example.com/image.jpg"
                        value="<?php echo htmlspecialchars($image); ?>"
                    >

                    <small class="image-help">
                        Enter the direct URL of the event image.
                    </small>

                    <!-- Image Preview -->

                    <div class="image-preview" id="imagePreview">

                        <img
                            id="previewImg"
                            src=""
                            alt="Event image preview"
                        >

                    </div>

                </div>

                <!-- Form Buttons -->

                <div class="form-actions">

                    <button type="submit" class="btn-save">

                        <i class="fas fa-save"></i>

                        <?php echo htmlspecialchars($lang['admin_save']); ?>

                    </button>

                    <a href="dashboard.php" class="btn-cancel">

                        <i class="fas fa-times"></i>

                        <?php echo htmlspecialchars($lang['admin_cancel']); ?>

                    </a>

                </div>

            </form>

        </div>

    </div>

    <!-- ===================================== -->
    <!-- Image URL Preview Script -->
    <!-- ===================================== -->

    <script>

        const imageUrl = document.getElementById('imageUrl');
        const imagePreview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');

        function updateImagePreview() {

            const url = imageUrl.value.trim();

            if (url !== '') {

                try {

                    const parsedUrl = new URL(url);

                    if (
                        parsedUrl.protocol === 'http:' ||
                        parsedUrl.protocol === 'https:'
                    ) {

                        previewImg.src = url;
                        imagePreview.style.display = 'block';

                    } else {

                        imagePreview.style.display = 'none';

                    }

                } catch (error) {

                    imagePreview.style.display = 'none';

                }

            } else {

                imagePreview.style.display = 'none';

            }

        }

        imageUrl.addEventListener('input', updateImagePreview);

        previewImg.addEventListener('error', function () {
            imagePreview.style.display = 'none';
        });

        updateImagePreview();

    </script>

</body>
</html>
