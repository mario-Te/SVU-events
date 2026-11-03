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
    setcookie('site_lang', $lang_code, time() + (86400 * 30), '/');  // 30 days
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
// Contact Form Submission (Server-Side)
// =====================================
$form_error = '';
$form_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Server-side validation
    if ($name === '' || $email === '' || $phone === '' || $message === '') {
        $form_error = $lang['contact_fields_required'];
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $form_error = $lang['contact_invalid_email'];
    } elseif (!preg_match('/^9[0-9]{8}$/', $phone)) {
        $form_error = $lang['contact_invalid_phone'];
    } else {
        $form_success = $lang['contact_success'];
    }
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
    <title><?php echo $lang['nav_contact']; ?> | <?php echo $lang['site_title']; ?></title>
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
            
            /* Status Colors */
            --error-color: #ef4444;
            --success-color: #10b981;
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

        .contact-container {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            margin-bottom: 50px;
        }

        .contact-info {
            flex: 1;
            min-width: 300px;
            background-color: var(--card-bg);
            border-radius: 14px;
            padding: 35px;
            box-shadow: 0 10px 25px -5px rgba(6, 95, 70, 0.08), 0 8px 10px -6px rgba(6, 95, 70, 0.08);
            border: 1px solid var(--border-color);
        }

        .contact-info h3 {
            margin-bottom: 25px;
            position: relative;
            padding-bottom: 10px;
            color: var(--primary-color);
            font-size: 1.5rem;
            font-weight: 700;
        }

        .contact-info h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            inset-inline-start: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(135deg, var(--accent-gradient-start), var(--accent-gradient-end));
            border-radius: 2px;
        }

        .contact-details { list-style: none; padding: 0; }

        .contact-details li {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 25px;
        }

        .contact-details i {
            color: var(--accent-color);
            font-size: 1.3rem;
            margin-top: 3px;
            background-color: var(--background-light);
            padding: 12px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
        }

        .contact-details div { flex: 1; }
        .contact-details h4 { margin-bottom: 5px; color: var(--primary-color); font-weight: 600; font-size: 1.1rem; }
        .contact-details p { color: var(--text-secondary); margin: 0; font-size: 1.05rem; }

        .contact-form {
            flex: 1.2;
            min-width: 300px;
            background-color: var(--card-bg);
            padding: 35px;
            border-radius: 14px;
            box-shadow: 0 10px 25px -5px rgba(6, 95, 70, 0.08), 0 8px 10px -6px rgba(6, 95, 70, 0.08);
            border: 1px solid var(--border-color);
        }

        .contact-form h3 {
            margin-bottom: 25px;
            position: relative;
            padding-bottom: 10px;
            color: var(--primary-color);
            font-size: 1.5rem;
            font-weight: 700;
        }

        .contact-form h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            inset-inline-start: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(135deg, var(--accent-gradient-start), var(--accent-gradient-end));
            border-radius: 2px;
        }

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
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-family: inherit;
            background-color: var(--background-light);
            color: var(--text-main);
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent-color);
            background-color: white;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
        }

        .form-control.error { border-color: var(--error-color); background-color: #fff5f5; }

        .error-message { color: var(--error-color); font-size: 0.85rem; margin-top: 5px; display: none; }
        .error-message.show { display: block; }

        .phone-input {
            display: flex;
            direction: ltr;
        }

        [dir="rtl"] .phone-input {
            flex-direction: row-reverse;
        }

        .phone-prefix {
            background-color: #e6f4ed;
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            white-space: nowrap;
            font-weight: 600;
        }

        [dir="rtl"] .phone-prefix {
            border-right: none;
            border-radius: 0 8px 8px 0;
        }
        [dir="rtl"] .phone-input .form-control {
            border-radius: 8px 0 0 8px;
        }

        [dir="ltr"] .phone-prefix {
            border-left: none;
            border-radius: 8px 0 0 8px;
        }
        [dir="ltr"] .phone-input .form-control {
            border-radius: 0 8px 8px 0;
        }

        textarea.form-control { min-height: 140px; resize: vertical; }

        .submit-btn {
            background: linear-gradient(135deg, var(--primary-gradient-start), var(--primary-gradient-end));
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            width: 100%;
            justify-content: center;
        }

        .submit-btn:hover {
            opacity: 0.95;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(6, 95, 70, 0.25);
        }
        .submit-btn:disabled { background-color: var(--text-secondary); cursor: not-allowed; transform: none; }

        .form-message {
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            display: none;
            font-weight: 500;
        }

        .form-message.success {
            background-color: rgba(16,185,129,0.1);
            color: var(--success-color);
            border: 1px solid var(--success-color);
            display: block;
        }

        .form-message.error {
            background-color: rgba(239,68,68,0.1);
            color: var(--error-color);
            border: 1px solid var(--error-color);
            display: block;
        }

        @media (max-width: 768px) {
            .contact-container { flex-direction: column; }
            .contact-info, .contact-form { padding: 25px; }
            .page-title { font-size: 1.8rem; }
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
            <h1 class="page-title"><?php echo $lang['contact_title']; ?></h1>

            <div class="contact-container">
                <div class="contact-info">
                    <h3><?php echo $lang['contact_title']; ?></h3>
                    <ul class="contact-details">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <h4><?php echo $lang['event_location']; ?></h4>
                                <p><?php echo $lang['contact_address']; ?></p>
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-phone"></i>
                            <div>
                                <h4><?php echo $lang['contact_phone']; ?></h4>
                                <p style="direction: ltr; text-align: <?php echo ($direction === 'rtl') ? 'right' : 'left'; ?>;">+963 956 22 77 90</p>
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <div>
                                <h4><?php echo $lang['contact_email']; ?></h4>
                                <p>info@svu-events.com</p>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="contact-form">
                    <h3><?php echo $lang['contact_send']; ?></h3>
                    <form id="contactForm" method="POST" action="contact.php">
                        <div class="form-group">
                            <label for="name"><?php echo $lang['contact_name']; ?></label>
                            <input type="text" id="name" name="name" class="form-control" required>
                            <div class="error-message" id="nameError"><?php echo $lang['contact_name']; ?></div>
                        </div>

                        <div class="form-group">
                            <label for="email"><?php echo $lang['contact_email']; ?></label>
                            <input type="email" id="email" name="email" class="form-control" required>
                            <div class="error-message" id="emailError"><?php echo $lang['contact_email']; ?></div>
                        </div>

                        <div class="form-group">
                            <label for="phone"><?php echo $lang['contact_phone']; ?></label>
                            <div class="phone-input">
                                <input type="tel" id="phone" name="phone" class="form-control" required pattern="9[0-9]{8}" maxlength="9">
                                <span class="phone-prefix">+963</span>
                            </div>
                            <div class="error-message" id="phoneError"><?php echo $lang['contact_phone_error']; ?></div>
                        </div>

                        <div class="form-group">
                            <label for="message"><?php echo $lang['contact_message']; ?></label>
                            <textarea id="message" name="message" class="form-control" required></textarea>
                            <div class="error-message" id="messageError"><?php echo $lang['contact_message']; ?></div>
                        </div>

                        <button type="submit" class="submit-btn" id="submitBtn">
                            <i class="fas fa-paper-plane"></i>
                            <?php echo $lang['contact_send']; ?>
                        </button>

                        <?php if ($form_error): ?>
                            <div class="form-message error"><?php echo $form_error; ?></div>
                        <?php endif; ?>
                        <?php if ($form_success): ?>
                            <div class="form-message success"><?php echo $form_success; ?></div>
                        <?php endif; ?>
                    </form>
                </div>
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
        // Cookie Consent Handler
        // =====================================
        $('#cookieAccept').click(function() {
            document.cookie = "cookie_consent=1; path=/; max-age=" + (60*60*24*30);
            $('#cookieConsent').fadeOut(400);
        });

        // =====================================
        // Client-Side Form Validation
        // =====================================
        $('#contactForm').on('submit', function (e) {
            var isValid = true;
            var name = $('#name').val().trim();
            var email = $('#email').val().trim();
            var phone = $('#phone').val().trim();
            var message = $('#message').val().trim();

            // Reset errors
            $('.form-control').removeClass('error');
            $('.error-message').removeClass('show');

            if (name === '') {
                $('#name').addClass('error');
                $('#nameError').addClass('show');
                isValid = false;
            }

            if (email === '') {
                $('#email').addClass('error');
                $('#emailError').addClass('show');
                isValid = false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                $('#email').addClass('error');
                $('#emailError').text('<?php echo $lang['contact_email_error']; ?>').addClass('show');
                isValid = false;
            }

            if (phone === '') {
                $('#phone').addClass('error');
                $('#phoneError').addClass('show');
                isValid = false;
            } else if (!/^9\d{8}$/.test(phone)) {
                $('#phone').addClass('error');
                $('#phoneError').addClass('show');
                isValid = false;
            }

            if (message === '') {
                $('#message').addClass('error');
                $('#messageError').addClass('show');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            }
        });

        // Live validation on blur
        $('#phone').on('blur', function () {
            var phone = $(this).val().trim();
            if (phone !== '' && !/^9\d{8}$/.test(phone)) {
                $(this).addClass('error');
                $('#phoneError').addClass('show');
            }
        });

        $('#name, #email, #message').on('input', function () {
            if ($(this).val().trim() !== '') {
                $(this).removeClass('error');
                $(this).next('.error-message').removeClass('show');
            }
        });
    });
    </script>
</body>
</html>