<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Action\UpdateProductAction;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['get:item:products']],
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['get:collection:products']],
        ),
        new Post(
            normalizationContext: ['groups' => ['get:item:products']],
            denormalizationContext: ['groups' => ['post:collection:products']],
            security: "is_granted('" . User::ROLE_ADMIN . "')"
        ),
        new Patch(
//            controller: UpdateProductAction::class,
            normalizationContext: ['groups' => ['get:item:products']],
            denormalizationContext: ['groups' => ['patch:item:products']]
        ),
        new Delete(),
    ],
    security: "is_granted('" . User::ROLE_ADMIN . "') or is_granted('" . User::ROLE_USER . "')"
)]
#[ApiFilter(SearchFilter::class, properties: ['name' => 'partial'])]
#[ApiFilter(RangeFilter::class, properties: ['createdAt'])]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Product
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'get:item:products',
        'get:collection:products'
    ])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups([
        'get:item:products',
        'get:collection:products',
        'post:collection:products',
        'patch:item:products'
    ])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups([
        'get:item:products',
        'post:collection:products',
        'patch:item:products'
    ])]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 6)]
    #[Assert\NotBlank]
    #[Assert\Type('numeric')]
    #[Groups([
        'get:item:products',
        'get:collection:products',
        'post:collection:products',
        'patch:item:products'
    ])]
    private ?string $price = null;

    #[Groups([
        'get:item:products',
        'get:collection:products',
        'post:collection:products'
    ])]
    #[ORM\ManyToOne(targetEntity: Category::class, cascade: [
        "persist",
        "remove"
    ], inversedBy: "products")]
    private Category $category;

    #[ORM\Column(type: "integer")]
    #[Groups([
        'get:item:products',
        'get:collection:products'
    ])]
    private int $createdAt;

    #[ORM\ManyToOne(targetEntity: User::class,inversedBy: "products")]
    private User $user;

    /**
     * Product constructor.
     */
    public function __construct()
    {
        //        $this->createdAt = time();
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
     * @return string|null
     */
    public function getPrice(): ?string
    {
        return $this->price;
    }

    /**
     * @param string $price
     * @return $this
     */
    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Category
     */
    public function getCategory(): Category
    {
        return $this->category;
    }

    /**
     * @param Category $category
     * @return $this
     */
    public function setCategory(Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return int
     */
    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    /**
     * @param int $createdAt
     * @return Product
     */
    public function setCreatedAt(int $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return void
     */
    #[ORM\PrePersist]
    public function initCreatedAt(): void
    {
        $this->createdAt = time();
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

}
