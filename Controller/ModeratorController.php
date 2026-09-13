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

    public function createModerator($id, $name, $email) {
        return new Moderator($id, $name, $email);
    }
}
