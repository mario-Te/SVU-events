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
// Fetch Single Event by ID
// =====================================
$event = null;
$relatedEvents = [];

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];

    try {
        $stmt = $pdo->prepare("
            SELECT e.*, 
                   c.name_ar AS category_name_ar, c.name_en AS category_name_en,
                   l.name_ar AS location_name_ar, l.name_en AS location_name_en
            FROM events e
            LEFT JOIN categories c ON e.category = c.id
            LEFT JOIN locations l ON e.location = l.id
            WHERE e.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $event = $stmt->fetch();

        // Save original IDs before localization overwrites them
        if ($event) {
            $originalCategory = $event['category'];
            $originalLocation = $event['location'];
        }

        // Select language-appropriate fields
        if ($event) {
            $event['title']       = ($lang_code === 'en' && !empty($event['title_en'])) ? $event['title_en'] : $event['title'];
            $event['description'] = ($lang_code === 'en' && !empty($event['description_en'])) ? $event['description_en'] : $event['description'];
            $event['category']    = ($lang_code === 'en' && !empty($event['category_name_en'])) ? $event['category_name_en'] : $event['category_name_ar'];
            $event['location']    = ($lang_code === 'en' && !empty($event['location_name_en'])) ? $event['location_name_en'] : $event['location_name_ar'];
        }

        // Fetch related events using original numeric IDs
        if ($event) {
            $stmtRel = $pdo->prepare(
                "SELECT * FROM events WHERE id != :id AND (
                    category = :cat1 OR category_en = :cat2 OR 
                    location = :loc1 OR location_en = :loc2
                ) LIMIT 3"
            );
            $stmtRel->execute([
                ':id'   => $id,
                ':cat1' => $originalCategory,
                ':cat2' => $originalCategory,
                ':loc1' => $originalLocation,
                ':loc2' => $originalLocation
            ]);
            $relatedEvents = $stmtRel->fetchAll();

            // Localize related events
            foreach ($relatedEvents as &$rel) {
                $rel['title']    = ($lang_code === 'en' && !empty($rel['title_en'])) ? $rel['title_en'] : $rel['title'];
                $rel['category'] = ($lang_code === 'en' && !empty($rel['category_en'])) ? $rel['category_en'] : $rel['category'];
                $rel['location'] = ($lang_code === 'en' && !empty($rel['location_en'])) ? $rel['location_en'] : $rel['location'];
            }
            unset($rel);
        }
    } catch (PDOException $e) {
        $event = null;
    }
}

// Language switch URL (preserves event ID)
$new_lang = ($lang_code === 'ar') ? 'en' : 'ar';
$switch_url = '?lang=' . $new_lang . (isset($id) ? '&id=' . $id : '');
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
    <title><?php echo ($event ? htmlspecialchars($event['title']) : $lang['nav_event']); ?> | <?php echo $lang['site_title']; ?></title>
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
            
            /* Admin & Warning Theme */
            --admin-primary: #8b0000;
            --admin-secondary: #b22222;
            --admin-warning-bg: #fff3f3;
        }

        body {
            background-color: var(--background-light);
            color: var(--text-main);
        }

        /* Main Container */
        main {
            flex: 1;
            padding: 30px 20px;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        /* Event Hero */
        .event-detail {
            background-color: var(--card-bg);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(6, 95, 70, 0.08), 0 8px 10px -6px rgba(6, 95, 70, 0.08);
            margin-bottom: 40px;
            border: 1px solid var(--border-color);
        }

        .event-hero {
            position: relative;
            height: 420px;
            overflow: hidden;
            background-color: #e6f4ed;
        }

        .event-hero img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .event-hero:hover img {
            transform: scale(1.02);
        }

        /* Event Title Below Image */
        .event-title-section {
            padding: 30px 35px 0 35px;
        }

        .event-main-title {
            font-size: 2.2rem;
            color: var(--primary-color);
            font-weight: 800;
            line-height: 1.3;
        }

        /* Event Content */
        .event-content { padding: 25px 35px 35px; }

        .event-info {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
            padding-bottom: 25px;
            border-bottom: 1px solid var(--border-color);
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 12px;
            background-color: var(--background-light);
            padding: 12px 18px;
            border-radius: 8px;
            min-width: 210px;
            border: 1px solid var(--border-color);
            font-weight: 500;
            color: var(--text-main);
        }

        .info-item i { color: var(--accent-color); font-size: 1.25rem; }

        .event-description { margin-bottom: 35px; line-height: 1.9; color: var(--text-main); font-size: 1.05rem; }

        .event-description h3 {
            margin-bottom: 15px;
            position: relative;
            padding-bottom: 10px;
            color: var(--primary-color);
            font-size: 1.4rem;
        }

        .event-description h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            inset-inline-start: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(135deg, var(--accent-gradient-start), var(--accent-gradient-end));
            border-radius: 2px;
        }

        /* Action Buttons */
        .event-actions {
            display: flex;
            gap: 15px;
            margin-top: 35px;
            flex-wrap: wrap;
        }

        .action-btn {
            padding: 14px 28px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: inherit;
            font-size: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .btn-primary { 
            background: linear-gradient(135deg, var(--primary-gradient-start), var(--primary-gradient-end)); 
            color: white; 
        }
        .btn-primary:hover { 
            opacity: 0.95;
            transform: translateY(-2px); 
            box-shadow: 0 6px 12px rgba(6, 95, 70, 0.25); 
        }

        .btn-secondary {
            background-color: var(--card-bg);
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
        }
        .btn-secondary:hover { 
            background-color: var(--background-light); 
            transform: translateY(-2px); 
        }

        /* Related Events */
        .related-events { margin-top: 50px; }

        .section-title {
            text-align: center;
            margin-bottom: 35px;
            position: relative;
            padding-bottom: 15px;
            color: var(--primary-color);
            font-size: 1.8rem;
            font-weight: 700;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 50%;
            transform: translateX(50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(135deg, var(--accent-gradient-start), var(--accent-gradient-end));
            border-radius: 2px;
        }

        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
        }

        .related-card {
            background-color: var(--card-bg);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 15px rgba(6, 95, 70, 0.06);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid var(--border-color);
        }

        .related-card:hover { 
            transform: translateY(-6px); 
            box-shadow: 0 12px 25px rgba(6, 95, 70, 0.12);
        }

        .related-image { height: 190px; overflow: hidden; background-color: #e6f4ed; }
        .related-image img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease; }
        .related-card:hover .related-image img { transform: scale(1.05); }

        .related-content { padding: 22px; }

        .related-date { color: var(--accent-color); font-size: 0.9rem; font-weight: 600; margin-bottom: 8px; }

        .related-title { font-size: 1.15rem; margin-bottom: 12px; color: var(--primary-color); font-weight: 700; line-height: 1.4; }

        .related-link {
            color: var(--primary-color);
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 12px;
            text-decoration: none;
            transition: color 0.2s;
        }

        .related-link:hover { color: var(--accent-color); text-decoration: underline; }

        .no-event {
            text-align: center;
            padding: 80px 20px;
            font-size: 1.3rem;
            color: var(--text-secondary);
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(6, 95, 70, 0.05);
            border: 1px solid var(--border-color);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .event-hero { height: 280px; }
            .event-main-title { font-size: 1.6rem; }
            .event-info { flex-direction: column; gap: 12px; }
            .info-item { min-width: 100%; }
            .event-actions { flex-direction: column; }
            .action-btn { width: 100%; justify-content: center; }
            .related-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>

<body>
    <!-- ===================================== -->
    <!-- Landing Page Overlay                  -->
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
            <?php if ($event): 
                // Determine image source for main event (supports external URLs or local files)
                $mainImg = $event['image'];
                if (empty($mainImg)) {
                    $mainImg = 'assets/img/events/default.webp';
                } elseif (!preg_match('/^https?:\/\//', $mainImg)) {
                    $mainImg = 'assets/img/events/' . $mainImg;
                }
            ?>
                <!-- Event Detail -->
                <div class="event-detail">
                    <!-- Event Hero Image -->
                    <div class="event-hero">
                        <img src="<?php echo htmlspecialchars($mainImg); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>">
                    </div>

                    <!-- Event Title Below Image -->
                    <div class="event-title-section">
                        <h1 class="event-main-title"><?php echo htmlspecialchars($event['title']); ?></h1>
                    </div>

                    <div class="event-content">
                        <!-- Event Info -->
                        <div class="event-info">
                            <div class="info-item">
                                <i class="fas fa-tag"></i>
                                <span><?php echo htmlspecialchars($event['category']); ?></span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-calendar-alt"></i>
                                <span><?php echo htmlspecialchars($event['event_date']); ?></span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo htmlspecialchars($event['location']); ?></span>
                            </div>
                        </div>

                        <!-- Event Description -->
                        <div class="event-description">
                            <h3><?php echo $lang['event_about']; ?></h3>
                            <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="event-actions">
                            <!-- Add to Calendar Button -->
                            <button class="action-btn btn-primary" id="addToCalendarBtn">
                                <i class="fas fa-calendar-plus"></i>
                                <?php echo ($lang_code === 'ar') ? 'أضف إلى التقويم' : 'Add to Calendar'; ?>
                            </button>

                            <!-- Share Button -->
                            <button class="action-btn btn-secondary" id="shareEventBtn">
                                <i class="fas fa-share-alt"></i>
                                <?php echo ($lang_code === 'ar') ? 'مشاركة' : 'Share'; ?>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Related Events -->
                <?php if (count($relatedEvents) > 0): ?>
                <div class="related-events">
                    <h2 class="section-title"><?php echo $lang['event_related']; ?></h2>
                    <div class="related-grid">
                        <?php foreach ($relatedEvents as $rel): 
                            // Determine image source for related events
                            $relImg = $rel['image'];
                            if (empty($relImg)) {
                                $relImg = 'assets/img/events/default.webp';
                            } elseif (!preg_match('/^https?:\/\//', $relImg)) {
                                $relImg = 'assets/img/events/' . $relImg;
                            }
                        ?>
                        <div class="related-card">
                            <div class="related-image">
                                <img src="<?php echo htmlspecialchars($relImg); ?>" alt="<?php echo htmlspecialchars($rel['title']); ?>">
                            </div>
                            <div class="related-content">
                                <div class="related-date"><?php echo htmlspecialchars($rel['event_date']); ?></div>
                                <h3 class="related-title"><?php echo htmlspecialchars($rel['title']); ?></h3>
                                <a href="event.php?id=<?php echo $rel['id']; ?>" class="related-link">
                                    <?php echo $lang['events_details']; ?> <i class="fas fa-arrow-right" style="font-size: 0.8rem;"></i>
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            <?php else: ?>
                <!-- Event Not Found -->
                <div class="no-event"><?php echo $lang['event_not_found']; ?></div>
            <?php endif; ?>
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
        // Cookie Consent Handler
        // =====================================
        $('#cookieAccept').click(function() {
            document.cookie = "cookie_consent=1; path=/; max-age=" + (60*60*24*30);
            $('#cookieConsent').fadeOut(400);
        });

        // =====================================
        // Share Event Button (Web Share API)
        // =====================================
        $('#shareEventBtn').click(function() {
            var url = window.location.href;
            var title = '<?php echo $event ? addslashes(htmlspecialchars($event['title'])) : ''; ?>';
            var text = '<?php echo $event ? addslashes(strip_tags($event['description'])) : ''; ?>';

            // Use Web Share API for mobile (falls back to copy link on desktop)
            if (navigator.share) {
                navigator.share({
                    title: title,
                    text: text.substring(0, 100) + '...',
                    url: url
                }).catch(function() {
                    copyToClipboard(url);
                });
            } else {
                copyToClipboard(url);
            }
        });

        // Copy link to clipboard
        function copyToClipboard(text) {
            var $temp = $('<input>');
            $('body').append($temp);
            $temp.val(text).select();
            document.execCommand('copy');
            $temp.remove();
            showToast('<?php echo ($lang_code === 'ar') ? 'تم نسخ الرابط!' : 'Link copied!'; ?>');
        }

        // =====================================
        // Add to Calendar Button
        // =====================================
        $('#addToCalendarBtn').click(function() {
            var title = '<?php echo $event ? addslashes(htmlspecialchars($event['title'])) : ''; ?>';
            var date = '<?php echo $event ? $event['event_date'] : ''; ?>';
            var location = '<?php echo $event ? addslashes(htmlspecialchars($event['location'])) : ''; ?>';
            var description = '<?php echo $event ? addslashes(strip_tags($event['description'])) : ''; ?>';

            // Build Google Calendar URL
            var googleCalUrl = 'https://www.google.com/calendar/render?action=TEMPLATE' +
                '&text=' + encodeURIComponent(title) +
                '&dates=' + encodeURIComponent(date.replace(/-/g, '') + 'T090000/' + date.replace(/-/g, '') + 'T170000') +
                '&details=' + encodeURIComponent(description) +
                '&location=' + encodeURIComponent(location);

            window.open(googleCalUrl, '_blank');
        });
    });
    </script>
</body>
</html>