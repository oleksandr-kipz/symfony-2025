<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Menu
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    private string $type;

    #[ORM\OneToMany(targetEntity: Dishes::class, mappedBy: "menu", cascade: [
        'persist',
        'remove'
    ])]
    private Collection $dishes;

    /**
     * Menu constructor.
     */
    public function __construct()
    {
        $this->dishes = new ArrayCollection();
    }

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
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getDishes(): Collection
    {
        return $this->dishes;
    }

    /**
     * @param Dishes $dishes
     */
    public function addDishes(Dishes $dishes): void
    {
        if (!$this->dishes->contains($dishes)) {
            $this->dishes[] = $dishes;
            $dishes->setMenu($this);
        }
    }

    /**
     * @param Dishes $dishes
     */
    public function removeDishes(Dishes $dishes): void
    {
        if ($this->dishes->contains($dishes)) {
            $this->dishes->removeElement($dishes);
            if ($dishes->getMenu() === $this) {
                $dishes->setMenu(null);
            }
        }
    }

}
