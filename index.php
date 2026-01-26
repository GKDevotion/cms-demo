<?php
/**
 * Client Listing - Main page displaying all clients
 */

require_once 'config/Database.php';
require_once 'classes/Client.php';

$page_title = 'Client Listing';

require_once 'views/header.php';
require_once 'views/list.php';
require_once 'views/footer.php';
?>
