<?php
/**
 * Client List View - Displays clients in table format
 * 
 * Expected variables:
 * - $page_title: Page title
 */
$page_title = 'Client Listing';
require_once 'views/header.php';
require_once 'config/Database.php';
require_once 'classes/Client.php';

$client = new Client($conn);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$clientData = $client->getPaginated($page, 10);
?>

<div class="container">
    <div class="top-bar">
        <span style="font-size: 2rem;">Clients</span>
        <div class="search-box">
            <input type="text" id="search" placeholder="Search The Clients">
        </div>
        
        <a href="create.php" class="btn">+ Add Client</a>
      
    </div>

    <table>
        <thead>
            <tr>
                <th>Sr</th>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Designation</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="clientTable">
            <!-- Loaded via AJAX -->
        </tbody>
    </table>
</div>

<?php require_once 'views/footer.php'; ?>
