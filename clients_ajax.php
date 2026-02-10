<?php

/**
 * AJAX Handler for Client Operations
 */

require_once 'config/Database.php';
require_once 'classes/Client.php';

$action = $_POST['action'] ?? '';
$client = new Client($conn);

/* ================= FETCH CLIENTS WITH PAGINATION ================= */
if ($action === 'fetch') {
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $limit = 10;

    // Get paginated data
    $data = $client->getPaginated($page, $limit);
    $clients = $data['clients'];
    $totalPages = $data['totalPages'];
    $offset = $data['offset'];

    $sr = $offset + 1;

    if (!empty($clients)) {
        foreach ($clients as $row) {

            $titleMap = [
                0 => 'Mr',
                1 => 'Mrs',
                2 => 'Miss',
                3 => 'Master'
            ];


            $title = $titleMap[(int)$row['title']] ?? '';

            $fullName = htmlspecialchars(
                trim($title . ' ' . $row['first_name'] . ' ' . $row['second_name'] . ' ' . $row['last_name'])
            );

            $statusText = $row['status'] == 1
                ? '<i class="bi bi-check-circle-fill text-success status-active"></i>'
                : '<i class="bi bi-x-circle-fill text-danger status-inactive"></i>';

            echo "
            <tr>
                <td>{$sr}</td>
                <td>{$fullName}</td>
                <td>{$row['email']}</td>
                <td>{$row['mobile1']}</td> 
                <td>
                    " . (!empty($row['companies'])
                ? "<span class='badge companies-listing text-white p-2'>" .
                str_replace(
                    ', ',
                    "</span> <span class='badge companies-listing text-white p-2'>",
                    htmlspecialchars($row['companies'])
                ) .
                "</span>"
                : "<span class='text-muted'>—</span>") . "
                </td>


                <td>{$row['created_at']}</td>
                <td>
                    <div class='action-wrapper'>
                        <button class='action-btn' onclick='toggleMenu(this)'>⋮</button>
                        <div class='action-menu'> 
                            <a href='edit.php?id={$row['id']}'>Edit</a>
                            <a href='javascript:void(0)' onclick='deleteClient({$row['id']})'>Delete</a>
                        </div>
                    </div>
                </td>
            </tr>
            ";
            $sr++;
        }
    } else {
        echo "<tr><td colspan='7' class='text-center'>No clients found</td></tr>";
    }

    // Pagination
    echo "<tr><td colspan='12' class='text-center'>";
    echo "<div class='custom-pagination'>";

    $maxLinks = 5;
    $start = max(1, $page - floor($maxLinks / 2));
    $end   = min($totalPages, $start + $maxLinks - 1);

    if ($end - $start + 1 < $maxLinks) {
        $start = max(1, $end - $maxLinks + 1);
    }

    // Previous
    if ($page > 1) {
        echo "<span class='page' onclick='loadClients(" . ($page - 1) . ")'>&laquo;</span>";
    }

    // Page numbers
    for ($i = $start; $i <= $end; $i++) {
        $active = ($i == $page) ? 'active' : '';
        echo "<span class='page $active' onclick='loadClients($i)'>$i</span>";
    }

    // Next
    if ($page < $totalPages) {
        echo "<span class='page' onclick='loadClients(" . ($page + 1) . ")'>&raquo;</span>";
    }

    echo "</div>";
    echo "</td></tr>";
}


/* ================= SEARCH CLIENTS ================= */
if ($action === 'search') {
    $query = $_POST['query'] ?? '';
    $query = trim($query);

    if (empty($query)) {
        // Return to normal listing
        $data = $client->getPaginated(1, 10);
        $clients = $data['clients'];
    } else {
        // Search in multiple fields including associated company name
        $searchQuery = "%$query%";

        $sql = "
                SELECT 
                    clients.*,
                    GROUP_CONCAT(
                        DISTINCT comp.company_name 
                        ORDER BY comp.company_name 
                        SEPARATOR ', '
                    ) AS companies
                FROM clients
                LEFT JOIN client_company_map ccm ON ccm.client_id = clients.id
                LEFT JOIN companies comp ON comp.id = ccm.company_id
                LEFT JOIN client_addresses ca ON ca.client_id = clients.id
                WHERE 
                    CONCAT(clients.first_name, ' ', clients.second_name, ' ', clients.last_name) LIKE ? 
                    OR clients.email LIKE ? 
                    OR clients.mobile1 LIKE ? 
                    OR clients.landline LIKE ? 
                    OR clients.company_name LIKE ? 
                    OR clients.company_type LIKE ?  
                    OR clients.company_website LIKE ? 
                    OR clients.designation LIKE ? 
                    OR clients.tax_no LIKE ? 
                    OR comp.company_name LIKE ? 
                    OR ca.address LIKE ?
                    OR ca.city LIKE ?
                    OR ca.state LIKE ?
                    OR ca.country LIKE ?
                    OR ca.pincode LIKE ?
                GROUP BY clients.id
                ORDER BY clients.id DESC
                ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sssssssssssssss", $searchQuery, $searchQuery, $searchQuery, $searchQuery, $searchQuery, $searchQuery, $searchQuery, $searchQuery, $searchQuery, $searchQuery, $searchQuery, $searchQuery, $searchQuery, $searchQuery, $searchQuery );
        $stmt->execute();
        $result = $stmt->get_result();
        $clients = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }

    $sr = 1;
    if (!empty($clients)) {
        foreach ($clients as $row) {
            $fullName = htmlspecialchars(
                $row['first_name'] . ' ' . $row['second_name'] . ' ' . $row['last_name']
            );

            $statusText = $row['status'] == 1
                ? '<i class="bi bi-check-circle-fill text-success status-active"></i>'
                : '<i class="bi bi-x-circle-fill text-danger status-inactive"></i>';

            echo "
            <tr>
                <td>{$sr}</td>
                <td>{$fullName}</td>
                <td>{$row['email']}</td>
                <td>{$row['mobile1']}</td> 
                  <td>
                    " . (!empty($row['companies'])
                ? "<span class='badge companies-listing text-white p-2'>" .
                str_replace(
                    ', ',
                    "</span> <span class='badge companies-listing text-white p-2'>",
                    htmlspecialchars($row['companies'])
                ) .
                "</span>"
                : "<span class='text-muted'>—</span>") . "
                </td>

                <td>
                    <div class='action-wrapper'>
                        <button class='action-btn' onclick='toggleMenu(this)'>⋮</button>
                        <div class='action-menu'> 
                            <a href='edit.php?id={$row['id']}'>Edit</a>
                            <a href='javascript:void(0)' onclick='deleteClient({$row['id']})'>Delete</a>
                        </div>
                    </div>
                </td>
            </tr>
            ";
            $sr++;
        }
    } else {
        echo "<tr><td colspan='7' class='text-center'>No clients found matching your search</td></tr>";
    }
}

/* ================= DELETE CLIENT ================= */
if ($action === 'delete') {
    $id = (int)$_POST['id'];
    if ($client->delete($id)) {
        echo 'success';
    } else {
        echo 'error';
    }
}

/* ================= TOGGLE STATUS ================= */
if ($action === 'status') {
    $id = (int)$_POST['id'];
    if ($client->toggleStatus($id)) {
        echo 'success';
    } else {
        echo 'error';
    }
}

$conn->close();
