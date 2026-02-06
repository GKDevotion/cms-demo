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

    <!-- jQuery (required for Select2) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function () {

            if( $('#service_id').length > 0){
                $('#service_id').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Select services',
                    allowClear: true,
                    closeOnSelect: false,
                    width: '100%'
                });
            }
        });
    </script>
</body>

</html>
