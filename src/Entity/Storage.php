<?php /** @noinspection PhpUnused */
/** @noinspection PhpUnusedAliasInspection */

/** @noinspection PhpPropertyOnlyWrittenInspection */

namespace App\Entity;

use App\Repository\StorageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Entity(repositoryClass=StorageRepository::class)
 */
class Storage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $name;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private ?string $postalCode;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private ?string $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $street;

    /**
     * @ORM\OneToMany(targetEntity=InStock::class, mappedBy="storage", orphanRemoval=true)
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private array|ArrayCollection|PersistentCollection $inStocks;

    #[Pure]
    public function __construct()
    {
        $this->inStocks = new ArrayCollection();
    }

    public function getAddress(): string
    {
        return $this->postalCode . ' ' . $this->city . ', ' . $this->street;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getInStocks(): Collection
    {
        return $this->inStocks;
    }

    public function addInStock(InStock $inStock): self
    {
        if (!$this->inStocks->contains($inStock)) {
            $this->inStocks[] = $inStock;
            $inStock->setStorage($this);
        }

        return $this;
    }

    public function removeInStock(InStock $inStock): self
    {
        if ($this->inStocks->removeElement($inStock)) {
            // set the owning side to null (unless already changed)
            if ($inStock->getStorage() === $this) {
                $inStock->setStorage(null);
            }
        }

        return $this;
    }
}
