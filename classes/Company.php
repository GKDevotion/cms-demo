<?php

/**
 * Company Class - Handles all company-related operations
 */

class Company
{
    private $conn;

    public function __construct($database)
    {
        $this->conn = $database;
    }

    /**
     * Validation for company name
     */
    public static function validateCompanyName($company_name)
    {
        $errors = [];
        if (empty($company_name)) {
            $errors[] = "Company name is required.";
        } elseif (strlen($company_name) < 2) {
            $errors[] = "Company name must be at least 2 characters.";
        } elseif (strlen($company_name) > 255) {
            $errors[] = "Company name must not exceed 255 characters.";
        }
        return $errors;
    }

    /**
     * Validate all fields
     */
    public function validateCompany($data, $exclude_id = null)
    {
        $errors = [];

        $errors = array_merge($errors, self::validateCompanyName($data['company_name']));

        // Check if company name exists
        if (empty($errors)) {
            $errors = array_merge($errors, $this->checkCompanyNameExists($data['company_name'], $exclude_id));
        }

        return $errors;
    }

    /**
     * Check if company name already exists
     */
    private function checkCompanyNameExists($company_name, $exclude_id = null)
    {
        $errors = [];

        if ($exclude_id) {
            $stmt = $this->conn->prepare("SELECT id FROM companies WHERE company_name = ? AND id != ?");
            $stmt->bind_param("si", $company_name, $exclude_id);
        } else {
            $stmt = $this->conn->prepare("SELECT id FROM companies WHERE company_name = ?");
            $stmt->bind_param("s", $company_name);
        }

        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $errors[] = "Company name already exists in the system.";
        }
        $stmt->close();

        return $errors;
    }

    /**
     * Get company by ID
     */
    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM companies WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $company = $result->num_rows > 0 ? $result->fetch_assoc() : null;
        $stmt->close();
        return $company;
    }

    /**
     * Create new company
     */
    public function create($data)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO companies (company_name, status)
            VALUES (?, ?)
        ");

        $stmt->bind_param("si", $data['company_name'], $data['status']);

        if ($stmt->execute()) {
            $company_id = $this->conn->insert_id;
            $stmt->close();
            return $company_id;
        } else {
            $stmt->close();
            return false;
        }
    }

    /**
     * Update company
     */
    public function update($id, $data)
    {
        $stmt = $this->conn->prepare("
            UPDATE companies 
            SET company_name = ?, status = ?
            WHERE id = ?
        ");

        $stmt->bind_param("sii", $data['company_name'], $data['status'], $id);

        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    /**
     * Delete company
     */
    public function delete($id)
    {
        $id = intval($id);
        return $this->conn->query("DELETE FROM companies WHERE id = $id");
    }

    /**
     * Toggle status
     */
    public function toggleStatus($id)
    {
        $stmt = $this->conn->prepare("
        UPDATE companies
        SET status = IF(status = 1, 0, 1)
        WHERE id = ?
    ");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }


    /**
     * Get paginated companies
     */
    public function getPaginated($page = 1, $limit = 10)
    {
        $page = max($page, 1);
        $offset = ($page - 1) * $limit;

        // Get total records
        $totalResult = $this->conn->query("SELECT COUNT(*) AS total FROM companies");
        $totalRow = $totalResult->fetch_assoc();
        $totalRecords = $totalRow['total'];
        $totalPages = ceil($totalRecords / $limit);

        // Get paginated data
        $result = $this->conn->query("SELECT * FROM companies ORDER BY id DESC LIMIT $limit OFFSET $offset");

        return [
            'companies' => $result->fetch_all(MYSQLI_ASSOC),
            'totalRecords' => $totalRecords,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'offset' => $offset
        ];
    }

    /**
     * Get all active companies
     */
    public function getActiveCompanies()
    {
        $result = $this->conn->query("SELECT id, company_name FROM companies WHERE status = 1 ORDER BY company_name ASC");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}
