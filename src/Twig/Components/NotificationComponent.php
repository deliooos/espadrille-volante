<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('notification')]
final class NotificationComponent
{
    public string $type = "info";
    public string $message;

    /*public function getIconSvg(): string
    {
        return match ($this->type) {
            "info" => "<i class='bx bx-info-circle'></i>",
            "success" => "<i class='bx bxs-check-circle'></i>",
            "danger" => "<i class='bx bxs-error'></i>",
            "error" => "<i class='bx bx-error-circle'></i>",
        };
    }*/

    /*public function getIconColor(): string
    {
        return match ($this->type) {
            "info" => "text-sky-500",
            "success" => "text-green-500",
            "danger" => "text-yellow-500",
            "error" => "text-red-500",
        };
    }*/
}