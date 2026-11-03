<!-- ===================================== -->
<!-- Unified Header -->
<!-- ===================================== -->

<!-- Get current page name for active state -->
<?php $current_page = basename($_SERVER['PHP_SELF']); ?>

<!-- Language Switcher URL -->
<?php
$new_lang = ($lang_code === 'ar') ? 'en' : 'ar';
$switch_url = '?lang=' . $new_lang;
?>

<header>
    <div class="header-container">
        <!-- Hamburger Menu -->
        <div class="hamburger" id="hamburger">
            <i class="fas fa-bars"></i>
        </div>

        <!-- Navigation Menu -->
        <ul class="nav-menu" id="navMenu">
            <li>
                <a href="<?php echo $base_url; ?>index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>"><?php echo $lang['nav_home']; ?></a>
            </li>
            <li>
                <a href="<?php echo $base_url; ?>events.php" class="<?php echo ($current_page == 'events.php') ? 'active' : ''; ?>"><?php echo $lang['nav_events']; ?></a>
            </li>
            <li>
                <a href="<?php echo $base_url; ?>about.php" class="<?php echo ($current_page == 'about.php') ? 'active' : ''; ?>"><?php echo $lang['nav_about']; ?></a>
            </li>
            <li>
                <a href="<?php echo $base_url; ?>contact.php" class="<?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>"><?php echo $lang['nav_contact']; ?></a>
            </li>
        </ul>

        <!-- Logo -->
        <a href="index.php" class="logo">
            <img src="assets/img/SVU-Events-Logo.webp" alt="SVU Logo" />
        </a>

        <!-- Language Switcher (Hidden on Event Page) -->
        <?php if ($current_page !== 'event.php'): ?>
        <a href="<?php echo $switch_url; ?>" class="lang-switch">
            <?php echo ($lang_code === 'ar') ? 'EN' : 'AR'; ?>
        </a>
        <?php endif; ?>
    </div>
</header>