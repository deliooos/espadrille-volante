<?php

namespace App\Data;

// Creating the CaravanFilter class that will be used by the CaravanRepository to filter the caravans
class CaravanFilter
{
    public ?\DateTime $startDate = null;

    public ?\DateTime $endDate = null;

    public ?int $size = null;
}