<?php
/**
 * Edit Company - Page to edit existing company
 */

require_once '../config/Database.php';
require_once '../classes/Company.php';

$page_title = 'Edit Company';
$errors = [];
$company = null;
$is_edit = true;

// Check if ID is provided
$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header("Location: index.php");
    exit;
}

// Get database connection
$company_obj = new Company($conn);

// Fetch company data
$company = $company_obj->getById($id);
if (!$company) {
    header("Location: index.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'company_name' => trim($_POST['company_name'] ?? ''),
        'status' => $_POST['status'] ?? 1
    ];

    // Validate data (exclude current company ID from name check)
    $errors = $company_obj->validateCompany($data, $id);

    // If no errors, update data
    if (empty($errors)) {
        if ($company_obj->update($id, $data)) {
            header("Location: index.php?success=1");
            exit;
        } else {
            $errors[] = "Error updating company. Please try again.";
        }
    }
}

require_once '../views/header.php';
?>

<?php require_once '../views/company-form.php'; ?>

<?php require_once '../views/footer.php'; ?>
