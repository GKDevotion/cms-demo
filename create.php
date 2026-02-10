<?php

/**
 * Create Client - Page to create new client
 */

require_once 'config/Database.php';
require_once 'classes/Client.php';

$page_title = 'Add Client';
$errors = [];
$client = null;
$is_edit = false;

// Get database connection
$client_obj = new Client($conn);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' =>  trim($_POST['title'] ?? ''),
        'first_name' => trim($_POST['first_name'] ?? ''),
        'second_name' => trim($_POST['second_name'] ?? ''),
        'last_name' => trim($_POST['last_name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''), 
        'country_code' => trim($_POST['country_code'] ?? ''),
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
        'birth_date' => trim($_POST['birth_date'] ?? ''),
        'status' => $_POST['status'] ?? 1
    ];


    /* ADDRESS DATA */
    $addresses = [];

    $addressTypeMap = [
        'company'   => 1,
        'permanent' => 2,
        'current'   => 3
    ];

    if (!empty($_POST['address']) && is_array($_POST['address'])) {
        foreach ($_POST['address'] as $type => $addr) {

            if (empty($addr['address'])) {
                continue;
            }

            $addresses[] = [
                'address_type' => $addressTypeMap[$type],
                'address'      => trim($addr['address'] ?? ''),
                'city'         => trim($addr['city'] ?? ''),
                'state'        => trim($addr['state'] ?? ''),
                'country'      => trim($addr['country'] ?? ''),
                'pincode'      => trim($addr['pincode'] ?? ''), 
            ];
        }
    }



    // Validate data
    $errors = $client_obj->validateClient($data);

    // If no errors, insert data
    if (empty($errors)) {

        $client_id = $client_obj->create($data);

        if ($client_id) {


            foreach ($addresses as $address) {
                $client_obj->addAddress($client_id, $address);
            }

            // Companies
            $selected_companies = $_POST['companies'] ?? [];
            $client_obj->addCompanies($client_id, $selected_companies);

            // Services
            $selected_services = $_POST['company_services'] ?? [];
            $client_obj->addServices($client_id, $selected_services);

            header("Location: index.php?success=1");
            exit;
        } else {
            $errors[] = "Error creating client. Please try again.";
        }
    }
}

// Fetch all active companies
$companies = $client_obj->getActiveCompanies();
$client_companies = [];

// Fetch all active company_services
$services = $client_obj->getActiveServices();

$serviceMap = [];
$company_services = [];

/* First pass: map */
foreach ($services as $service) {
    $service['children'] = [];
    $serviceMap[$service['id']] = $service;
}

/* Second pass: build tree */
foreach ($serviceMap as $id => &$service) {
    if (!empty($service['parent_id']) && isset($serviceMap[$service['parent_id']])) {
        $serviceMap[$service['parent_id']]['children'][] = &$service;
    } else {
        $company_services[] = &$service;
    }
}
unset($service);

$client_services = [];


require_once 'views/header.php';
?>


<div class="container-narrow">
    <?php require_once 'views/form.php'; ?>
</div>

<?php require_once 'views/footer.php'; ?>