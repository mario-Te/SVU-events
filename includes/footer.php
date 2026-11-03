<!-- ===================================== -->
<!-- Unified Footer -->
<!-- ===================================== -->

<footer>
    <div class="footer-container">
        <!-- About Section -->
        <div class="footer-section">
            <h3><?php echo $lang['site_slogan']; ?></h3>
            <p><?php echo $lang['footer_desc']; ?></p>
        </div>

        <!-- Quick Links -->
        <div class="footer-section">
            <h3><?php echo $lang['footer_links']; ?></h3>
            <ul>
                <li><a href="<?php echo $base_url; ?>index.php"><?php echo $lang['nav_home']; ?></a></li>
                <li><a href="<?php echo $base_url; ?>events.php"><?php echo $lang['nav_events']; ?></a></li>
                <li><a href="<?php echo $base_url; ?>about.php"><?php echo $lang['nav_about']; ?></a></li>
                <li><a href="<?php echo $base_url; ?>contact.php"><?php echo $lang['nav_contact']; ?></a></li>
            </ul>
        </div>

        <!-- Social Media -->
        <div class="footer-section">
            <h3><?php echo $lang['footer_social']; ?></h3>
            <div class="social-icons">
                <a href="https://www.facebook.com/svuonline.org" target="_blank"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-whatsapp"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
    </div>

    <!-- Copyright -->
    <div class="footer-bottom">
        <p><?php echo $lang['footer_rights']; ?></p>
    </div>
</footer>