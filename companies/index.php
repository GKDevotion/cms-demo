<?php
require_once '../views/header.php';
?>

<div class="container">

    <div class="top-bar">
        <span style="font-size: 2rem;">Companies</span>
        <div class="search-box">
            <input type="text" id="search" placeholder="Search The Companies">
        </div>

        <a href="create.php" class="btn">+ Add Companies</a>

    </div>
    <table width="100%">
        <thead>
            <tr>
                <th>Sr</th>
                <th>Company Name</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="companyTable"></tbody>
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
<script src="<?php echo $basePath; ?>assets/js/companies.js"></script>