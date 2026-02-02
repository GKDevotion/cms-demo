<?php
/**
 * Client Listing - Main page displaying all clients
 */

require_once 'config/Database.php';
require_once 'classes/ReportService.php';

$page_title = 'Client Report';

$pdo = Database::connect();
$reportService = new ReportService($pdo);

$filters = [
    'mobile'      => $_POST['mobile'] ?? null,
    'birth_date'  => $_POST['birth_date'] ?? null,
    'country'     => $_POST['country'] ?? null,
    'state'       => $_POST['state'] ?? null,
    'city'        => $_POST['city'] ?? null,
    'pincode'     => $_POST['pincode'] ?? null,
    'service_id'  => $_POST['service_id'] ?? [],
    'from_date'   => $_POST['from_date'] ?? null,
    'to_date'     => $_POST['to_date'] ?? null,
];

$data = $reportService->getClientReport($filters);
?>

<table border="1" cellpadding="5" style="width: 100%;">
    <thead>
        <tr style="background-color:#f2f2f2; font-weight:bold;">
        <th width="5%">ID</th>
        <th width="15%">Client Name</th>
        <th width="15%">Mobile</th>
        <th width="10%">Birth Date</th>
        <th width="12%">Country</th>
        <th width="12%">State</th>
        <th width="12%">City</th>
        <th width="10%">Pincode</th>
        <th width="19%">Services</th>
        </tr>
    </thead>
    <tbody>

    <?php
        // Loop data
        $html = "";
        foreach ($data as $row) {
            $titleMap = [
                0 => 'Mr',
                1 => 'Mrs',
                2 => 'Miss',
                3 => 'Master'
            ];

            $html .= '
            <tr>
                <td>'.$row['id'].'</td>
                <td>'.$titleMap[$row['title']].' '.$row['client_name'].'</td>
                <td>'.$row['mobile1'].'</td>
                <td>'.$row['birth_date'].'</td>
                <td>'.$row['country'].'</td>
                <td>'.$row['state'].'</td>
                <td>'.$row['city'].'</td>
                <td>'.$row['pincode'].'</td>
                <td>'.$row['services'].'</td>
            </tr>';
        }

        echo $html;
    ?>
    </tbody>
</table>

