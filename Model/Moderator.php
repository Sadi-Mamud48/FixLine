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
        $stmt = $this->conn->prepare("SELECT id, name, email AS gmail, status, created_at FROM users WHERE role = 'customer' ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Fetch Providers
    public function getProvidersByStatus($status = null) {
        if ($status) {
            $stmt = $this->conn->prepare("SELECT sp.id, u.name, u.email, sp.profession AS service_type, '' AS credentials_doc, sp.status, sp.created_at FROM service_providers sp JOIN users u ON u.id = sp.user_id WHERE sp.status = ? ORDER BY sp.id DESC");
            $stmt->execute([$status]);
        } else {
            $stmt = $this->conn->prepare("SELECT sp.id, u.name, u.email, sp.profession AS service_type, '' AS credentials_doc, sp.status, sp.created_at FROM service_providers sp JOIN users u ON u.id = sp.user_id ORDER BY sp.id DESC");
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update Provider Status
    public function updateProviderStatus($id, $status) {
        $stmt = $this->conn->prepare("UPDATE service_providers SET status = ? WHERE id = ?");
        if (!in_array($status, ['pending', 'approved', 'rejected'], true)) {
            return false;
        }
        return $stmt->execute([$status, $id]);
    }

    // 3. Fetch Complaints
    public function getComplaints() {
         $query = "SELECT c.id, cust.name AS customer_name, puser.name AS provider_name,
                    c.issue_description, c.status, c.created_at
                FROM complaints c
                JOIN users cust ON c.customer_id = cust.id
                JOIN service_providers p ON c.provider_id = p.id
                JOIN users puser ON p.user_id = puser.id
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