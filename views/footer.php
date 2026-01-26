<?php
/**
 * Footer Template - Common footer for all pages
 */
// Detect if we're in a subdirectory (like /companies/)
$scriptPath = $_SERVER['PHP_SELF'];
$isSubdirectory = substr_count($scriptPath, '/') > 3; // More than /listing-page/file.php
$basePath = $isSubdirectory ? '../' : '';
?>
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-left">
                <p>© 2026 Devotion. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="<?php echo $basePath; ?>assets/js/app.js"></script>
</body>

</html>
