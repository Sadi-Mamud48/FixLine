<?php

require_once 'Controller/AdministratorController.php';


$controllerName = $_GET['controller'] ?? 'administrator';

$action = $_GET['action'] ?? 'dashboard';


if ($controllerName === 'administrator') {

    $controller = new AdministratorController();


    if (method_exists($controller, $action)) {

        $controller->$action();

    } else {

        die("Action not found.");

    }

} else {

    die("Controller not found.");

}

?>