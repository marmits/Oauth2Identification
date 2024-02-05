<?php
declare(strict_types=1);

namespace Marmits\Oauth2Identification\Entity;

use DateTimeInterface;
use  Marmits\Oauth2Identification\Repository\OauthUserRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OauthUserRepository::class)
 */
class OauthUser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $providerName;

    /**
     * @ORM\Column(type="json")
     */
    private $ownerDetails = [];

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $accessToken;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $refreshToken;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $idApiUser;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?DateTimeInterface $dateConnexion;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProviderName(): ?string
    {
        return $this->providerName;
    }

    public function setProviderName(string $providerName): self
    {
        $this->providerName = $providerName;

        return $this;
    }

    public function getOwnerDetails(): ?array
    {
        return $this->ownerDetails;
    }

    public function setOwnerDetails(array $ownerDetails): self
    {
        $this->ownerDetails = $ownerDetails;

        return $this;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(?string $refreshToken): self
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getIdApiUser(): ?string
    {
        return $this->idApiUser;
    }

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
