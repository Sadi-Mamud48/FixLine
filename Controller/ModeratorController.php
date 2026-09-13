<?php
// FixLine/Controller/ModeratorController.php

require_once __DIR__ . '/../Model/Moderator.php';

class ModeratorController {

    public function index() {
        // Render the moderator dashboard view
        require_once __DIR__ . '/../View/Moderator/index.php';
    }

    public function createModerator($id, $name, $email) {
        return new Moderator($id, $name, $email);
    }
}