<?php
namespace Marmits\GoogleIdentification\Services;


use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;

class Access
{
    protected array $params;
    protected string $password;
    protected string $identifiant;



    /**
     * @param array $private_params
     */
    public function __construct(array $private_params)
    {
        $this->params = $private_params;
    }

    public function setPassword(string $val){
        $this->password = $val;
        return $this;
    }

    public function setIdentifiant(string $val){
        $this->identifiant = $val;
        return $this;
    }

    public function getIdentifiant(){
        return $this->identifiant;
    }

    public function getPassword(){
        return  $this->password;
    }

    public function checkCrediental(){

        $isValid = false;
        $passwordInput = $this->getPassword();
        $identifiantInput = $this->getIdentifiant();
        if($identifiantInput === $this->getIdentifiantParam()) {
            $isValid = $this->VerifPassHash($passwordInput);
        }
        return $isValid;

    }

    private function VerifPassHash($val){

        $factory = new PasswordHasherFactory([
            'common' => ['algorithm' => 'bcrypt'],
            'memory-hard' => ['algorithm' => 'sodium'],
        ]);

        $passwordHasher = $factory->getPasswordHasher('common');
        $hash = $passwordHasher->hash($this->params["private_params"]['password']); // returns a bcrypt hash

        return $passwordHasher->verify($hash, $val);

    }

    public function VerifIdentifiantPasswordHash($val){

        $factory = new PasswordHasherFactory([
            'common' => ['algorithm' => 'bcrypt'],
            'memory-hard' => ['algorithm' => 'sodium'],
        ]);

        $passwordHasher = $factory->getPasswordHasher('common');
        $hash = $passwordHasher->hash($this->params["private_params"]['identifiant'].$this->params["private_params"]['password']); // returns a bcrypt hash

        return $passwordHasher->verify($hash,$val );

    }

    public function getIdentifiantParam(){
        return $this->params["private_params"]['identifiant'];
    }




}