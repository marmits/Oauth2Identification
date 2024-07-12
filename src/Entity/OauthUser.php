<?php
declare(strict_types=1);

namespace Marmits\Oauth2Identification\Entity;


use DateTimeInterface;
use Marmits\Oauth2Identification\Repository\OauthUserRepository;
use Doctrine\ORM\Mapping as ORM;


/**
 *
 */
#[ORM\Entity(repositoryClass: OauthUserRepository::class)]
class OauthUser
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column (type: 'integer')]
    private $id;


    #[ORM\Column (type: 'string', length: 255)]
    private ?string $providerName;


    #[ORM\Column (type: 'json')]
    private array $ownerDetails = [];


    #[ORM\Column (type: 'string', length: 255)]
    private ?string $accessToken;


    #[ORM\Column (type: 'string', length: 255, nullable: true)]
    private ?string $refreshToken;

    #[ORM\Column (type: 'string', length: 255)]
    private ?string $email;

    #[ORM\Column (type: 'string', length: 255)]
    private ?string $idApiUser;

    #[ORM\Column (type: 'datetime')]
    private ?DateTimeInterface $dateConnexion;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getProviderName(): ?string
    {
        return $this->providerName;
    }

    /**
     * @param string $providerName
     * @return $this
     */
    public function setProviderName(string $providerName): self
    {
        $this->providerName = $providerName;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getOwnerDetails(): ?array
    {
        return $this->ownerDetails;
    }

    /**
     * @param array $ownerDetails
     * @return $this
     */
    public function setOwnerDetails(array $ownerDetails): self
    {
        $this->ownerDetails = $ownerDetails;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     * @return $this
     */
    public function setAccessToken(string $accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    /**
     * @param string|null $refreshToken
     * @return $this
     */
    public function setRefreshToken(?string $refreshToken): self
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getIdApiUser(): ?string
    {
        return $this->idApiUser;
    }

    /**
     * @param string $idApiUser
     * @return $this
     */
    public function setIdApiUser(string $idApiUser): self
    {
        $this->idApiUser = $idApiUser;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDateConnexion(): ?DateTimeInterface
    {
        return $this->dateConnexion;
    }

    /**
     * @param DateTimeInterface|null $dateConnexion
     * @return OauthUser
     */
    public function setDateConnexion(?DateTimeInterface $dateConnexion): self
    {
        $this->dateConnexion = $dateConnexion;
        return $this;
    }
}
