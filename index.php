<?php
// =====================================
// Initialize System
// =====================================
require_once 'config/db.php';
// Base URL of your project


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
// Fetch Featured Events from Database
// =====================================
try {
    $stmt = $pdo->query("
        SELECT e.*, 
               c.name_ar AS category_name_ar, c.name_en AS category_name_en,
               l.name_ar AS location_name_ar, l.name_en AS location_name_en
        FROM events e
        LEFT JOIN categories c ON e.category = c.id
        LEFT JOIN locations l ON e.location = l.id
        ORDER BY e.event_date ASC
        LIMIT 3
    ");
    $eventsData = $stmt->fetchAll();
} catch (PDOException $e) {
    $eventsData = [];
}
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
    <title><?php echo $lang['nav_home']; ?> | <?php echo $lang['site_title']; ?></title>
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

        /* Collapsible Sections Theme */
        .collapsible-sections {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 15px;
        }

        .collapsible-section {
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
            margin-bottom: 20px;
        }

        .collapsible-section:hover {
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }

        .collapsible-header {
            background: linear-gradient(135deg, var(--primary-gradient-start), var(--primary-gradient-end));
            color: white;
            padding: 18px 25px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }

        .collapsible-header:hover {
            opacity: 0.95;
        }

        .collapsible-title {
            color: #ffffff;
            font-size: 1.3rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .collapsible-icon {
            font-size: 1.1rem;
            transition: transform 0.3s ease;
        }

        .collapsible-section.active .collapsible-icon {
            transform: rotate(180deg);
        }

        .collapsible-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s ease, padding 0.3s ease;
            background: var(--background-light);
        }

        .collapsible-section.active .collapsible-content {
            max-height: 1000px;
            padding: 25px;
        }

        /* Developer Card Theme */
        .developer-container {
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        .developer-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .developer-card {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            border-left: 4px solid var(--accent-color);
            flex: 1;
            min-width: 280px;
            max-width: calc(50% - 10px);
            box-sizing: border-box;
            border: 1px solid #e2e8f0;
        }

        [dir="rtl"] .developer-card {
            border-left: 1px solid #e2e8f0;
            border-right: 4px solid var(--accent-color);
        }

        .developer-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }

        .dev-name {
            font-size: 1.25rem;
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 6px;
        }

        .dev-info {
            color: var(--text-secondary);
            font-size: 0.95rem;
            margin-bottom: 12px;
        }

        .dev-role {
            display: inline-block;
            background: var(--accent-color);
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
        }

        /* Admin Access Section Theme (Danger Zone) */
        .admin-access-section {
            border: 2px solid #fecaca;
        }

        .admin-access-header {
            background: linear-gradient(135deg, var(--admin-primary), var(--admin-secondary)) !important;
            border-bottom: 3px solid #ff4444 !important;
        }

        .admin-warning {
            background: var(--admin-warning-bg);
            border: 1px solid #f87171;
            color: var(--admin-secondary);
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .admin-warning i {
            font-size: 1.2rem;
        }

        .admin-cards-grid {
            display: grid;
            gap: 15px;
            margin-bottom: 20px;
        }

        .admin-card {
            background: var(--card-bg);
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.3s ease;
        }

        .admin-card:hover {
            border-color: var(--admin-secondary);
            box-shadow: 0 4px 15px rgba(178,34,34,0.08);
            transform: translateY(-2px);
        }

        .admin-card-icon {
            width: 45px;
            height: 45px;
            background: #fef2f2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--admin-secondary);
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .admin-card-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 0;
        }

        .admin-card-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .admin-card-value {
            font-size: 0.95rem;
            font-weight: 600;
            color: #1e293b;
            word-break: break-all;
        }

        .admin-security-note {
            background: #f1f5f9;
            border-left: 4px solid var(--admin-secondary);
            padding: 14px 18px;
            font-size: 0.9rem;
            color: #475569;
            display: flex;
            align-items: center;
            gap: 12px;
            border-radius: 0 8px 8px 0;
        }

        .admin-security-note i {
            color: var(--admin-secondary);
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        [dir="ltr"] .admin-security-note {
            border-left: none;
            border-right: 4px solid var(--admin-secondary);
            border-radius: 8px 0 0 8px;
        }

        .admin-access-section .collapsible-content {
            max-height: 0 !important;
            padding: 0 !important;
        }

        .admin-access-section.active .collapsible-content {
            max-height: 1000px !important;
            padding: 25px !important;
        }

        /* Hero Slider Theme */
        .hero-slider {
            position: relative;
            height: 480px;
            width: 90%;
            overflow: hidden;
            margin: 25px auto;
            max-width: 1200px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1s ease;
            cursor: pointer;
            z-index: 0;
        }

        .slide.active {
            opacity: 1;
            z-index: 1;
        }

        .slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 16px;
        }

        .slide-content {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 30px;
            background: linear-gradient(to top, rgba(15,23,42,0.9), rgba(15,23,42,0.4), transparent);
            color: white;
        }

        .slide-date {
            display: inline-block;
            padding: 5px 12px;
            font-size: 0.85rem;
            color: #ffffff;
            border-radius: 20px;
            background-color: var(--accent-color);
            margin-bottom: 8px;
            font-weight: 600;
        }

        .slide-title {
            display: block;
            font-size: 1.8rem;
            color: #ffffff;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .slider-nav {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            z-index: 10;
        }

        .slider-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: rgba(255,255,255,0.4);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .slider-dot.active {
            background-color: var(--accent-color);
            width: 28px;
            border-radius: 6px;
        }

        /* Explore Button Theme */
        .explore-btn-container {
            text-align: center;
            margin: 45px 0;
            position: relative;
        }

        .explore-btn {
            display: inline-flex;
            align-items: center;
            gap: 15px;
            background: linear-gradient(135deg, var(--accent-gradient-start), var(--accent-gradient-end));
            color: white;
            padding: 16px 36px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.2rem;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
            text-decoration: none;
        }

        .explore-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(59, 130, 246, 0.6);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-title { font-size: 1.8rem; }
            .hero-slider { height: 320px; width: 92%; }
            .slide-title { font-size: 1.3rem; }
            .developer-card { max-width: 100%; min-width: 100%; }
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
    <!-- Developer Section (Collapsible)       -->
    <!-- ===================================== -->
    <div class="collapsible-sections">
        <div class="collapsible-section">
            <div class="collapsible-header">
                <h3 class="collapsible-title">
                    <i class="fas fa-user-graduate"></i>
                    <?php echo $lang['team_section']; ?>
                </h3>
                <i class="fas fa-chevron-down collapsible-icon"></i>
            </div>
            <div class="collapsible-content">
                <div class="developer-container">
                    <div class="developer-row">
                        <div class="developer-card">
                            <h4 class="dev-name"><?php echo $lang['developer_name_0']; ?></h4>
                            <div class="dev-info"><?php echo $lang['developer_id_0']; ?></div>
                            <span class="dev-role"><?php echo $lang['developer_role_1']; ?></span>
                        </div>
                         <div class="developer-card">
                            <h4 class="dev-name"><?php echo $lang['developer_name_1']; ?></h4>
                            <div class="dev-info"><?php echo $lang['developer_id_1']; ?></div>
                            <span class="dev-role"><?php echo $lang['developer_role_0']; ?></span>
                        </div>
                         <div class="developer-card">
                            <h4 class="dev-name"><?php echo $lang['developer_name_2']; ?></h4>
                            <div class="dev-info"><?php echo $lang['developer_id_2']; ?></div>
                            <span class="dev-role"><?php echo $lang['developer_role_1']; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===================================== -->
    <!-- Admin Access Section (Collapsible)    -->
    <!-- ===================================== -->
    <div class="collapsible-sections">
        <div class="collapsible-section admin-access-section">
            <div class="collapsible-header admin-access-header">
                <h3 class="collapsible-title">
                    <i class="fas fa-shield"></i>
                    <?php echo $lang['admin_access_title']; ?>
                </h3>
                <i class="fas fa-chevron-down collapsible-icon"></i>
            </div>
            <div class="collapsible-content">
                <div class="admin-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?php echo $lang['admin_access_desc']; ?>
                </div>

                <div class="admin-cards-grid">
                    <!-- URL Card -->
                    <a href="<?php echo $base_url; ?>admin/login.php" target="_blank" style="text-decoration:none;">
                        <div class="admin-card">
                            <div class="admin-card-icon">
                                <i class="fas fa-link"></i>
                            </div>
                            <div class="admin-card-info">
                                <span class="admin-card-label"><?php echo $lang['admin_access_url']; ?></span>
                                <span class="admin-card-value"><?php echo $base_url; ?>admin/login.php</span>
                            </div>
                        </div>
                    </a>

                    <!-- Username Card -->
                    <div class="admin-card">
                        <div class="admin-card-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="admin-card-info">
                            <span class="admin-card-label"><?php echo $lang['admin_access_user']; ?></span>
                            <span class="admin-card-value">admin</span>
                        </div>
                    </div>

                    <!-- Password Card -->
                    <div class="admin-card">
                        <div class="admin-card-icon">
                            <i class="fas fa-key"></i>
                        </div>
                        <div class="admin-card-info">
                            <span class="admin-card-label"><?php echo $lang['admin_access_pass']; ?></span>
                            <span class="admin-card-value">admin</span>
                        </div>
                    </div>
                </div>

                <div class="admin-security-note">
                    <i class="fas fa-lock"></i>
                    <?php echo $lang['admin_access_warn']; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ===================================== -->
    <!-- Main Content                          -->
    <!-- ===================================== -->
    <main>
        <div class="main-content" id="mainContent">
            <h1 class="page-title"><?php echo $lang['home_title']; ?></h1>

            <!-- Hero Slider -->
            <div class="hero-slider" id="heroSlider">
                <!-- Populated dynamically by JS with data from PHP -->
            </div>

            <!-- Explore Button -->
            <div class="explore-btn-container">
                <a href="<?php echo $base_url; ?>events.php" class="explore-btn">
                    <?php echo $lang['home_explore']; ?>
                    <i class="fas fa-arrow-<?php echo ($lang_code === 'ar') ? 'left' : 'right'; ?>"></i>
                </a>
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
    $(document).ready(function() {
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

        $('#cookieAccept').click(function() {
            document.cookie = "cookie_consent=1; path=/; max-age=" + (60*60*24*30);
            $('#cookieConsent').fadeOut(400);
        });

        $('.collapsible-header').click(function() {
            var $section = $(this).closest('.collapsible-section');
            $section.toggleClass('active');
        });

        setTimeout(function() {
            $('.collapsible-section').not('.admin-access-section').addClass('active');
        }, 800);

        function initHeroSlider() {
            if (eventsData.length === 0) {
                $('#heroSlider').html('<p style="text-align:center;padding:120px;color:var(--text-secondary);"><?php echo $lang['home_no_event_avl']; ?></p>');
                return;
            }

            var sliderHtml = '';
            var dotsHtml = '';

            eventsData.forEach(function(event, index) {
                var activeClass = index === 0 ? 'active' : '';
                var imgSrc = event.image ?  event.image : 'assets/img/events/default.webp';
                sliderHtml +=
                    '<div class="slide ' + activeClass + '" data-event-id="' + event.id + '">' +
                        '<img src="' + imgSrc + '" alt="' + event.title + '">' +
                        '<div class="slide-content">' +
                            '<div class="slide-date">' + event.event_date + '</div>' +
                            '<h3 class="slide-title">' + event.title + '</h3>' +
                        '</div>' +
                    '</div>';
                dotsHtml += '<div class="slider-dot ' + activeClass + '" data-slide="' + index + '"></div>';
            });

            $('#heroSlider').html(sliderHtml);
            $('#heroSlider').append('<div class="slider-nav">' + dotsHtml + '</div>');

            $('.slide').click(function() {
                var eventId = $(this).data('event-id');
                window.location.href = '<?php echo $base_url; ?>event.php?id=' + eventId;
            });

            var currentSlide = 0;
            var slideCount = eventsData.length;

            function goToSlide(index) {
                $('.slide').removeClass('active');
                $('.slider-dot').removeClass('active');
                $('.slide').eq(index).addClass('active');
                $('.slider-dot[data-slide="' + index + '"]').addClass('active');
                currentSlide = index;
            }

            $('.slider-dot').click(function(e) {
                e.stopPropagation();
                var slideIndex = $(this).data('slide');
                goToSlide(slideIndex);
            });

            setInterval(function() {
                currentSlide = (currentSlide + 1) % slideCount;
                goToSlide(currentSlide);
            }, 5000);
        }

        initHeroSlider();
    });
    </script>
</body>
</html>