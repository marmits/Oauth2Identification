<?php
declare(strict_types=1);

namespace Marmits\GoogleIdentification\Services;

use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
Use Exception;
use Marmits\GoogleIdentification\Services\Encryption;

/**
 * GERE LE TRAITEMENT ET LA SECURITE DES CREDENTIALS
 */
class Access
{
    protected array $params;
    protected string $password;
    protected string $identifiant;
    protected Encryption $encryption;

    /**
     * @param array $private_params
     * @param Encryption $encryption
     */
    public function __construct(array $private_params, Encryption $encryption)
    {
        $this->params = $private_params;
        $this->encryption = $encryption;
    }

    /**
     * @param string $val
     * @return $this
     */
    public function setPassword(string $val): Access
    {
        $this->password = $val;
        return $this;
    }

    /**
     * @param string $val
     * @return $this
     */
    public function setIdentifiant(string $val): Access
    {
        $this->identifiant = $val;
        return $this;
    }

    /**
     * @return string
     */
    public function getIdentifiant(): string
    {
        return $this->identifiant;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getRealPassword():string{
        return $this->params['private_params']['passwordfull'];
    }

    /**
     * @return string
     */
    public function geFirstPartPassword():string{
        return $this->params['private_params']['passwordfirst'];
    }

    /**
     * @return false|mixed
     */
    public function checkCrediental(){

        $isValid = false;

        if($this->getIdentifiant() === $this->getIdentifiantParam()) {
            $password = $this->getPassword();
            $isValid = $this->VerifPassHash($password);
        }
        return $isValid;

    }

    /**
     * @param $val
     * @return mixed
     */
    private function VerifPassHash($val){

        $factory = new PasswordHasherFactory([
            'common' => ['algorithm' => 'bcrypt'],
            'memory-hard' => ['algorithm' => 'sodium'],
        ]);

        $passwordHasher = $factory->getPasswordHasher('common');
        $hash = $passwordHasher->hash($this->getRealPassword()); // returns a bcrypt hash

        return $passwordHasher->verify($hash, $val);

    }

    /**
     * @param $val
     * @return mixed
     */
    public function VerifIdentifiantPasswordHash($val){

        $factory = new PasswordHasherFactory([
            'common' => ['algorithm' => 'bcrypt'],
            'memory-hard' => ['algorithm' => 'sodium'],
        ]);

        $passwordHasher = $factory->getPasswordHasher('common');
        $hash = $passwordHasher->hash($this->getIdentifiantParam().$this->getRealPassword()); // returns a bcrypt hash

        return $passwordHasher->verify($hash,$val);

    }

    /**
     * @return mixed
     */
    public function getIdentifiantParam(){
        return $this->params['private_params']['identifiant'];
    }

    /**
     * @return bool
     */
    public function isParamCrypted() :bool{
        return $this->encryption->getParms()['encryption_params']['decrypt'];
    }

    /**
     * @param $contenu
     * @return string
     */
    public function getDatasCrypted($contenu): string
    {
        try {
            return nl2br($this->encryption->decrypt($contenu));
        }
        catch (Exception $e){
            return $e->getMessage();
        }

    }

}