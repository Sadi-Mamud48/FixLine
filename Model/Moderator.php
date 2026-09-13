<?php
// FixLine/Model/Moderator.php
require_once __DIR__ . '/../config/db.php';

class Moderator {
    private $conn;

    // Updated constructor: initializes Database connection directly
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // 1. Fetch Customers
    public function getCustomers() {
        $stmt = $this->conn->prepare("SELECT id, name, gmail, status, created_at FROM customers ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Fetch Providers
    public function getProvidersByStatus($status = null) {
        if ($status) {
            $stmt = $this->conn->prepare("SELECT * FROM providers WHERE status = ? ORDER BY id DESC");
            $stmt->execute([$status]);
        } else {
            $stmt = $this->conn->prepare("SELECT * FROM providers ORDER BY id DESC");
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update Provider Status
    public function updateProviderStatus($id, $status) {
        $stmt = $this->conn->prepare("UPDATE providers SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    // 3. Fetch Complaints
    public function getComplaints() {
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
        $stmt = $this->conn->prepare("UPDATE complaints SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }
}