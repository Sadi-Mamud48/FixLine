<?php 
require_once __DIR__ . '/Database.php';  

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
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email AND password = :password LIMIT 1");             
            $stmt->execute(['email' => $email, 'password' => $password]);             
            return $stmt->fetch();         
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
            $stmt = $this->db->query("SELECT * FROM users");             
            $rows = $stmt->fetchAll();             
            $result = [];             
            foreach ($rows as $r) {                 
                $result[$r['id']] = $r;             
            }             
            return $result;         
        }         
        return $_SESSION['users'];     
    }      

    public function getUserById($id) {         
        if ($this->db) {             
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");             
            $stmt->execute(['id' => $id]);             
            return $stmt->fetch();         
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
}