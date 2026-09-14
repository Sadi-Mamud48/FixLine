<?php
// FixLine/Controller/ModeratorController.php

require_once __DIR__ . '/../Model/Moderator.php';

class ModeratorController {

    public function handleRequest(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (($_SESSION['user_role'] ?? '') !== 'moderator') {
            header('Location: /FixLine/View/Auth/login.php');
            exit();
        }

        $action = $_GET['action'] ?? 'dashboard';
        $views = [
            'dashboard' => 'index.php',
            'customers' => 'customers.php',
            'providers' => 'providers.php',
            'complaints' => 'complaints.php',
            'account_management' => 'account_management.php',
        ];

        if (!isset($views[$action])) {
            http_response_code(404);
            echo 'Page not found';
            return;
        }

        require_once __DIR__ . '/../View/Moderator/' . $views[$action];
    }

    public function index() {
        // Render the moderator dashboard view
        require_once __DIR__ . '/../View/Moderator/index.php';
    }

    public function customers() {
        require_once __DIR__ . '/../View/Moderator/customers.php';
    }

    public function providers() {
        require_once __DIR__ . '/../View/Moderator/providers.php';
    }

    public function complaints() {
        require_once __DIR__ . '/../View/Moderator/complaints.php';
    }

    public function accountManagement() {
        require_once __DIR__ . '/../View/Moderator/account_management.php';
    }

    public function createModerator($id, $name, $email) {
        return new Moderator($id, $name, $email);
    }
}
