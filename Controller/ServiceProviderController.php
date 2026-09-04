<?php

require_once __DIR__ . "/../Model/ServiceProvider.php";

class ServiceProviderController
{
    public function createProvider($name, $email, $phone)
    {
        $provider = new ServiceProvider($name, $email, $phone);

        return $provider;
    }
}
?>