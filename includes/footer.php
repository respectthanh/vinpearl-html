    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3 class="footer-title">Vinpearl Nha Trang</h3>
                    <p class="footer-text">Experience luxury and comfort in our beachfront resort with world-class amenities.</p>
                    <div class="social-links">
                        <a href="#" class="social-link">Facebook</a>
                        <a href="#" class="social-link">Instagram</a>
                        <a href="#" class="social-link">Twitter</a>
                    </div>
                </div>
                <div class="footer-col">
                    <h3 class="footer-title">Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="<?php echo $base_url; ?>index.php">Home</a></li>
                        <li><a href="<?php echo $base_url; ?>pages/about/index.php">About</a></li>
                        <li><a href="<?php echo $base_url; ?>pages/rooms/index.php">Rooms</a></li>
                        <li><a href="<?php echo $base_url; ?>pages/tours/index.php">Tours</a></li>
                        <li><a href="<?php echo $base_url; ?>pages/packages/index.php">Packages</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3 class="footer-title">Contact</h3>
                    <address class="footer-contact">
                        <p>Hon Tre Island, Nha Trang</p>
                        <p>Khanh Hoa, Vietnam</p>
                        <p>Phone: +84 258 359 8888</p>
                        <p>Email: info@vinpearl.com</p>
                    </address>
                </div>
                <div class="footer-col">
                    <h3 class="footer-title">Newsletter</h3>
                    <p class="footer-text">Subscribe to our newsletter for the latest updates and special offers.</p>
                    <form class="newsletter-form">
                        <input type="email" placeholder="Your Email" class="newsletter-input">
                        <button type="submit" class="btn btn-primary newsletter-button">Subscribe</button>
                    </form>
                </div>
            </div>
            <div class="footer-bottom">
                <p class="copyright">© 2025 Vinpearl Nha Trang. All rights reserved.</p>
                <div class="footer-bottom-links">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="<?php echo $base_url; ?>js/main.js"></script>
    <?php if (isset($extra_js)) echo $extra_js; ?>
</body>
</html> 