<?php

class Administrator
{

    private $users = [

        [
            'id' => 1,
            'name' => 'Plumber',
            'role' => 'Plumber',
            'phone' => '01700000001',
            'photo' => 'plumber.png'
        ],

        [
            'id' => 2,
            'name' => 'Electrician',
            'role' => 'Electrician',
            'phone' => '01700000002',
            'photo' => 'electrician.png'
        ],

        [
            'id' => 3,
            'name' => 'Sarmin',
            'role' => 'Customer',
            'phone' => '01700000003',
            'photo' => 'sarmin.png'
        ],

        [
            'id' => 4,
            'name' => 'Afnan',
            'role' => 'Customer',
            'phone' => '01700000004',
            'photo' => 'afnan.png'
        ]

    ];


    public function getUsers()
    {
        return $this->users;
    }


    public function getUserById($id)
    {

        foreach ($this->users as $user) {

            if ($user['id'] == $id) {

                return $user;

            }

        }

        return null;

    }


    public function updateUser(
        $id,
        $name,
        $role,
        $phone
    ) {

        /*
         * Database version will be added later.
         *
         * For now this method exists so that
         * Controller -> Model communication
         * is established.
         */

        return true;

    }

}

?>