<?php

/**
 * Edit Client - Page to edit existing client
 */

require_once 'config/Database.php';
require_once 'classes/Client.php';

$page_title = 'Edit Client';
$errors = [];
$client = null;
$is_edit = true;

// Check if ID is provided
$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header("Location: index.php");
    exit;
}

// Get database connection
$client_obj = new Client($conn);

// Fetch client address (for edit display)
$client_address = $client_obj->getAddress($id);

// Fetch client data
$client = $client_obj->getById($id);
if (!$client) {
    header("Location: index.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => trim($_POST['title'] ?? ''),
        'first_name' => trim($_POST['first_name'] ?? ''),
        'second_name' => trim($_POST['second_name'] ?? ''),
        'last_name' => trim($_POST['last_name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'mobile1' => trim($_POST['mobile1'] ?? ''),
        'mobile2' => trim($_POST['mobile2'] ?? ''),
        'landline' => trim($_POST['landline'] ?? ''),
        'company_name' => trim($_POST['company_name'] ?? ''),
        'company_type' => trim($_POST['company_type'] ?? ''),
        'company_website' => trim($_POST['company_website'] ?? ''),
        'trn_no' => trim($_POST['trn_no'] ?? ''),
        'tax_no' => trim($_POST['tax_no'] ?? ''),
        'sms_notification' => trim($_POST['sms_notification'] ?? ''),
        'email_notification' => trim($_POST['email_notification'] ?? ''),
        'designation' => trim($_POST['designation'] ?? ''),
        'status' => $_POST['status'] ?? 1
    ];

    $addressData = [
        'address_type' => $_POST['address_type'],
        'address' => $_POST['address'],
        'city' => $_POST['city'],
        'state' => $_POST['state'],
        'country' => $_POST['country'],
        'pincode' => $_POST['pincode'],
        'country_code' => $_POST['country_code']
    ];



    // Validate data (exclude current client ID from email check)
    $errors = $client_obj->validateClient($data, $id);

    // If no errors, update data
    if (empty($errors)) {
        if ($client_obj->update($id, $data)) {

 

            $existingAddress = $client_obj->getAddress($id);

            if ($existingAddress) {
                $client_obj->updateAddress($id, $addressData);
            } else {
                $client_obj->addAddress($id, $addressData);
            }

            // Update company mappings: remove existing and add selected
            $selected_companies = $_POST['companies'] ?? [];
            $client_obj->removeCompanies($id);
            if (!empty($selected_companies)) {
                $client_obj->addCompanies($id, $selected_companies);
            }

            $selected_services = $_POST['company_services'] ?? [];
            $client_obj->removeServices($id);
            if (!empty($selected_services)) {
                $client_obj->addServices($id, $selected_services);
            }

            header("Location: index.php?success=1");
            exit;
        } else {
            $errors[] = "Error updating client. Please try again.";
        }
    }
}

// Fetch all active companies
$companies = $client_obj->getActiveCompanies();

// Get existing client companies
$client_companies = $client_obj->getClientCompanies($id);

$client_id = $id;
// Fetch all active company_services
$services = $client_obj->getActiveServices();

/* ====== IMPORTANT ======
   Build the service tree exactly like create page
========================== */


$serviceMap = [];
$company_services = [];

/* First pass: map */
foreach ($services as $service) {
    $service['children'] = [];
    $serviceMap[$service['id']] = $service;
}

/* Second pass: build tree */
foreach ($serviceMap as $serviceId => &$service) {

    if (!empty($service['parent_id']) && isset($serviceMap[$service['parent_id']])) {
        $serviceMap[$service['parent_id']]['children'][] = &$service;
    } else {
        $company_services[] = &$service;
    }
}
unset($service);

/* Get existing client services */
$client_services = $client_obj->getClientServices($client_id);


require_once 'views/header.php';
?>

<div class="container-narrow">
    <?php require_once 'views/form.php'; ?>
</div>

<?php require_once 'views/footer.php'; ?>