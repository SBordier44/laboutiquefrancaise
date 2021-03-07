<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\OrderItemRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderItemRepository::class)
 */
class OrderItem
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="orderItems")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Order $parentOrder = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $productName = null;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $quantity = null;

    /**
     * @ORM\Column(type="float")
     */
    private ?float $price = null;

    /**
     * @ORM\Column(type="float")
     */
    private ?float $total = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $picture = null;

    public function __toString(): string
    {
        return $this->productName
            . ' (' . (number_format($this->price / 100, 2, ',', ' ')) . ' €)'
            . ' x'
            . $this->quantity
            . ' - '
            . number_format(($this->price / 100) * $this->quantity, 2, ',', ' ')
            . ' €';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParentOrder(): ?Order
    {
        return $this->parentOrder;
    }

    public function setParentOrder(?Order $parentOrder): self
    {
        $this->parentOrder = $parentOrder;

        return $this;
    }

    public function getProductName(): ?string
    {
        return $this->productName;
    }

    public function setProductName(string $productName): self
    {
        $this->productName = $productName;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }
}
