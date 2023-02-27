<?php

namespace App\Data;

// Creating the MobileHomeFilter class that will be used by the MobileHomeRepository to filter the mobile homes
class MobileHomeFilter
{
    public ?\DateTime $startDate = null;

    public ?\DateTime $endDate = null;

    public ?int $size = null;

    public bool $fromCompanyOnly = false;
}