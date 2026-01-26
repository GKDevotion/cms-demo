<?php
/**
 * Header Template - Common header for all pages
 */
// Detect if we're in a subdirectory (like /companies/)
$scriptPath = $_SERVER['PHP_SELF'];
$isSubdirectory = substr_count($scriptPath, '/') > 3; // More than /listing-page/file.php
$basePath = $isSubdirectory ? '../' : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title ?? 'Client Management'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/style.css">
