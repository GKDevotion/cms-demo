<?php
/**
 * Create Company - Page to create new company
 */

require_once '../config/Database.php';
require_once '../classes/Company.php';

$page_title = 'Add Company';
$errors = [];
$company = null;
$is_edit = false;

// Get database connection
$company_obj = new Company($conn);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'company_name' => trim($_POST['company_name'] ?? ''),
        'status' => $_POST['status'] ?? 1
    ];

    // Validate data
    $errors = $company_obj->validateCompany($data);

    // If no errors, insert data
    if (empty($errors)) {
        $company_id = $company_obj->create($data);

        if ($company_id) {
            header("Location: index.php?success=1");
            exit;
        } else {
            $errors[] = "Error creating company. Please try again.";
        }
    }
}

require_once '../views/header.php'; ?>

<?php require_once '../views/company-form.php'; ?>

<?php require_once '../views/footer.php'; ?>
