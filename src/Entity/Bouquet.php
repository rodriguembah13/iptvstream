<?php

namespace App\Entity;

use App\Repository\BouquetRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BouquetRepository::class)
 */
class Bouquet
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isactive;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $chanelids;
    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $serieids;
    /**
     * @ORM\Column(type="integer", length=255, nullable=true)
     */
    private $bouquetorder;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $bouquetid;
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }
    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(?\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }
    public function isIsactive(): ?bool
    {
        return $this->isactive;
    }

    public function setIsactive(?bool $isactive): self
    {
        $this->isactive = $isactive;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getChanelids(): ?array
    {
        return $this->chanelids;
    }

    public function setChanelids(?array $chanelids): self
    {
        $this->chanelids = $chanelids;

        return $this;
    }

    /**
     * @return array
     */
    public function getSerieids(): array
    {
        return $this->serieids;
    }

    /**
     * @param array $serieids
     */
    public function setSerieids(array $serieids): void
    {
        $this->serieids = $serieids;
    }

    /**
     * @return mixed
     */
    public function getBouquetorder()
    {
        return $this->bouquetorder;
    }

    /**
     * @param mixed $bouquetorder
     */
    public function setBouquetorder($bouquetorder): void
    {
        $this->bouquetorder = $bouquetorder;
    }

    public function getBouquetid(): ?int
    {
        return $this->bouquetid;
    }

    public function setBouquetid(?int $bouquetid): self
    {
        $this->bouquetid = $bouquetid;

        return $this;
    }
}
