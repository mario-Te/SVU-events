<?php
// =====================================
// Initialize System
// =====================================
require_once 'config/db.php';
session_start(); // Default session timeout: 24 minutes (1440 seconds) as per PHP settings

// Set timezone
date_default_timezone_set('Asia/Damascus');

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
    $lang = require_once 'languages/en.php';
} else {
    $lang = require_once 'languages/ar.php';
}

// =====================================
// Fetch All Events from Database
// =====================================
try {
    $stmt = $pdo->query("
        SELECT e.*, 
               c.name_ar AS category_name_ar, c.name_en AS category_name_en,
               l.name_ar AS location_name_ar, l.name_en AS location_name_en
        FROM events e
        LEFT JOIN categories c ON e.category = c.id
        LEFT JOIN locations l ON e.location = l.id
        ORDER BY e.event_date DESC
    ");
    $eventsData = $stmt->fetchAll();
} catch (PDOException $e) {
    $eventsData = [];
}

// Extract categories and cities based on current language
$categories = [];
$cities = [];
foreach ($eventsData as $event) {
    $cat = ($lang_code === 'en' && !empty($event['category_name_en'])) ? $event['category_name_en'] : $event['category_name_ar'];
    $loc = ($lang_code === 'en' && !empty($event['location_name_en'])) ? $event['location_name_en'] : $event['location_name_ar'];
    if (!in_array($cat, $categories)) $categories[] = $cat;
    if (!in_array($loc, $cities)) $cities[] = $loc;
}
sort($categories);
sort($cities);
?>

<!DOCTYPE html>
<html lang="<?php echo $lang_code; ?>" dir="<?php echo $direction; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Meta Tags -->
    <meta name="description" content="<?php echo $lang['site_desc']; ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Title & Icon -->
    <title><?php echo $lang['nav_events']; ?> | <?php echo $lang['site_title']; ?></title>
    <link rel="icon" href="assets/img/SVU-Events-icon.png" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/styles.css" />

    <!-- ===================================== -->
    <!-- CUSTOM THEME & COLOR LABELS           -->
    <!-- ===================================== -->
    <style>
        :root {
    /* Primary Theme: Deep Forest Slate */
    --primary-color: #065f46; 
    --primary-gradient-start: #065f46;
    --primary-gradient-end: #022c22;

    /* Accent Highlights: Vibrant Emerald */
    --accent-color: #10b981; 
    --accent-gradient-start: #10b981;
    --accent-gradient-end: #059669;

    /* Neutral Backgrounds & Cards */
    --background-light: #f0fdf4;
    --card-bg: #ffffff;
    --text-secondary: #4b5563;
    
    /* Admin & Warning Theme */
    --admin-primary: #8b0000;
    --admin-secondary: #b22222;
    --admin-warning-bg: #fff3f3;
}

        /* Main Container */
        main {
            flex: 1;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        /* Page Title */
        .page-title {
            text-align: center;
            font-size: 2.3rem;
            margin: 35px 0;
            position: relative;
            padding-bottom: 15px;
            color: var(--primary-color);
            font-weight: 800;
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background-color: var(--accent-color);
            border-radius: 2px;
        }

        /* Filters Section */
        .filters {
            background-color: var(--card-bg);
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 35px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            border: 1px solid var(--border-color);
        }

        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
            align-items: center;
        }

        .filter-group {
            flex: 1;
            min-width: 220px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--primary-color);
            font-size: 0.95rem;
        }

        .filter-group select, .filter-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-family: inherit;
            background-color: var(--background-light);
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        .filter-group select:focus, .filter-group input:focus {
            outline: none;
            border-color: var(--accent-color);
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.15);
        }

        .filter-buttons {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 15px;
        }

        .filter-btn {
            padding: 10px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-apply {
            background: linear-gradient(135deg, var(--primary-gradient-start), var(--primary-gradient-end));
            color: white;
            box-shadow: 0 4px 12px rgba(67, 56, 202, 0.3);
        }

        .btn-apply:hover { 
            opacity: 0.95;
            transform: translateY(-2px);
        }

        .btn-clear {
            background-color: var(--background-light);
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
        }

        .btn-clear:hover { 
            background-color: var(--border-color); 
            color: var(--text-primary);
        }

        /* Events Grid */
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }

        .event-card {
            background-color: var(--card-bg);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            border: 1px solid var(--border-color);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .event-image {
            height: 220px;
            overflow: hidden;
        }

        .event-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .event-card:hover .event-image img { transform: scale(1.05); }

        .event-content {
            padding: 25px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .event-date {
            color: var(--accent-color);
            font-size: 0.9rem;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .event-title {
            font-size: 1.3rem;
            margin-bottom: 12px;
            color: var(--primary-color);
            font-weight: 700;
            flex: 1;
        }

        .event-location {
            color: var(--text-secondary);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
        }

        .event-category {
            display: inline-block;
            background-color: var(--background-light);
            color: var(--primary-color);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 20px;
            border: 1px solid var(--border-color);
            align-self: flex-start;
        }

        .event-btn {
            align-self: flex-start;
            padding: 10px 24px;
            background: linear-gradient(135deg, var(--accent-gradient-start), var(--accent-gradient-end));
            color: white;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            margin-top: auto;
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }

        .event-btn:hover { 
            opacity: 0.95;
            transform: translateY(-2px);
        }

        .no-events {
            grid-column: 1 / -1;
            text-align: center;
            padding: 50px;
            background-color: var(--card-bg);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            font-size: 1.1rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .events-grid { grid-template-columns: 1fr; }
            .filter-row { flex-direction: column; align-items: stretch; }
            .filter-group { min-width: 100%; }
            .filter-buttons { justify-content: center; }
        }
    </style>
</head>

<body>
    <!-- ===================================== -->
    <!-- Landing Page Overlay                   -->
    <!-- ===================================== -->
    <?php include 'includes/landing.php'; ?>

    <!-- ===================================== -->
    <!-- Unified Header                        -->
    <!-- ===================================== -->
    <?php include 'includes/header.php'; ?>

    <!-- ===================================== -->
    <!-- Main Content                          -->
    <!-- ===================================== -->
    <main>
        <div class="main-content" id="mainContent">
            <!-- Page Title -->
            <h1 class="page-title"><?php echo $lang['events_title']; ?></h1>

            <!-- Filters -->
            <div class="filters">
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="categoryFilter"><?php echo $lang['events_all_cats']; ?></label>
                        <select id="categoryFilter">
                            <option value=""><?php echo $lang['events_all_cats']; ?></option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="cityFilter"><?php echo $lang['events_all_cities']; ?></label>
                        <select id="cityFilter">
                            <option value=""><?php echo $lang['events_all_cities']; ?></option>
                            <?php foreach ($cities as $city): ?>
                                <option value="<?php echo htmlspecialchars($city); ?>"><?php echo htmlspecialchars($city); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="dateFilter"><?php echo $lang['event_date']; ?></label>
                        <input type="date" id="dateFilter">
                    </div>
                </div>
                <div class="filter-buttons">
                    <button class="filter-btn btn-apply" id="applyFilters"><?php echo $lang['events_filter']; ?></button>
                    <button class="filter-btn btn-clear" id="clearFilters"><?php echo $lang['events_clear']; ?></button>
                </div>
            </div>

            <!-- Events Grid -->
            <div class="events-grid" id="eventsGrid">
                <!-- Dynamically populated by JS -->
            </div>
        </div>
    </main>

    <!-- ===================================== -->
    <!-- Back to Top Button                    -->
    <!-- ===================================== -->
    <div class="back-to-top" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </div>

    <!-- ===================================== -->
    <!-- Unified Footer                        -->
    <!-- ===================================== -->
    <?php include 'includes/footer.php'; ?>

    <!-- JQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- Main JS -->
    <script src="assets/js/main.js"></script>

    <!-- Custom JS (Page Specific) -->
    <script>
    $(document).ready(function () {
        // =====================================
        // Pass events data from PHP to JS
        // =====================================
        var eventsData = <?php
            $localizedEvents = [];
            foreach ($eventsData as $event) {
                $event['title']       = ($lang_code === 'en' && !empty($event['title_en'])) ? $event['title_en'] : $event['title'];
                $event['description'] = ($lang_code === 'en' && !empty($event['description_en'])) ? $event['description_en'] : $event['description'];
                $event['category']    = ($lang_code === 'en' && !empty($event['category_name_en'])) ? $event['category_name_en'] : $event['category_name_ar'];
                $event['location']    = ($lang_code === 'en' && !empty($event['location_name_en'])) ? $event['location_name_en'] : $event['location_name_ar'];
                $localizedEvents[] = $event;
            }
            echo json_encode($localizedEvents);
        ?>;

        // =====================================
        // Cookie Consent Handler
        // =====================================
        $('#cookieAccept').click(function() {
            document.cookie = "cookie_consent=1; path=/; max-age=" + (60*60*24*30);
            $('#cookieConsent').fadeOut(400);
        });

        // =====================================
        // Display Events
        // =====================================
        function displayEvents(events) {
            var $grid = $('#eventsGrid');

            if (events.length === 0) {
                $grid.html('<div class="no-events"><?php echo $lang['events_no_results']; ?></div>');
                return;
            }

            var html = '';
          events.forEach(function(event) {
    var imgSrc = !event.image ? 'assets/img/events/default.webp' : 
                 (event.image.startsWith('http') ? event.image : 'assets/img/events/' + event.image);
                 
    html += '' +
        '<div class="event-card">' +
            '<div class="event-image">' +
                '<img src="' + imgSrc + '" alt="' + event.title + '">' +
            '</div>' +
            '<div class="event-content">' +
                '<div class="event-date">' + event.event_date + '</div>' +
                '<h3 class="event-title">' + event.title + '</h3>' +
                '<div class="event-location"><i class="fas fa-map-marker-alt"></i>' + event.location + '</div>' +
                '<span class="event-category">' + event.category + '</span>' +
                '<a href="<?php echo $base_url; ?>event.php?id=' + event.id + '" class="event-btn"><?php echo $lang['events_details']; ?></a>' +
            '</div>' +
        '</div>';
});

            $grid.html(html);
        }

        // Show all initially
        displayEvents(eventsData);

        // =====================================
        // Apply Filters
        // =====================================
        $('#applyFilters').click(function() {
            var cat = $('#categoryFilter').val();
            var city = $('#cityFilter').val();
            var date = $('#dateFilter').val();

            var filtered = eventsData.filter(function(e) {
                return (!cat || e.category === cat) &&
                       (!city || e.location === city) &&
                       (!date || e.event_date === date);
            });

            displayEvents(filtered);
        });

        // Clear Filters
        $('#clearFilters').click(function() {
            $('#categoryFilter').val('');
            $('#cityFilter').val('');
            $('#dateFilter').val('');
            displayEvents(eventsData);
        });
    });
    </script>
</body>
</html>