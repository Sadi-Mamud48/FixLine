<?php 
require_once __DIR__ . '/../Model/Administrator.php';  

class AdministratorController {     
    private $adminModel;      

    public function __construct() {         
        if (session_status() == PHP_SESSION_NONE) {             
            session_start();         
        }         
        $this->adminModel = new Administrator();     
    }      

    public function handleRequest() {         
        $action = $_GET['action'] ?? 'login';          

        if (!$this->isLoggedIn() && !in_array($action, ['login', 'do_login'])) {             
            header("Location: index.php?action=login");             
            exit;         
        }          

        switch ($action) {             
            case 'login':                 
                $this->showLogin();                 
                break;             
            case 'do_login':                 
                $this->doLogin();                 
                break;             
            case 'logout':                 
                $this->logout();                 
                break;             
            case 'settings':                 
                $this->showSettings();                 
                break;             
            case 'update_settings_email':                 
                $this->updateSettingsEmail();                 
                break;             
            case 'update_settings_pass':                 
                $this->updateSettingsPass();                 
                break;             
            case 'dashboard':                 
                $this->showDashboard();                 
                break;             
            case 'account_management':                 
                $this->showAccountManagement();                 
                break;             
            case 'profile_detail':                 
                $this->showProfileDetail();                 
                break;             
            case 'save_profile':                 
                $this->saveProfile();                 
                break;             
            case 'toggle_block':                 
                $this->toggleBlock();                 
                break;             
            case 'update_provider_status':
                $this->updateProviderStatus();
                break;
            case 'analytics':                 
                $this->showAnalytics();                 
                break;             
            case 'users_info':                 
                $this->showUsersInfo();                 
                break;             
            default:                 
                $this->showDashboard();                 
                break;         
        }     
    }      

    private function isLoggedIn() {         
        if (isset($_SESSION['logged_user'])) {             
            return true;         
        }         
        if (isset($_COOKIE['remember_email'])) {             
            $_SESSION['logged_user'] = $_COOKIE['remember_email'];             
            return true;         
        }         
        return false;     
    }      

    private function showLogin() {         
        if ($this->isLoggedIn()) {             
            header("Location: index.php?action=dashboard");             
            exit;         
        }         
        require_once __DIR__ . '/../View/Administrator/login.php';     
    }      

    private function doLogin() {         
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {             
            $email = trim($_POST['email'] ?? '');             
            $password = trim($_POST['password'] ?? '');             
            $remember = isset($_POST['remember']);              

            $user = $this->adminModel->authenticate($email, $password);             
            if ($user) {                 
                $_SESSION['logged_user'] = $user['email'];                 
                $_SESSION['user_name'] = $user['name'];                 
                $_SESSION['user_role'] = $user['role'];                  

                if ($remember) {                     
                    setcookie('remember_email', $user['email'], time() + (86400 * 30), "/");                 
                }                 
                header("Location: index.php?action=dashboard");                 
                exit;             
            } else {                 
                header("Location: index.php?action=login&error=invalid");                 
                exit;             
            }         
        }     
    }      

    private function logout() {         
        session_destroy();         
        setcookie('remember_email', '', time() - 3600, "/");         
        header("Location: index.php?action=login");         
        exit;     
    }      

    private function showSettings() {         
        require_once __DIR__ . '/../View/Administrator/settings.php';     
    }      

    private function updateSettingsEmail() {         
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {             
            $oldEmail = $_SESSION['logged_user'] ?? '';             
            $newEmail = trim($_POST['new_email'] ?? '');              

            if ($oldEmail && $newEmail) {                 
                $this->adminModel->updateUserEmail($oldEmail, $newEmail);                 
                $_SESSION['logged_user'] = $newEmail;                  

                if (isset($_COOKIE['remember_email'])) {                     
                    setcookie('remember_email', $newEmail, time() + (86400 * 30), "/");                 
                }                  

                header("Location: index.php?action=settings&msg=email_updated");                 
                exit;             
            }         
        }         
        header("Location: index.php?action=settings");         
        exit;     
    }      

    private function updateSettingsPass() {         
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {             
            $email = $_SESSION['logged_user'] ?? '';             
            $currentPass = trim($_POST['current_password'] ?? '');             
            $newPass = trim($_POST['new_password'] ?? '');              

            if ($email && $currentPass && $newPass) {                 
                $success = $this->adminModel->updateUserPassword($email, $currentPass, $newPass);                 
                if ($success) {                     
                    header("Location: index.php?action=settings&msg=pass_updated");                 
                } else {                     
                    header("Location: index.php?action=settings&msg=pass_error");                 
                }                 
                exit;             
            }         
        }         
        header("Location: index.php?action=settings");         
        exit;     
    }      

    private function showDashboard() {         
        $dashboard = $this->adminModel->getDashboardData();
        require_once __DIR__ . '/../View/Administrator/dashboard.php';     
    }      

    private function showAccountManagement() {         
        $allUsers = $this->adminModel->getAllUsers();         
        $category = strtolower(trim($_GET['category'] ?? ''));         
        $category = $category !== '' ? $category : null;
        $search = strtolower($_GET['search'] ?? '');         
        require_once __DIR__ . '/../View/Administrator/account_management.php';     
    }      

    private function showProfileDetail() {         
        $id = $_GET['id'] ?? 'plumber';         
        $user = $this->adminModel->getUserById($id);         
        if (!$user) {             
            header("Location: index.php?action=account_management");             
            exit;         
        }         
        require_once __DIR__ . '/../View/Administrator/profile_detail.php';     
    }      

    private function saveProfile() {         
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {             
            $id = $_POST['id'] ?? '';             
            $role = strtolower(trim($_POST['role'] ?? 'customer'));
            $role = str_replace('service provider', 'provider', $role);
            $data = [                 
                'name' => $_POST['account_name'] ?? '',                 
                'role' => $role,
                'phone' => $_POST['phone_number'] ?? '',                 
                'email' => $_POST['email'] ?? ''             
            ];             
            $this->adminModel->updateUser($id, $data);             
            header("Location: index.php?action=profile_detail&id=" . urlencode($id) . "&msg=saved");             
            exit;         
        }     
    }      

    private function toggleBlock() {         
        $id = $_GET['id'] ?? '';         
        if ($id) {             
            $newStatus = $this->adminModel->toggleBlockUser($id);             
            header("Location: index.php?action=profile_detail&id=" . urlencode($id) . "&msg=" . urlencode($newStatus));             
            exit;         
        }     
    }      

    private function showAnalytics() {         
        $analytics = $this->adminModel->getAnalytics();
        require_once __DIR__ . '/../View/Administrator/analytics.php';     
    }      

    private function showUsersInfo() {         
        $users = $this->adminModel->getAllUsers();         
        require_once __DIR__ . '/../View/Administrator/users_info.php';     
    } 
    
    private function updateProviderStatus() {
        $providerId = (int) ($_POST['provider_id'] ?? 0);
        $status = $_POST['status'] ?? '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $providerId > 0) {
            $this->adminModel->updateProviderStatus($providerId, $status);
        }
        header('Location: index.php?action=account_management&category=provider');
        exit;
    }
}
