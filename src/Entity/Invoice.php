<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
class Invoice
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $reference = null;

    #[ORM\Column(length: 255)]
    private ?string $adressedTo = null;

    #[ORM\Column(length: 255)]
    private ?string $adressedMail = null;

    #[ORM\Column(length: 255)]
    private ?string $adressedPhone = null;

    #[ORM\Column(length: 255)]
    private ?string $housingIdentifier = null;

    #[ORM\Column]
    private ?float $housingTotal = null;

    #[ORM\Column]
    private ?float $adultsStayTax = null;

    #[ORM\Column]
    private ?float $childrenStayTax = null;

    #[ORM\Column]
    private ?float $adultsPoolTax = null;

    #[ORM\Column]
    private ?float $childrenPoolTax = null;

    #[ORM\Column]
    private ?float $totalPretax = null;

    #[ORM\Column]
    private ?float $totalAftertax = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getAdressedTo(): ?string
    {
        return $this->adressedTo;
    }

    public function setAdressedTo(string $adressedTo): self
    {
        $this->adressedTo = $adressedTo;

        return $this;
    }

    public function getAdressedMail(): ?string
    {
        return $this->adressedMail;
    }

    public function setAdressedMail(string $adressedMail): self
    {
        $this->adressedMail = $adressedMail;

        return $this;
    }

    public function getAdressedPhone(): ?string
    {
        return $this->adressedPhone;
    }

    public function setAdressedPhone(string $adressedPhone): self
    {
        $this->adressedPhone = $adressedPhone;

        return $this;
    }

    public function getHousingIdentifier(): ?string
    {
        return $this->housingIdentifier;
    }

    public function setHousingIdentifier(string $housingIdentifier): self
    {
        $this->housingIdentifier = $housingIdentifier;

        return $this;
    }

    public function getHousingTotal(): ?float
    {
        return $this->housingTotal;
    }

    public function setHousingTotal(float $housingTotal): self
    {
        $this->housingTotal = $housingTotal;

        return $this;
    }

    public function getAdultsStayTax(): ?float
    {
        return $this->adultsStayTax;
    }

    public function setAdultsStayTax(float $adultsStayTax): self
    {
        $this->adultsStayTax = $adultsStayTax;

        return $this;
    }

    public function getChildrenStayTax(): ?float
    {
        return $this->childrenStayTax;
    }

    public function setChildrenStayTax(float $childrenStayTax): self
    {
        $this->childrenStayTax = $childrenStayTax;

        return $this;
    }

    public function getAdultsPoolTax(): ?float
    {
        return $this->adultsPoolTax;
    }

    public function setAdultsPoolTax(float $adultsPoolTax): self
    {
        $this->adultsPoolTax = $adultsPoolTax;

        return $this;
    }

    public function getChildrenPoolTax(): ?float
    {
        return $this->childrenPoolTax;
    }

    public function setChildrenPoolTax(float $childrenPoolTax): self
    {
        $this->childrenPoolTax = $childrenPoolTax;

        return $this;
    }

    public function getTotalPretax(): ?float
    {
        return $this->totalPretax;
    }

    public function setTotalPretax(float $totalPretax): self
    {
        $this->totalPretax = $totalPretax;

        return $this;
    }

    public function getTotalAftertax(): ?float
    {
        return $this->totalAftertax;
    }

    public function setTotalAftertax(float $totalAftertax): self
    {
        $this->totalAftertax = $totalAftertax;

        return $this;
    }
}
