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
?>

<!DOCTYPE html>
<html lang="<?php echo $lang_code; ?>" dir="<?php echo $direction; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $lang['site_desc']; ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title><?php echo $lang['nav_about']; ?> | <?php echo $lang['site_title']; ?></title>
    <link rel="icon" href="assets/img/SVU-Events-icon.png" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/styles.css" />

    <!-- Custom CSS (Forest Green & Emerald Theme) -->
    <style>
        :root {
            /* Primary Theme: Deep Forest Slate */
            --primary-color: #065f46; 
            --primary-gradient-start: #065f46;
            --primary-gradient-end: #022c22;

            /* Accent Highlights: Vibrant Emerald */
            --accent-color: #10b981; 
            --accent-hover: #059669;
            --accent-gradient-start: #10b981;
            --accent-gradient-end: #059669;

            /* Neutral Backgrounds & Cards */
            --background-light: #f0fdf4;
            --card-bg: #ffffff;
            --text-main: #1f2937;
            --text-secondary: #4b5563;
            --border-color: #d1fae5;
        }

        body {
            background-color: var(--background-light);
            color: var(--text-main);
        }

        main {
            flex: 1;
            padding: 30px 20px;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .page-title {
            text-align: center;
            margin: 20px 0 40px;
            position: relative;
            padding-bottom: 15px;
            color: var(--primary-color);
            font-size: 2.2rem;
            font-weight: 800;
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(135deg, var(--accent-gradient-start), var(--accent-gradient-end));
            border-radius: 2px;
        }

        .about-section {
            background-color: var(--card-bg);
            border-radius: 14px;
            padding: 35px;
            margin-bottom: 30px;
            box-shadow: 0 10px 25px -5px rgba(6, 95, 70, 0.08), 0 8px 10px -6px rgba(6, 95, 70, 0.08);
            border: 1px solid var(--border-color);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .about-section:hover {
            box-shadow: 0 15px 35px rgba(6, 95, 70, 0.12);
        }

        .about-section h2 {
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
            color: var(--primary-color);
            font-size: 1.5rem;
            font-weight: 700;
        }

        .about-section h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            inset-inline-start: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(135deg, var(--accent-gradient-start), var(--accent-gradient-end));
            border-radius: 2px;
        }

        .about-content { 
            line-height: 1.9; 
            color: var(--text-secondary);
            font-size: 1.05rem;
        }
        .about-content p { margin-bottom: 15px; }

        .mvp-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }

        .mvp-item {
            background-color: var(--card-bg);
            padding: 35px 25px 25px;
            border-radius: 12px;
            text-align: center;
            position: relative;
            border: 1px solid var(--border-color);
            box-shadow: 0 6px 15px rgba(6, 95, 70, 0.04);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .mvp-item:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 25px rgba(6, 95, 70, 0.1);
        }

        .mvp-item::before {
            content: '✦';
            position: absolute;
            top: -16px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, var(--accent-gradient-start), var(--accent-gradient-end));
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 0.9rem;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
        }

        .mvp-item h3 { 
            color: var(--primary-color); 
            margin-bottom: 12px; 
            font-size: 1.25rem;
            font-weight: 700;
        }

        .mvp-item p {
            color: var(--text-secondary);
            font-size: 0.95rem;
            line-height: 1.7;
            margin: 0;
        }

        .policies-list { list-style: none; padding: 0; margin-top: 15px; }
        .policies-list li {
            margin-bottom: 14px;
            padding-inline-start: 30px;
            position: relative;
            color: var(--text-secondary);
        }
        .policies-list li::before {
            content: '✔';
            color: var(--accent-color);
            font-weight: bold;
            position: absolute;
            inset-inline-start: 0;
            font-size: 1.1rem;
        }

        /* Thank-You Section */
        .thankyou-section {
            background-color: var(--card-bg);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(6, 95, 70, 0.08), 0 8px 10px -6px rgba(6, 95, 70, 0.08);
            margin-bottom: 30px;
            border: 1px solid var(--border-color);
        }

        .thankyou-image {
            width: 100%;
            display: block;
            max-height: 450px;
            object-fit: cover;
        }

        .thankyou-caption {
            text-align: center;
            padding: 30px;
            background: linear-gradient(to bottom, #f0fdf4, #ffffff);
        }

        .thankyou-caption h3 {
            color: var(--primary-color);
            font-size: 1.5rem;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .thankyou-caption p {
            color: var(--text-secondary);
            font-size: 1.05rem;
            margin: 0;
        }

        @media (max-width: 768px) {
            .thankyou-image { max-height: 280px; }
            .about-section { padding: 20px; }
            .page-title { font-size: 1.8rem; }
        }
    </style>
</head>

<body>
    <?php include 'includes/landing.php'; ?>
    <?php include 'includes/header.php'; ?>

    <main>
        <div class="main-content" id="mainContent">
            <h1 class="page-title"><?php echo $lang['about_title']; ?></h1>

            <div class="about-section">
                <h2><?php echo $lang['about_welcome']; ?></h2>
                <div class="about-content">
                    <p><?php echo $lang['about_intro']; ?></p>
                    <p><?php echo $lang['about_belief']; ?></p>
                </div>
            </div>

            <div class="about-section">
                <h2><?php echo $lang['about_vision_title']; ?></h2>
                <div class="mvp-container">
                    <div class="mvp-item">
                        <h3><?php echo $lang['vision']; ?></h3>
                        <p><?php echo $lang['about_vision']; ?></p>
                    </div>
                    <div class="mvp-item">
                        <h3><?php echo $lang['mission']; ?></h3>
                        <p><?php echo $lang['about_mission']; ?></p>
                    </div>
                    <div class="mvp-item">
                        <h3><?php echo $lang['goal']; ?></h3>
                        <p><?php echo $lang['about_goal']; ?></p>
                    </div>
                </div>
            </div>

            <div class="thankyou-section">
                <img src="assets/img/Thank-You.webp" alt="Thank You" class="thankyou-image">
                <div class="thankyou-caption">
                    <h3>
                        <?php echo ($lang_code === 'ar') ? 'شكراً لكم على دعمكم المتواصل' : 'Thank You for Your Continued Support'; ?>
                    </h3>
                    <p>
                        <?php echo ($lang_code === 'ar') ? 'معاً نبني مستقبلاً أفضل لطلاب الجامعة الافتراضية السورية' : 'Together we build a better future for SVU students'; ?>
                    </p>
                </div>
            </div>

            <div class="about-section">
                <h2><?php echo $lang['about_policies_title']; ?></h2>
                <div class="about-content">
                    <p><?php echo $lang['about_policies_desc']; ?></p>
                  <ul class="policies-list">
    <li><?php echo $lang['about_li_1']; ?></li>
    <li><?php echo $lang['about_li_2']; ?></li>
    <li><?php echo $lang['about_li_3']; ?></li>
    <li><?php echo $lang['about_li_4']; ?></li>
    <li><?php echo $lang['about_li_5']; ?></li>
    <li><?php echo $lang['about_li_6']; ?></li>
    <li><?php echo $lang['about_li_7']; ?></li>
    <li><?php echo $lang['about_li_8']; ?></li>
    <li><?php echo $lang['about_li_9']; ?></li>
    <li><?php echo $lang['about_li_10']; ?></li>
</ul>
                </div>
            </div>
        </div>
    </main>

    <div class="back-to-top" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
    $(document).ready(function () {
        $('#cookieAccept').click(function() {
            document.cookie = "cookie_consent=1; path=/; max-age=" + (60*60*24*30);
            $('#cookieConsent').fadeOut(400);
        });
    });
    </script>
</body>
</html>