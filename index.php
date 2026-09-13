<?php 
// FixLine - Main Entry Point Router 
require_once __DIR__ . '/Controller/AdministratorController.php';  

$controller = new AdministratorController(); 
$controller->handleRequest();