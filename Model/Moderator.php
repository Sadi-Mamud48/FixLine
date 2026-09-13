<?php
// FixLine/Model/Moderator.php
require_once __DIR__ . '/../Config/Database.php';

class Moderator {
    private $conn;

    // Updated constructor: initializes Database connection directly
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // 1. Fetch Customers
    public function getCustomers() {
        $stmt = $this->conn->prepare(
            "SELECT id, name, email AS gmail, created_at
             FROM users
             WHERE role = 'customer'
             ORDER BY id DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Fetch Providers
    public function getProvidersByStatus($status = null) {
        $query = "SELECT sp.id, u.name, u.email, sp.profession AS service_type,
                         NULL AS credentials_doc, sp.status
                  FROM service_providers sp
                  INNER JOIN users u ON u.id = sp.user_id";
        if ($status) {
            $stmt = $this->conn->prepare($query . " WHERE sp.status = ? ORDER BY sp.id DESC");
            $stmt->execute([$status]);
        } else {
            $stmt = $this->conn->prepare($query . " ORDER BY sp.id DESC");
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update Provider Status
    public function updateProviderStatus($id, $status) {
        $allowedStatuses = ['pending', 'approved', 'rejected'];
        if (!in_array($status, $allowedStatuses, true)) {
            return false;
        }

        $stmt = $this->conn->prepare("UPDATE service_providers SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    // 3. Fetch Complaints
    public function getComplaints() {
        if (!$this->tableExists('complaints')) {
            return [];
        }

        $query = "SELECT c.id, cust.name AS customer_name, p.name AS provider_name, 
                         c.issue_description, c.status, c.created_at 
                  FROM complaints c
                  JOIN customers cust ON c.customer_id = cust.id
                  JOIN providers p ON c.provider_id = p.id
                  ORDER BY c.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update Complaint Status
    public function updateComplaintStatus($id, $status) {
        if (!$this->tableExists('complaints')) {
            return false;
        }

        $stmt = $this->conn->prepare("UPDATE complaints SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    private function tableExists(string $table): bool {
        $stmt = $this->conn->prepare('SHOW TABLES LIKE ?');
        $stmt->execute([$table]);
        return (bool) $stmt->fetchColumn();
    }
}
