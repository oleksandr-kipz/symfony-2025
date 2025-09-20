<?php

namespace App\Entity;

use App\Repository\TableRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TableRepository::class)]
#[ORM\Table(name: '`table`')]
class Table
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private int $number;

    #[ORM\Column]
    private int $numberOfSeats;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getNumber(): ?int
    {
        return $this->number;
    }

    /**
     * @param int $number
     * @return $this
     */
    public function setNumber(int $number): static
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return int
     */
    public function getNumberOfSeats(): int
    {
        return $this->numberOfSeats;
    }

    /**
     * @param int $numberOfSeats
     * @return $this
     */
    public function setNumberOfSeats(int $numberOfSeats): static
    {
        $this->numberOfSeats = $numberOfSeats;

        return $this;
    }

}
