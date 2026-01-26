<?php
class Service
{
    private $conn;
    private $table = "company_services";  // <-- change this to your real table name

    public function __construct($database)
    {
        $this->conn = $database;
    }

    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $services = $result->num_rows > 0 ? $result->fetch_assoc() : null;
        $stmt->close();
        return $services;
    }

    public function getPaginated($page = 1, $limit = 10)
    {
        $page = max(1, (int)$page);
        $limit = max(1, (int)$limit);
        $offset = ($page - 1) * $limit;

        // Get total records with prepared statement
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM {$this->table}");
        $stmt->execute();
        $totalResult = $stmt->get_result();
        $totalRow = $totalResult->fetch_assoc();
        $totalRecords = $totalRow['total'];
        $totalPages = ceil($totalRecords / $limit);
        $stmt->close();

        // Get paginated data WITH parent name
        $sql = "
        SELECT s.*, p.name as parent_name 
        FROM {$this->table} s
        LEFT JOIN {$this->table} p ON s.parent_id = p.id
        ORDER BY s.id DESC 
        LIMIT ? OFFSET ?
    ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $services = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return [
            'services' => $services,
            'totalRecords' => $totalRecords,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'offset' => $offset
        ];
    }

    public function toggleStatus($id)
    {
        $stmt = $this->conn->prepare("
            UPDATE {$this->table}
            SET status = IF(status = 1, 0, 1)
            WHERE id = ?
        ");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function getActiveCompanies()
    {
        $result = $this->conn->query("SELECT id, slug FROM {$this->table} WHERE status = 1 ORDER BY slug ASC");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = ?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
}
