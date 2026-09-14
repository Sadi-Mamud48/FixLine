<?php 
require_once __DIR__ . '/../Config/Database.php';  

class Administrator {     
    private $db;      

    public function __construct() {         
        $this->db = Database::getConnection();         
        if (!$this->db) {             
            $this->initSessionData();         
        }     
    }      

    private function initSessionData() {         
        if (session_status() == PHP_SESSION_NONE) {             
            session_start();         
        }         
        if (!isset($_SESSION['users'])) {             
            $_SESSION['users'] = [                 
                'admin' => ['id'=>'admin','name'=>'FixLine Admin','email'=>'admin@fixline.com','password'=>'admin123','role'=>'Admin','phone'=>'+880 1700-000000','photo'=>'images/admin_avatar.png','status'=>'Active'],                 
                'plumber' => ['id'=>'plumber','name'=>'MD. Faiz Uddin','email'=>'faiz.plumber@fixline.com','password'=>'123456','role'=>'Service Provider','phone'=>'+880 1711-223344','photo'=>'images/plumber.png','status'=>'Active'],                 
                'electrician' => ['id'=>'electrician','name'=>'Tanvir Ahmed','email'=>'tanvir.elec@fixline.com','password'=>'123456','role'=>'Service Provider','phone'=>'+880 1812-345678','photo'=>'images/electrician.png','status'=>'Active'],                 
                'sarmin' => ['id'=>'sarmin','name'=>'Sarmin Akter','email'=>'sarmin.a@gmail.com','password'=>'123456','role'=>'Customer','phone'=>'+880 1913-987654','photo'=>'images/userinfo.png','status'=>'Active'],                 
                'afnan' => ['id'=>'afnan','name'=>'Afnan Chowdhury','email'=>'afnan.mod@fixline.com','password'=>'123456','role'=>'Moderator','phone'=>'+880 1614-556677','photo'=>'images/afnan.png','status'=>'Active']             
            ];         
        }     
    }      

    public function authenticate($email, $password) {         
        if ($this->db) {             
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");             
            $stmt->execute(['email' => $email]);             
            $user = $stmt->fetch();
            if ($user && (password_verify($password, $user['password']) || hash_equals((string) $user['password'], $password))) {
                return $user;
            }
            return false;         
        } else {             
            foreach ($_SESSION['users'] as $u) {                 
                if ($u['email'] === $email && $u['password'] === $password) {                     
                    return $u;                 
                }             
            }         
        }         
        return false;     
    }      

    public function getAllUsers() {         
        if ($this->db) {             
            $stmt = $this->db->query("SELECT u.*, sp.id AS provider_id, sp.profession, sp.affiliate, sp.experience, sp.bio, sp.profile_picture, sp.rating, sp.status AS provider_status
                FROM users u
                LEFT JOIN service_providers sp ON sp.user_id = u.id
                ORDER BY u.created_at DESC, u.id DESC");             
            $rows = $stmt->fetchAll();             
            $result = [];             
            foreach ($rows as $r) {                 
                $r['role_label'] = ucfirst((string) $r['role']);
                $r['status'] = $r['status'] ?: 'Active';
                $r['photo'] = $r['profile_photo'] ?: ($r['profile_picture'] ?: 'images/userinfo.png');
                $r['photo_src'] = $this->resolvePhotoPath($r['photo']);
                $result[$r['id']] = $r;             
            }             
            return $result;         
        }         
        return $_SESSION['users'];     
    }      

    public function getUserById($id) {         
        if ($this->db) {             
            $stmt = $this->db->prepare("SELECT u.*, sp.id AS provider_id, sp.profession, sp.affiliate, sp.experience, sp.bio, sp.profile_picture, sp.rating, sp.status AS provider_status
                FROM users u LEFT JOIN service_providers sp ON sp.user_id = u.id WHERE u.id = :id");             
            $stmt->execute(['id' => $id]);             
            $user = $stmt->fetch();
            if ($user) {
                $user['role_label'] = ucfirst((string) $user['role']);
                $user['status'] = $user['status'] ?: 'Active';
                $user['photo'] = $user['profile_photo'] ?: ($user['profile_picture'] ?: 'images/userinfo.png');
                $user['photo_src'] = $this->resolvePhotoPath($user['photo']);
            }
            return $user;         
        }         
        return $_SESSION['users'][$id] ?? null;     
    }      

    public function updateUser($id, $data) {         
        if ($this->db) {             
            $stmt = $this->db->prepare("UPDATE users SET name = :name, role = :role, phone = :phone, email = :email WHERE id = :id");             
            return $stmt->execute([                 
                'name' => $data['name'],                 
                'role' => $data['role'],                 
                'phone' => $data['phone'],                 
                'email' => $data['email'],                 
                'id' => $id             
            ]);         
        } else {             
            if (isset($_SESSION['users'][$id])) {                 
                $_SESSION['users'][$id]['name'] = $data['name'];                 
                $_SESSION['users'][$id]['role'] = $data['role'];                 
                $_SESSION['users'][$id]['phone'] = $data['phone'];                 
                $_SESSION['users'][$id]['email'] = $data['email'];                 
                return true;             
            }         
        }         
        return false;     
    }      

    public function updateUserEmail($oldEmail, $newEmail) {         
        if ($this->db) {             
            $stmt = $this->db->prepare("UPDATE users SET email = :newEmail WHERE email = :oldEmail");             
            return $stmt->execute(['newEmail' => $newEmail, 'oldEmail' => $oldEmail]);         
        } else {             
            foreach ($_SESSION['users'] as $key => $u) {                 
                if ($u['email'] === $oldEmail) {                     
                    $_SESSION['users'][$key]['email'] = $newEmail;                     
                    return true;                 
                }             
            }         
        }         
        return false;     
    }      

    public function updateUserPassword($email, $currentPass, $newPass) {         
        if ($this->db) {             
            $stmt = $this->db->prepare("UPDATE users SET password = :newPass WHERE email = :email AND password = :currentPass");             
            $stmt->execute(['newPass' => $newPass, 'email' => $email, 'currentPass' => $currentPass]);             
            return $stmt->rowCount() > 0;         
        } else {             
            foreach ($_SESSION['users'] as $key => $u) {                 
                if ($u['email'] === $email && $u['password'] === $currentPass) {                     
                    $_SESSION['users'][$key]['password'] = $newPass;                     
                    return true;                 
                }             
            }         
        }         
        return false;     
    }      

    public function toggleBlockUser($id) {         
        $user = $this->getUserById($id);         
        if ($user) {             
            $newStatus = ($user['status'] === 'Blocked') ? 'Active' : 'Blocked';             
            if ($this->db) {                 
                $stmt = $this->db->prepare("UPDATE users SET status = :status WHERE id = :id");                 
                $stmt->execute(['status' => $newStatus, 'id' => $id]);             
            } else {                 
                $_SESSION['users'][$id]['status'] = $newStatus;             
            }             
            return $newStatus;         
        }         
        return null;     
    }

    public function getDashboardData() {
        if (!$this->db) {
            return ['total_users' => count($_SESSION['users']), 'customers' => 1, 'providers' => 2, 'pending_providers' => 0, 'bookings' => 0, 'open_jobs' => 0, 'revenue' => 0, 'refunds' => 0, 'recent_users' => []];
        }

        return [
            'total_users' => $this->countRows('users'),
            'customers' => $this->countRows('users', "role = 'customer'"),
            'providers' => $this->countRows('service_providers'),
            'pending_providers' => $this->countRows('service_providers', "status = 'pending'"),
            'bookings' => $this->countRows('bookings'),
            'open_jobs' => $this->countRows('jobs', "status = 'open'"),
            'revenue' => $this->scalar('SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = \'paid\''),
            'refunds' => $this->countRows('refund_requests', "status = 'requested'"),
            'recent_users' => $this->getRecentUsers(5)
        ];
    }

    public function getAnalytics() {
        if (!$this->db) {
            return ['total_bookings' => 0, 'active_users' => count($_SESSION['users']), 'total_revenue' => 0, 'pending_requests' => 0, 'completed_services' => 0, 'top_providers' => []];
        }

        return [
            'total_bookings' => $this->countRows('bookings'),
            'active_users' => $this->countRows('users', "role IN ('customer', 'provider')"),
            'total_revenue' => $this->scalar('SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = \'paid\''),
            'pending_requests' => $this->countRows('service_requests', "status = 'new'"),
            'completed_services' => $this->countRows('bookings', "status = 'completed'"),
            'top_providers' => $this->getTopProviders()
        ];
    }

    public function updateProviderStatus($providerId, $status) {
        if (!$this->db || !in_array($status, ['approved', 'rejected', 'pending'], true)) {
            return false;
        }
        $stmt = $this->db->prepare('UPDATE service_providers SET status = :status WHERE id = :id');
        return $stmt->execute(['status' => $status, 'id' => (int) $providerId]);
    }

    private function countRows($table, $where = '1 = 1') {
        try {
            return (int) $this->db->query("SELECT COUNT(*) FROM {$table} WHERE {$where}")->fetchColumn();
        } catch (Throwable $exception) {
            return 0;
        }
    }

    private function scalar($sql) {
        try {
            return $this->db->query($sql)->fetchColumn() ?: 0;
        } catch (Throwable $exception) {
            return 0;
        }
    }

    private function getRecentUsers($limit) {
        try {
            $stmt = $this->db->prepare('SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC, id DESC LIMIT :limit');
            $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Throwable $exception) {
            return [];
        }
    }

    private function getTopProviders() {
        try {
            $stmt = $this->db->query("SELECT u.name, sp.profession AS service,
                       COALESCE(AVG(r.rating), 0.0) AS rating
                FROM service_providers sp JOIN users u ON u.id = sp.user_id
                LEFT JOIN services s ON s.provider_id = sp.id
                LEFT JOIN reviews r ON r.service_id = s.id
                WHERE sp.status = 'approved'
                GROUP BY sp.id, u.name, sp.profession
                ORDER BY rating DESC, u.name LIMIT 5");
            return $stmt->fetchAll();
        } catch (Throwable $exception) {
            return [];
        }
    }

    private function resolvePhotoPath($photo) {
        if (!$photo) {
            return 'View/images/userinfo.png';
        }
        if (strpos($photo, '/') === 0) {
            return $photo;
        }
        if (strpos($photo, 'uploads/') === 0) {
            return '/FixLine/' . $photo;
        }
        return 'View/' . ltrim($photo, '/');
    }
}