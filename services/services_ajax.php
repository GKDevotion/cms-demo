<?php
require_once '../config/Database.php';
require_once '../classes/Service.php';

$conn = Database::getInstance();
$service = new Service($conn);

$action = $_POST['action'] ?? '';

if ($action === 'fetch') {
    $page  = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $limit = 10;

    $data        = $service->getPaginated($page, $limit);
    $services    = $data['services'];
    $totalPages  = $data['totalPages'];
    $offset      = $data['offset'];

    $sr = $offset + 1;

    if (!empty($services)) {
          // Get all parent IDs first
        $parentIds = [];
        foreach ($services as $row) {
            if (!empty($row['parent_id'])) {
                $parentIds[] = (int)$row['parent_id'];
            }
        }
        
        // Fetch all parent names in one query
        $parentNames = [];
        if (!empty($parentIds)) {
            $ids = implode(',', array_unique($parentIds));
            $parentResult = $conn->query("SELECT id, name FROM company_services WHERE id IN ($ids)");
            while ($parent = $parentResult->fetch_assoc()) {
                $parentNames[$parent['id']] = $parent['name'];
            }
        }

        foreach ($services as $row) {
                  // Get parent name from our array
            $parentName = '-';
            if (!empty($row['parent_id']) && isset($parentNames[$row['parent_id']])) {
                $parentName = $parentNames[$row['parent_id']];
            }
            
            $parentID = $row['parent_id'] ?? '-'; 
            $statusText = $row['status'] == 1
                ? '<i class="bi bi-check-circle-fill text-success status-active"></i>'
                : '<i class="bi bi-x-circle-fill text-danger status-inactive"></i>';

            echo "
            <tr>
                <td>{$sr}</td>
                <td>{$row['name']}</td>
                <td>{$row['slug']}</td>
                <td>{$parentName}</td> 
                <td>
                    <span onclick='toggleStatus({$row['id']})' style='cursor:pointer'>
                        {$statusText}
                    </span>
                </td>
                <td>{$row['sort_order']}</td>
                <td>
                    <div class='action-wrapper'>
                        <button class='action-btn' onclick='toggleMenu(this)'>⋮</button>
                        <div class='action-menu'>
                            <a href='edit.php?id={$row['id']}'>Edit</a>
                            <a href='javascript:void(0)' onclick='deleteService({$row['id']})'>Delete</a>
                        </div>
                    </div>
                </td>
            </tr>
            ";
            $sr++;
        }
    } else {
        echo "<tr><td colspan='8' class='text-center'>No services found</td></tr>";
    }

    // Pagination
    echo "<tr><td colspan='8' class='text-center'>";
    echo "<div class='custom-pagination'>";

    $maxLinks = 5;
    $start = max(1, $page - floor($maxLinks / 2));
    $end   = min($totalPages, $start + $maxLinks - 1);

    if ($end - $start + 1 < $maxLinks) {
        $start = max(1, $end - $maxLinks + 1);
    }

    if ($page > 1) {
        echo "<span class='page' onclick='loadServices(" . ($page - 1) . ")'>&laquo;</span>";
    }

    for ($i = $start; $i <= $end; $i++) {
        $active = ($i == $page) ? 'active' : '';
        echo "<span class='page $active' onclick='loadServices($i)'>$i</span>";
    }

    if ($page < $totalPages) {
        echo "<span class='page' onclick='loadServices(" . ($page + 1) . ")'>&raquo;</span>";
    }

    echo "</div>";
    echo "</td></tr>";
}
if ($action === 'search') {
    $query = trim($_POST['query'] ?? '');

    if ($query === '') {
        $data = $service->getPaginated(1, 10);
        $services = $data['services'];
        $totalPages = $data['totalPages'];
        $offset = $data['offset'];
        
        // Generate paginated output like in 'fetch' action
        $sr = $offset + 1;
        
        if (!empty($services)) {
            foreach ($services as $row) {
                $parentID = $row['parent_id'] ?? '-';
                $typeText = $row['type'] == 1 ? 'MAINLAND' : 'FREEZONE';
                $statusText = $row['status'] == 1
                    ? '<i class="bi bi-check-circle-fill text-success status-active"></i>'
                    : '<i class="bi bi-x-circle-fill text-danger status-inactive"></i>';

                echo "
                <tr>
                    <td>{$sr}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['slug']}</td>
                    <td>{$parentID}</td>
                    <td>{$typeText}</td>
                    <td>
                        <span onclick='toggleStatus({$row['id']})' style='cursor:pointer'>
                            {$statusText}
                        </span>
                    </td>
                    <td>{$row['sort_order']}</td>
                    <td>
                        <div class='action-wrapper'>
                            <button class='action-btn' onclick='toggleMenu(this)'>⋮</button>
                            <div class='action-menu'>
                                <a href='edit.php?id={$row['id']}'>Edit</a>
                                <a href='javascript:void(0)' onclick='deleteService({$row['id']})'>Delete</a>
                            </div>
                        </div>
                    </td>
                </tr>
                ";
                $sr++;
            }
            
            // Pagination for empty search
            echo "<tr><td colspan='8' class='text-center'>";
            echo "<div class='custom-pagination'>";
            
            $maxLinks = 5;
            $start = max(1, 1 - floor($maxLinks / 2)); // Current page is 1
            $end   = min($totalPages, $start + $maxLinks - 1);
            
            if ($end - $start + 1 < $maxLinks) {
                $start = max(1, $end - $maxLinks + 1);
            }
            
            for ($i = $start; $i <= $end; $i++) {
                $active = ($i == 1) ? 'active' : '';
                echo "<span class='page $active' onclick='loadServices($i)'>$i</span>";
            }
            
            echo "</div>";
            echo "</td></tr>";
            
        } else {
            echo "<tr><td colspan='8' class='text-center'>No services found</td></tr>";
        }
        
    } else {
        $search = "%{$query}%";
        $sql = "
            SELECT s.*, p.name as parent_name
            FROM company_services s
            LEFT JOIN company_services p ON s.parent_id = p.id
            WHERE s.name LIKE ?
            ORDER BY s.id DESC
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $search);
        $stmt->execute();
        $result   = $stmt->get_result();
        $services = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        $sr = 1;
        if (!empty($services)) {
            foreach ($services as $row) {
                $parentID = $row['parent_id'] ?? '-'; 
                $statusText = $row['status'] == 1
                    ? '<i class="bi bi-check-circle-fill text-success status-active"></i>'
                    : '<i class="bi bi-x-circle-fill text-danger status-inactive"></i>';

                echo "
                <tr>
                    <td>{$sr}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['slug']}</td>
                    <td>{$parentID}</td> 
                    <td>
                        <span onclick='toggleStatus({$row['id']})' style='cursor:pointer'>
                            {$statusText}
                        </span>
                    </td>
                    <td>{$row['sort_order']}</td>
                    <td>
                        <div class='action-wrapper'>
                            <button class='action-btn' onclick='toggleMenu(this)'>⋮</button>
                            <div class='action-menu'>
                                <a href='edit.php?id={$row['id']}'>Edit</a>
                                <a href='javascript:void(0)' onclick='deleteService({$row['id']})'>Delete</a>
                            </div>
                        </div>
                    </td>
                </tr>
                ";
                $sr++;
            }
        } else {
            echo "<tr><td colspan='8' class='text-center'>No services found matching your search</td></tr>";
        }
    }
}

if ($action === 'delete') {
    $id = (int)$_POST['id'];
    echo $service->delete($id) ? 'success' : 'error';
}

if ($action === 'status') {
    $id = (int)$_POST['id'];
    echo $service->toggleStatus($id) ? 'success' : 'error';
}

$conn->close();
?>
