<?php
// FixLine/Controller/ModeratorController.php

require_once __DIR__ . '/../Model/Moderator.php';

class ModeratorController {

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