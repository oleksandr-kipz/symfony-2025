<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['get:item:categories']],
        ),
    ],
    security: "is_granted('" . User::ROLE_ADMIN . "') or is_granted('" . User::ROLE_USER . "')"
)]
#[ORM\Entity]
class Category
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'get:item:products',
        'get:collection:products',
        'get:item:categories'
    ])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups([
        'get:item:products',
        'get:collection:products',
        'get:item:categories'
    ])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups([
        'get:item:products',
        'get:collection:products',
        'get:item:categories'
    ])]
    private ?string $description = null;

    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: "category", cascade: [
        "persist",
        "remove"
    ])]
    private Collection $products;

    /**
     * Category constructor.
     */
    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

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
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return ArrayCollection|Collection
     */
    public function getProducts(): ArrayCollection|Collection
    {
        return $this->products;
    }

    /**
     * @param Product $product
     */
    public function addProducts(Product $product): void
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setCategory($this);
        }
    }

    /**
     * @param Product $product
     */
    public function removeProducts(Product $product): void
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            if ($product->getCategory() === $this) {
                $product->setCategory(null);
            }
        }
    }

}
