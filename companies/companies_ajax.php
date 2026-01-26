<?php
/**
 * AJAX Handler for Company Operations
 */

require_once '../config/Database.php';
require_once '../classes/Company.php';

$conn    = Database::getInstance();
$company = new Company($conn);

$action = $_POST['action'] ?? '';

/* ================= FETCH COMPANIES WITH PAGINATION ================= */
if ($action === 'fetch') {

    $page  = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $limit = 10;

    // Get paginated data
    $data        = $company->getPaginated($page, $limit);
    $companies   = $data['companies'];
    $totalPages = $data['totalPages'];
    $offset     = $data['offset'];

    $sr = $offset + 1;

    if (!empty($companies)) {
        foreach ($companies as $row) {

            $companyName = htmlspecialchars($row['company_name']);

                 $statusText = $row['status'] == 1
                ? '<i class="bi bi-check-circle-fill text-success status-active"></i>'
                : '<i class="bi bi-x-circle-fill text-danger status-inactive"></i>';
            echo "
            <tr>
                <td>{$sr}</td>
                <td>{$companyName}</td>
                <td>
                    <span onclick='toggleStatus({$row['id']})' style='cursor:pointer'>
                        {$statusText}
                    </span>
                    
                </td>
                <td>
                    <div class='action-wrapper'>
                        <button class='action-btn' onclick='toggleMenu(this)'>⋮</button>
                        <div class='action-menu'>
                            <a href='edit.php?id={$row['id']}'>Edit</a>
                            <a href='javascript:void(0)' onclick='deleteCompany({$row['id']})'>Delete</a>
                        </div>
                    </div>
                </td>
            </tr>
            ";
            $sr++;
        }
    } else {
        echo "<tr><td colspan='4' class='text-center'>No companies found</td></tr>";
    }

    /* ---------- Pagination ---------- */
    echo "<tr><td colspan='4' class='text-center'>";
    echo "<div class='custom-pagination'>";

    $maxLinks = 5;
    $start = max(1, $page - floor($maxLinks / 2));
    $end   = min($totalPages, $start + $maxLinks - 1);

    if ($end - $start + 1 < $maxLinks) {
        $start = max(1, $end - $maxLinks + 1);
    }

    // Previous
    if ($page > 1) {
        echo "<span class='page' onclick='loadCompanies(" . ($page - 1) . ")'>&laquo;</span>";
    }

    // Page numbers
    for ($i = $start; $i <= $end; $i++) {
        $active = ($i == $page) ? 'active' : '';
        echo "<span class='page $active' onclick='loadCompanies($i)'>$i</span>";
    }

    // Next
    if ($page < $totalPages) {
        echo "<span class='page' onclick='loadCompanies(" . ($page + 1) . ")'>&raquo;</span>";
    }

    echo "</div>";
    echo "</td></tr>";
}
 
/* ================= SEARCH CLIENTS BY COMPANY ================= */
 
if ($action === 'search') {

    $query = trim($_POST['query'] ?? '');

    if ($query === '') {
        // fallback to normal pagination
        $data      = $company->getPaginated(1, 10);
        $companies = $data['companies'];
    } else {

        $search = "%{$query}%";

        $sql = "
            SELECT *
            FROM companies
            WHERE company_name LIKE ?
            ORDER BY id DESC
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $search);
        $stmt->execute();
        $result    = $stmt->get_result();
        $companies = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }

    $sr = 1;

    if (!empty($companies)) {
        foreach ($companies as $row) {

            $companyName = htmlspecialchars(trim($row['company_name']));

            $statusText = $row['status'] == 1
                ? '<i class="bi bi-check-circle-fill text-success"></i>'
                : '<i class="bi bi-x-circle-fill text-danger"></i>';

            echo "
            <tr>
                <td>{$sr}</td>
                <td>{$companyName}</td>
                <td>
                    <span onclick='toggleStatus({$row['id']})' style='cursor:pointer'>
                        {$statusText}
                    </span>
                </td>
                <td>
                    <div class='action-wrapper'>
                        <button class='action-btn' onclick='toggleMenu(this)'>⋮</button>
                        <div class='action-menu'>
                            <a href='edit.php?id={$row['id']}'>Edit</a>
                            <a href='javascript:void(0)' onclick='deleteCompany({$row['id']})'>Delete</a>
                        </div>
                    </div>
                </td>
            </tr>
            ";
            $sr++;
        }
    } else {
        echo "<tr><td colspan='4' class='text-center'>No companies found</td></tr>";
    }
}




/* ================= DELETE CLIENT ================= */
if ($action === 'delete') {
    $id = (int)$_POST['id'];
    if ($company->delete($id)) {
        echo 'success';
    } else {
        echo 'error';
    }
}


/* ================= TOGGLE STATUS ================= */
 
if ($action === 'status') {
    $id = (int)$_POST['id'];
    if ($company->toggleStatus($id)) {
        echo 'success';
    } else {
        echo 'error';
    }
}
$conn->close();
