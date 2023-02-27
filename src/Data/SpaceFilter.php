<?php

namespace App\Data;

// Creating the SpaceFilter class that will be used by the SpaceRepository to filter the spaces
class SpaceFilter
{
    public ?\DateTime $startDate = null;

    public ?\DateTime $endDate = null;

    public ?int $size = null;
}