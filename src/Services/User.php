<?php

namespace Marmits\GoogleIdentification\Services;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, PasswordAuthenticatedUserInterface
{

    private int $identifiant = 1;
    private string $username;
    private $passwordHasher;

    /**
     * @param array $private_params
     */
    public function __construct(array $private_params, $passwordHasher = null)
    {
        $this->params = $private_params;
        $this->setUsername($this->params["private_params"]['identifiant']);
        $this->passwordHasher = $passwordHasher;


    }

    public function setUsername($val){
        $this->username = $val;
    }



    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    public function getRoles(){

    }

    public function setPassword(string $password): self
    {
        error_log($password);
        $this->password = $password;

        return $this;
    }

    public function getPassword(): ?string{
        return $this->params["private_params"]['password'];
    }
    public function getSalt(){

    }
    public function eraseCredentials(){

    }

    public function getUsername(){
        return $this->getUserIdentifier();
    }


}