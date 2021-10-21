<?php /** @noinspection PhpUnused */
/** @noinspection PhpPropertyOnlyWrittenInspection */

/** @noinspection PhpUnusedAliasInspection */

namespace App\Entity;

use App\Repository\InStockRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InStockRepository::class)
 */
class InStock
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $capacity;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $stock;

    /**
     * @ORM\ManyToOne(targetEntity=Storage::class, inversedBy="inStocks")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Storage $storage;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="inStocks")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Product $product;

    /**
     * Returns leftovers after operation.
     */
    public function changeProductQuantity(int $quantity): int
    {
        if ($quantity === 0) {
            return 0;
        }

        if ($quantity > 0) {
            // Increase stock
            $vacant = $this->capacity - $this->stock;
            if ($vacant >= $quantity) {
                $this->stock += $quantity;

                return 0;
            }
            $this->stock += $vacant;

            return $quantity - $vacant;
        }

        // Decrease stock ($quantity < 0)
        $quantity *= -1;
        if ($this->stock >= $quantity) {
            $this->stock -= $quantity;

            return 0;
        }
        $leftovers = $this->stock - $quantity;
        $this->stock = 0;

        return $leftovers;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getStorage(): ?Storage
    {
        return $this->storage;
    }

    public function setStorage(?Storage $storage): self
    {
        $this->storage = $storage;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }
}
