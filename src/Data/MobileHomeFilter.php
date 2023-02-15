<?php

namespace App\Data;

class MobileHomeFilter
{
    public ?\DateTime $startDate = null;

    public ?\DateTime $endDate = null;

    public ?int $size = null;

    public bool $fromCompanyOnly = false;
}