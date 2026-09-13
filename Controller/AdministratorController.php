<?php

class AdministratorController
{
    private $model;


    public function __construct()
    {
        require_once __DIR__ . '/../Model/Administrator.php';

        $this->model = new Administrator();
    }


    /*
     * PAGE 1
     * Administrator Dashboard
     */
    public function dashboard()
    {
        require_once __DIR__ . '/../View/Administrator/dashboard.php';
    }


    /*
     * PAGE 2
     * Account Management
     */
    public function accounts()
    {
        $users = $this->model->getUsers();

        require_once __DIR__ . '/../View/Administrator/accountManagement.php';
    }


    /*
     * PAGE 3
     * Account Details
     */
    public function accountDetails()
    {
        $id = $_GET['id'] ?? null;


        if ($id === null) {

            die("User ID is missing.");

        }


        $user = $this->model->getUserById($id);


        if ($user === null) {

            die("User not found.");

        }


        require_once __DIR__ . '/../View/Administrator/accountDetails.php';
    }


    /*
     * SAVE USER
     */
    public function saveUser()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $id = $_POST['id'];

            $name = $_POST['name'];

            $role = $_POST['role'];

            $phone = $_POST['phone'];


            $this->model->updateUser(
                $id,
                $name,
                $role,
                $phone
            );

        }


        header(
            "Location: index.php?controller=administrator&action=accounts"
        );

        exit;

    }

}

?>