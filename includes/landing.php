<!-- ===================================== -->
<!-- Cookie Consent Banner -->
<!-- ===================================== -->
<?php if (!isset($_COOKIE['cookie_consent'])): ?>
<div id="cookieConsent" class="cookie-banner">
    <div class="cookie-content">
        <i class="fas fa-cookie-bite cookie-icon"></i>
        <p><?php echo $lang['cookie_message']; ?></p>
        <button id="cookieAccept" class="cookie-btn"><?php echo $lang['cookie_accept']; ?></button>
    </div>
</div>
<?php endif; ?>

<!-- ===================================== -->
<!-- Landing Page Overlay -->
<!-- ===================================== -->
<div id="landingPage" class="landing-page">
    <!-- SVU Logo -->
    <img src="assets/img/SVU-Events-icon.png" alt="SVU Icon" class="landing-logo">
    <!-- Country Name -->
    <h3 class="syria-name"><?php echo $lang['landing_slogan']; ?></h3>
    <!-- Welcome Text -->
    <h2 class="welcome-text"><?php echo $lang['landing_welcome']; ?></h2>
    <!-- Platform Name in Arabic -->
    <h2 class="platform-name-ar">دليل فعاليات الجامعة الافتراضية السورية</h2>
    <!-- Platform Name in English -->
    <h5 class="platform-name-en">SVU Events Guide</h5>
</div>