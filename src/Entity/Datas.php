<?php
declare(strict_types=1);
namespace Marmits\GoogleIdentification\Entity;

use App\Repository\DatasRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DatasRepository::class)
 */
class Datas
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
    private string $idApi;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $email;

    /**
     * @ORM\Column(type="text")
     */
    private ?string $contenu;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?DateTimeInterface $temps;

    /**
     * @ORM\Column(type="boolean", options={"default":"0"})
     */
    private bool $activate;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {

        return $this->id;
    }

    /**
     * @return string
     */
    public function getIdApi(): string
    {
        return $this->idApi;
    }

    /**
     * @param string $idApi
     * @return Datas
     */
    public function setIdApi(string $idApi): self
    {
        $this->idApi = $idApi;
        return $this;
    }

    /**
     * @param bool $activate
     * @return Datas
     */
    public function setActivate(bool $activate): self
    {
        $this->activate = $activate;
        return $this;
    }

    /**
     * @return bool
     */
    public function getActivate(): bool
    {
        return $this->activate;
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
    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    /**
     * @param string $contenu
     * @return $this
     */
    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getTemps(): ?DateTimeInterface
    {
        return $this->temps;
    }

    /**
     * @param DateTimeInterface $temps
     * @return $this
     */
    public function setTemps(DateTimeInterface $temps): self
    {
        $this->temps = $temps;

        return $this;
    }
}
