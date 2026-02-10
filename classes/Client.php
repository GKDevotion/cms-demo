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

    public static function validateMobileNumber($mobile1)
    {
        $errors = [];
        if (empty($mobile1)) {
            $errors[] = "Mobile number is required.";
        } elseif (!preg_match('/^[0-9\+\-\(\)\s]{7,20}$/', $mobile1)) {
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


        $errors = array_merge($errors, self::validateLastName($data['last_name']));
        $errors = array_merge($errors, self::validateEmail($data['email']));
        $errors = array_merge($errors, self::validateMobileNumber($data['mobile1']));
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
            INSERT INTO clients ( client_uid, title, first_name, second_name, last_name, email,  mobile1, mobile2 , landline ,  company_name, company_type, company_website,trn_no, tax_no, sms_notification, email_notification,  designation,   birth_date,  status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "ssssssssssssssssssi",
            $data['client_uid'],
            $data['title'],
            $data['first_name'],
            $data['second_name'],
            $data['last_name'],
            $data['email'], 
            $data['mobile1'],
            $data['mobile2'],
            $data['landline'],
            $data['company_name'],
            $data['company_type'],
            $data['company_website'],
            $data['trn_no'],
            $data['tax_no'],
            $data['sms_notification'],
            $data['email_notification'],
            $data['designation'],
            $data['birth_date'],
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
            SET title = ?, first_name = ?, second_name = ?, last_name = ?, 
                email = ?,  mobile1 = ?, mobile2 = ?, landline = ?,    company_name = ?, company_type = ?, company_website = ? ,  trn_no = ?, tax_no = ?, sms_notification = ?, email_notification = ?, designation = ?, birth_date = ?, status = ?
            WHERE id = ?
        ");

        $stmt->bind_param(
            "ssssssssssssssssssi",
            $data['title'],
            $data['first_name'],
            $data['second_name'],
            $data['last_name'],
            $data['email'], 
            $data['mobile1'],
            $data['mobile2'],
            $data['landline'],
            $data['company_name'],
            $data['company_type'],
            $data['company_website'],
            $data['trn_no'],
            $data['tax_no'],
            $data['sms_notification'],
            $data['email_notification'],
            $data['designation'],
            $data['birth_date'],
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

        /* ===== TOTAL COUNT ===== */
        $totalSql = "SELECT COUNT(DISTINCT clients.id) AS total FROM clients";
        $totalResult = $this->conn->query($totalSql);
        $totalRow = $totalResult->fetch_assoc();
        $totalRecords = (int)$totalRow['total'];
        $totalPages = ceil($totalRecords / $limit);

        /* ===== FETCH CLIENTS WITH COMPANIES ===== */
        $sql = "
        SELECT 
            clients.*,
            GROUP_CONCAT(
                comp.company_name 
                ORDER BY comp.company_name 
                SEPARATOR ', '
            ) AS companies
        FROM clients
        LEFT JOIN client_company_map ccm 
            ON ccm.client_id = clients.id
        LEFT JOIN companies comp 
            ON comp.id = ccm.company_id
        GROUP BY clients.id
        ORDER BY clients.id DESC
        LIMIT ? OFFSET ?
    ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();

        return [
            'clients' => $result->fetch_all(MYSQLI_ASSOC),
            'totalRecords' => $totalRecords,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'offset' => $offset
        ];
    }


    public function addAddress($client_id, $data)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO client_addresses 
            (client_id, address_type, address, city, state, country, pincode)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "iisssss",
            $client_id,
            $data['address_type'],
            $data['address'],
            $data['city'],
            $data['state'],
            $data['country'],
            $data['pincode'],

        );

        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function getAddress($client_id)
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM client_addresses
            WHERE client_id = ?
            LIMIT 1
        ");
        $stmt->bind_param("i", $client_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $address = $result->fetch_assoc();
        $stmt->close();
        return $address;
    }

    public function updateAddress($client_id, $data)
    {
        $stmt = $this->conn->prepare("
            UPDATE client_addresses
            SET address = ?, city = ?, state = ?, country = ?, pincode = ?
            WHERE client_id = ? AND address_type = ?
        ");

        $stmt->bind_param(
            "ssssiis",
            $data['address'],
            $data['city'],
            $data['state'],
            $data['country'],
            $data['pincode'],
            $client_id,
            $data['address_type']
        );

        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }


    public function getAddressByType($client_id, $address_type)
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM client_addresses
            WHERE client_id = ? AND address_type = ?
            LIMIT 1
        ");
        $stmt->bind_param("ii", $client_id, $address_type);
        $stmt->execute();
        $result = $stmt->get_result();
        $address = $result->fetch_assoc();
        $stmt->close();
        return $address;
    }

    public function updateAddressByType($client_id, $address_type, $data)
    {
        $stmt = $this->conn->prepare("
            UPDATE client_addresses
            SET address = ?, city = ?, state = ?, country = ?, pincode = ?
            WHERE client_id = ? AND address_type = ?
        ");

        $stmt->bind_param(
            "sssssii",
            $data['address'],
            $data['city'],
            $data['state'],
            $data['country'],
            $data['pincode'],
            $client_id,
            $address_type
        );

        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function getAllAddresses($client_id)
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM client_addresses
            WHERE client_id = ?
        ");
        $stmt->bind_param("i", $client_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $addresses = [];
        while ($row = $result->fetch_assoc()) {
            $addresses[$row['address_type']] = $row; // map by type
        }
        $stmt->close();
        return $addresses;
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
            $service_ids[] = $row['service_id'];
        }


        $stmt->close();
        return $service_ids;
    }

    public function generateClientUID()
    {
        $today = date('ymd'); // 260210

        $sql = "
        SELECT client_uid 
        FROM clients 
        WHERE client_uid LIKE CONCAT(?, '%')
        ORDER BY client_uid DESC 
        LIMIT 1
    ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $today);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Extract last 3 digits and increment
            $lastSeq = (int)substr($row['client_uid'], -3);
            $nextSeq = str_pad($lastSeq + 1, 3, '0', STR_PAD_LEFT);
        } else {
            // First record of the day
            $nextSeq = '001';
        }

        $stmt->close();

        return $today . $nextSeq; // 260210001
    }
}
