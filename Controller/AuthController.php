<?php

require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../Model/Auth.php';

class AuthController
{
    private Auth $auth;

    public function __construct()
    {
        $database = new Database();
        $this->auth = new Auth($database->getConnection());
    }

    public function login(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = strtolower(trim($_POST['email'] ?? ''));
            $password = $_POST['password'] ?? '';
            $selectedRole = strtolower(trim($_POST['role'] ?? 'customer'));
            $user = $this->auth->findUserByEmail($email);
            $roleMatches = $user && ($user['status'] ?? 'Active') === 'Active' && (($selectedRole === 'customer' && $user['role'] === 'customer')
                || ($selectedRole === 'provider' && $user['role'] === 'provider')
                || ($selectedRole === 'other' && in_array($user['role'], ['admin', 'moderator', 'finance'], true)));

            $passwordMatches = $user && (password_verify($password, $user['password'])
                || hash_equals((string) $user['password'], $password));

            if (!$roleMatches || !$passwordMatches) {
                $_SESSION['auth_error'] = 'Invalid email or password.';
                header('Location: /FixLine/index.php?action=login');
                exit();
            }

            if ($user['role'] === 'provider') {
                $providerStatus = $this->auth->getProviderApprovalStatus((int) $user['id']);
                if ($providerStatus !== 'approved') {
                    $_SESSION['auth_error'] = match ($providerStatus) {
                        'rejected' => 'Your provider account was rejected. Please contact FixLine support.',
                        'pending' => 'Your provider account is awaiting administrator approval.',
                        default => 'Your provider account cannot be verified. Please contact FixLine support.',
                    };
                    header('Location: /FixLine/index.php?action=login');
                    exit();
                }
            }

            session_regenerate_id(true);
            unset($_SESSION['provider_id']);
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['logged_user'] = $user['email'];
            $_SESSION['user_name'] = $user['name'];

            $destination = match ($user['role']) {
                'provider' => '/FixLine/index.php?action=provider_dashboard',
                'admin' => '/FixLine/index.php?action=admin_dashboard',
                'moderator' => '/FixLine/index.php?action=moderator_dashboard',
                'finance' => '/FixLine/index.php?action=finance_dashboard',
                default => '/FixLine/index.php?action=customer_dashboard'
            };
            header("Location: {$destination}");
            exit();
        }

        require_once __DIR__ . '/../View/Auth/login.php';
    }

    public function registerCustomer(): void
    {
        $role = ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['account_type'] ?? 'customer') === 'provider')
            ? 'provider'
            : 'customer';
        $this->register($role);
    }

    public function registerProvider(): void
    {
        $this->register('provider');
    }

    private function register(string $role): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmation = $_POST['password_confirmation'] ?? '';

            try {
                if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new RuntimeException('Enter a valid name and email address.');
                }
                if (strlen($password) < 8 || $password !== $confirmation) {
                    throw new RuntimeException('Passwords must match and contain at least 8 characters.');
                }
                if (!preg_match('/^[0-9]{11}$/', $phone)) {
                    throw new RuntimeException('Phone number must contain exactly 11 digits.');
                }

                if ($role === 'provider') {
                    $this->auth->createProvider(
                        $name,
                        $email,
                        $password,
                        trim($_POST['profession'] ?? $_POST['work_type'] ?? ''),
                        $phone,
                        trim($_POST['affiliate'] ?? '') ?: null,
                        trim($_POST['experience'] ?? '') ?: null,
                        trim($_POST['bio'] ?? '') ?: null
                    );
                } else {
                    $this->auth->createCustomer($name, $email, $password, $phone);
                }

                $_SESSION['auth_message'] = 'Registration successful. You can now log in.';
                header('Location: /FixLine/index.php?action=login');
                exit();
            } catch (Throwable $exception) {
                $_SESSION['auth_error'] = $exception->getMessage();
                $target = $role === 'provider' ? 'provider_signup' : 'customer_signup';
                header("Location: /FixLine/index.php?action={$target}");
                exit();
            }
        }

        $view = $role === 'provider' ? 'provider_signup.php' : 'customer_signup.php';
        require_once __DIR__ . '/../View/Auth/' . $view;
    }
}
