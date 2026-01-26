<?php
require_once '../views/header.php';
?>

<div class="container">
    <div class="top-bar">
        <span style="font-size: 2rem;">Services</span>
        <div class="search-box">
            <input type="text" id="search" placeholder="Search The Services">
        </div>
        <a href="create.php" class="btn">+ Add Service</a>
    </div>

    <table width="100%">
        <thead>
            <tr>
                <th>Sr</th>
                <th>Name</th>
                <th>Slug</th>
                <th>Parent</th> 
                <th>Status</th>
                <th>Sort</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="serviceTable"></tbody>
    </table>
</div>

<footer class="footer">
    <div class="footer-content">
        <div class="footer-left">
            <p>© 2026 Devotion. All Rights Reserved.</p>
        </div>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="<?php echo $basePath; ?>assets/js/services.js"></script>
