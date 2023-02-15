<?php

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Gedmo\Mapping\Annotation\Timestampable;
use Doctrine\ORM\Mapping as ORM;

trait TimestampableTrait
{
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Timestampable(on: 'create')]
    private ?\DateTimeInterface $created = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Timestampable]
    private ?\DateTimeInterface $updated = null;

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }
}