<?php /** @noinspection PhpUnused */
/** @noinspection PhpUnusedAliasInspection */

/** @noinspection PhpPropertyOnlyWrittenInspection */

namespace App\Entity;

use App\Repository\KeyboardRepository;
use App\Repository\MonitorRepository;
use App\Repository\ProductRepository;
use App\Repository\SsdRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
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
    private ?string $className;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $articleNumber;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $price;

    /**
     * @ORM\ManyToOne(targetEntity=Brand::class, inversedBy="products")
     */
    private ?Brand $brand;

    /**
     * @ORM\OneToMany(targetEntity=InStock::class, mappedBy="product", orphanRemoval=true)
     */
    private array|ArrayCollection|PersistentCollection $inStocks;

    #[Pure]
    public function __construct()
    {
        $this->inStocks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArticleNumber(): ?string
    {
        return $this->articleNumber;
    }

    public function setArticleNumber(string $articleNumber): self
    {
        $this->articleNumber = $articleNumber;

        return $this;
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

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getClassName(): ?string
    {
        return $this->className;
    }

    public function setClassName(?string $className): self
    {
        $this->className = $className;

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
            $inStock->setProduct($this);
        }

        return $this;
    }

    public function removeInStock(InStock $inStock): self
    {
        if ($this->inStocks->removeElement($inStock)) {
            // set the owning side to null (unless already changed)
            if ($inStock->getProduct() === $this) {
                $inStock->setProduct(null);
            }
        }

        return $this;
    }
}
