<?php
class ReportService
{
    private $conn;

    public function __construct($database)
    {
        $this->conn = $database;
    }

    public function getClientReport(array $filters): array
    {
        // echo "<pre>";
        // print_r($filters);
        // die;
        $sql = "";

        if( false ){
            $sql = "
            SELECT
                c.id,
                c.title,
                CONCAT(c.first_name, ' ', c.last_name) AS client_name,
                c.mobile1,
                c.birth_date,
                c.created_at,
                ca.country,
                ca.state,
                ca.city,
                ca.pincode,
                GROUP_CONCAT(c.company_name SEPARATOR ', ') AS services
            FROM clients c

            LEFT JOIN client_addresses ca
                ON ca.client_id = c.id AND ca.address_type = 2

            LEFT JOIN client_service_map csm
                ON csm.client_id = c.id

            LEFT JOIN company_services cs
                ON cs.id = csm.service_id

            WHERE 1=1
            ";

            $params = [];

            // Mobile
            if (!empty($filters['mobile'])) {
                $sql .= " AND (c.mobile1 LIKE ? OR c.mobile2 LIKE ?)";
                $mobile = '%' . trim($filters['mobile']) . '%';
                $params[] = $mobile;
                $params[] = $mobile;
            }

            // Birth Date
            if (!empty($filters['birth_date'])) {
                $sql .= " AND c.birth_date = ?";
                $params[] = $filters['birth_date'];
            }

            // Country
            if (!empty($filters['country'])) {
                $sql .= " AND ca.country LIKE ?";
                $params[] = '%' . trim($filters['country']) . '%';
            }

            // State
            if (!empty($filters['state'])) {
                $sql .= " AND ca.state LIKE ?";
                $params[] = '%' . trim($filters['state']) . '%';
            }

            // City
            if (!empty($filters['city'])) {
                $sql .= " AND ca.city LIKE ?";
                $params[] = '%' . trim($filters['city']) . '%';
            }

            // Pincode
            if (!empty($filters['pincode'])) {
                $sql .= " AND ca.pincode = ?";
                $params[] = trim($filters['pincode']);
            }

            // Services (Multi Select)
            if (!empty($filters['service_id']) && is_array($filters['service_id'])) {
                $ids = array_map('intval', $filters['service_id']);
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $sql .= " AND csm.service_id IN ($placeholders)";
                $params = array_merge($params, $ids);
            }

            // Created Date Range
            if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
                $sql .= " AND DATE(c.created_at) BETWEEN ? AND ?";
                $params[] = $filters['from_date'];
                $params[] = $filters['to_date'];
            }

            $sql .= " GROUP BY c.id ORDER BY c.created_at ASC";
            
        } else {
            $sql = "
                SELECT
                    c.id,
                    c.title,
                    CONCAT(c.first_name, ' ', c.last_name) AS client_name,
                    c.mobile1,
                    c.birth_date,
                    c.created_at,
                    ca.country,
                    ca.state,
                    ca.city,
                    ca.pincode,
                    GROUP_CONCAT(cs.company_name SEPARATOR ', ') AS services
                FROM clients c

                LEFT JOIN client_addresses ca
                    ON ca.client_id = c.id AND ca.address_type = 2

                LEFT JOIN client_company_map ccm
                    ON ccm.client_id = c.id

                LEFT JOIN companies cs
                    ON cs.id = ccm.company_id

                WHERE 1=1
            ";

            $params = [];

            /* -------------------- FILTERS -------------------- */

            // Mobile
            if (!empty($filters['mobile'])) {
                $sql .= " AND (c.mobile1 LIKE ? OR c.mobile2 LIKE ?)";
                $mobile = '%' . trim($filters['mobile']) . '%';
                $params[] = $mobile;
                $params[] = $mobile;
            }

            // Birth Date
            if (!empty($filters['birth_date'])) {
                $sql .= " AND c.birth_date = ?";
                $params[] = $filters['birth_date'];
            }

            // Country
            if (!empty($filters['country'])) {
                $sql .= " AND ca.country LIKE ?";
                $params[] = '%' . trim($filters['country']) . '%';
            }

            // State
            if (!empty($filters['state'])) {
                $sql .= " AND ca.state LIKE ?";
                $params[] = '%' . trim($filters['state']) . '%';
            }

            // City
            if (!empty($filters['city'])) {
                $sql .= " AND ca.city LIKE ?";
                $params[] = '%' . trim($filters['city']) . '%';
            }

            // Pincode
            if (!empty($filters['pincode'])) {
                $sql .= " AND ca.pincode = ?";
                $params[] = trim($filters['pincode']);
            }

            // Services (Multi-select)
            if (!empty($filters['service_id']) && is_array($filters['service_id'])) {
                $ids = array_map('intval', $filters['service_id']);
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $sql .= " AND ccm.company_id IN ($placeholders)";
                $params = array_merge($params, $ids);
            }

            // Created Date Range
            if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
                $sql .= " AND DATE(c.created_at) BETWEEN ? AND ?";
                $params[] = $filters['from_date'];
                $params[] = $filters['to_date'];
            }

            /* -------------------- DYNAMIC ORDER BY -------------------- */

            $orderBy = "c.created_at ASC"; // default order

            if (!empty($filters['mobile'])) {
                $orderBy = "c.mobile1 ASC";
            } elseif (!empty($filters['birth_date'])) {
                $orderBy = "c.birth_date ASC";
            } elseif (!empty($filters['country'])) {
                $orderBy = "ca.country ASC";
            } elseif (!empty($filters['state'])) {
                $orderBy = "ca.state ASC";
            } elseif (!empty($filters['city'])) {
                $orderBy = "ca.city ASC";
            } elseif (!empty($filters['pincode'])) {
                $orderBy = "ca.pincode ASC";
            } elseif (!empty($filters['service_id'])) {
                $orderBy = "services ASC";
            } elseif (!empty($filters['from_date']) && !empty($filters['to_date'])) {
                $orderBy = "c.created_at DESC";
            }

            /* -------------------- FINAL QUERY -------------------- */

            $sql .= " GROUP BY c.id ORDER BY $orderBy";

        }
            
        /* -------------------- EXECUTION -------------------- */
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        $result = $stmt->get_result(); // IMPORTANT
        
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;
    }
}