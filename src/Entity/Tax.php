<?php

namespace App\Entity;

use App\Repository\TaxRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaxRepository::class)]
class Tax
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column(length: 255)]
    private ?string $applicant = null;

    #[ORM\Column]
    private ?float $applicantTax = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getApplicant(): ?string
    {
        return $this->applicant;
    }

    public function setApplicant(string $applicant): self
    {
        $this->applicant = $applicant;

        return $this;
    }

    public function getApplicantTax(): ?float
    {
        return $this->applicantTax;
    }

    public function setApplicantTax(float $applicantTax): self
    {
        $this->applicantTax = $applicantTax;

        return $this;
    }
}
