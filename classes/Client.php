<?php

/**
 * Client Class - Handles all client-related operations
 */

class Client
{
    private $conn;

    public function __construct($database)
    {
        $this->conn = $database;
    }

    /**
     * Validation Rules
     */
    public static function validateFirstName($first_name)
    {
        $errors = [];
        if (empty($first_name)) {
            $errors[] = "First name is required.";
        } elseif (strlen($first_name) < 2) {
            $errors[] = "First name must be at least 2 characters.";
        } elseif (!preg_match('/^[a-zA-Z\s\-\']+$/', $first_name)) {
            $errors[] = "First name contains invalid characters.";
        }
        return $errors;
    }

    public static function validateLastName($last_name)
    {
        $errors = [];
        if (empty($last_name)) {
            $errors[] = "Last name is required.";
        } elseif (strlen($last_name) < 2) {
            $errors[] = "Last name must be at least 2 characters.";
        } elseif (!preg_match('/^[a-zA-Z\s\-\']+$/', $last_name)) {
            $errors[] = "Last name contains invalid characters.";
        }
        return $errors;
    }

    public static function validateEmail($email)
    {
        $errors = [];
        if (empty($email)) {
            $errors[] = "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }
        return $errors;
    }

    public static function validateMobileNumber($mobile_number)
    {
        $errors = [];
        if (empty($mobile_number)) {
            $errors[] = "Mobile number is required.";
        } elseif (!preg_match('/^[0-9\+\-\(\)\s]{10,20}$/', $mobile_number)) {
            $errors[] = "Invalid mobile number format. Use digits, spaces, +, -, or () only.";
        }
        return $errors;
    }

    public static function validateDesignation($designation)
    {
        $errors = [];
        if (empty($designation)) {
            $errors[] = "Designation is required.";
        } elseif (strlen($designation) < 2) {
            $errors[] = "Designation must be at least 2 characters.";
        }
        return $errors;
    }

    /**
     * Validate all fields
     */
    public function validateClient($data, $exclude_id = null)
    {
        $errors = [];

        $errors = array_merge($errors, self::validateFirstName($data['first_name']));
        $errors = array_merge($errors, self::validateLastName($data['last_name']));
        $errors = array_merge($errors, self::validateEmail($data['email']));
        $errors = array_merge($errors, self::validateMobileNumber($data['mobile_number']));
        $errors = array_merge($errors, self::validateDesignation($data['designation']));

        // Check if email exists
        if (empty($errors)) {
            $errors = array_merge($errors, $this->checkEmailExists($data['email'], $exclude_id));
        }

        return $errors;
    }

    /**
     * Check if email already exists
     */
    private function checkEmailExists($email, $exclude_id = null)
    {
        $errors = [];

        if ($exclude_id) {
            $stmt = $this->conn->prepare("SELECT id FROM clients WHERE email = ? AND id != ?");
            $stmt->bind_param("si", $email, $exclude_id);
        } else {
            $stmt = $this->conn->prepare("SELECT id FROM clients WHERE email = ?");
            $stmt->bind_param("s", $email);
        }

        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $errors[] = "Email already exists in the system.";
        }
        $stmt->close();

        return $errors;
    }

    /**
     * Get client by ID
     */
    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM clients WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $client = $result->num_rows > 0 ? $result->fetch_assoc() : null;
        $stmt->close();
        return $client;
    }

    /**
     * Create new client
     */
    public function create($data)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO clients (first_name, second_name, last_name, email, mobile_number, designation, status)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "ssssssi",
            $data['first_name'],
            $data['second_name'],
            $data['last_name'],
            $data['email'],
            $data['mobile_number'],
            $data['designation'],
            $data['status']
        );

        if ($stmt->execute()) {
            $client_id = $this->conn->insert_id;
            $stmt->close();
            return $client_id;
        } else {
            $stmt->close();
            return false;
        }
    }

    /**
     * Update client
     */
    public function update($id, $data)
    {
        $stmt = $this->conn->prepare("
            UPDATE clients 
            SET first_name = ?, second_name = ?, last_name = ?, 
                email = ?, mobile_number = ?, designation = ?, status = ?
            WHERE id = ?
        ");

        $stmt->bind_param(
            "ssssssii",
            $data['first_name'],
            $data['second_name'],
            $data['last_name'],
            $data['email'],
            $data['mobile_number'],
            $data['designation'],
            $data['status'],
            $id
        );

        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    /**
     * Delete client
     */
    public function delete($id)
    {
        $id = intval($id);
        return $this->conn->query("DELETE FROM clients WHERE id = $id");
    }

    /**
     * Toggle status
     */
    public function toggleStatus($id)
    {
        $id = intval($id);
        return $this->conn->query("UPDATE clients SET status = IF(status=1,0,1) WHERE id = $id");
    }

    /**
     * Get paginated clients
     */
    public function getPaginated($page = 1, $limit = 10)
    {
        $page = max($page, 1);
        $offset = ($page - 1) * $limit;

        // Get total records
        $totalResult = $this->conn->query("SELECT COUNT(*) AS total FROM clients");
        $totalRow = $totalResult->fetch_assoc();
        $totalRecords = $totalRow['total'];
        $totalPages = ceil($totalRecords / $limit);

        // Get paginated data
        $result = $this->conn->query("SELECT * FROM clients ORDER BY id DESC LIMIT $limit OFFSET $offset");

        return [
            'clients' => $result->fetch_all(MYSQLI_ASSOC),
            'totalRecords' => $totalRecords,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'offset' => $offset
        ];
    }

    /**
     * Add company to client
     */
    public function addCompany($client_id, $company_id)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO client_company_map (client_id, company_id)
            VALUES (?, ?)
        ");
        $stmt->bind_param("ii", $client_id, $company_id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    /**
     * Add multiple companies
     */
    public function addCompanies($client_id, $company_ids)
    {
        if (empty($company_ids)) return true;

        $stmt = $this->conn->prepare("
            INSERT INTO client_company_map (client_id, company_id , status)
            VALUES (?, ?, ?)
        ");

        foreach ($company_ids as $company_id) {
            $company_id = intval($company_id);
            $status = 1;   // or 0 based on requirement
            $stmt->bind_param("iii", $client_id, $company_id, $status);
            $stmt->execute();
        }
        $stmt->close();
        return true;
    }

    /**
     * Add multiple Services
     */
    public function addServices($client_id, $service_ids)
    {
        if (empty($service_ids)) return true;

        $stmt = $this->conn->prepare("
            INSERT INTO client_service_map (client_id, service_id, status)
            VALUES (?, ?, ?)
        ");

        foreach ($service_ids as $service_id) {
            $service_id = intval($service_id);
            $status = 1;   // or 0 based on requirement
            $stmt->bind_param("iii", $client_id, $service_id, $status);
            $stmt->execute();
        }
        $stmt->close();
        return true;
    }

    public function getActiveServices()
    {
        $result = $this->conn->query("
        SELECT id, name, parent_id
        FROM company_services
        WHERE status = 1
        ORDER BY 
            CASE WHEN parent_id IS NULL THEN 0 ELSE 1 END,
            parent_id ASC,
            id ASC
    ");

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }



    /**
     * Get all active companies
     */
    public function getActiveCompanies()
    {
        $result = $this->conn->query("SELECT id, company_name FROM companies WHERE status = 1 ORDER BY company_name ASC");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Remove all company mappings for a client
     */
    public function removeCompanies($client_id)
    {
        $stmt = $this->conn->prepare("DELETE FROM client_company_map WHERE client_id = ?");
        $stmt->bind_param("i", $client_id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    /**
     * Remove all services mappings for a client
     */
    public function removeServices($client_id)
    {
        $stmt = $this->conn->prepare("DELETE FROM client_service_map WHERE client_id = ?");
        $stmt->bind_param("i", $client_id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    /**
     * Get companies associated with a client
     */
    public function getClientCompanies($client_id)
    {
        $stmt = $this->conn->prepare("
            SELECT company_id FROM client_company_map WHERE client_id = ?
        ");
        $stmt->bind_param("i", $client_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $company_ids = [];
        while ($row = $result->fetch_assoc()) {
            $company_ids[] = $row['company_id'];
        }
        $stmt->close();
        return $company_ids;
    }

    public function getClientServices($client_id)
{
    $stmt = $this->conn->prepare("
        SELECT service_id FROM client_service_map WHERE client_id = ?
    ");
    $stmt->bind_param("i", $client_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $service_ids = [];
    while ($row = $result->fetch_assoc()) {
        $service_ids[$row['service_id']] = true;   // make lookup faster
    }

    $stmt->close();
    return $service_ids;
}

}
