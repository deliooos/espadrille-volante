<?php

namespace App\Service;

use App\Entity\Booking;
use App\Entity\Housing;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/*
 * @deprecated Use specific checkers instead, like MobileHomeChecker, CaravanChecker, etc.
 */
class HousingBooker
{
    public function __construct(private readonly SessionInterface $session)
    {
    }

    /**
     * @param Booking $booking
     * @param Housing $housing
     * @return bool
     * Checks whether the housing is available for the booking, return true if it is, false otherwise
     */
    public function checkAvailability(Booking $booking, Housing $housing): bool
    {
        $bookings = $housing->getBookings();
        foreach ($bookings as $b) {
            if ($b->getStartDate() <= $booking->getStartDate() && $b->getEndDate() >= $booking->getEndDate()) {
                $this->session->getFlashBag()->add('error', 'Le logement est déjà réservé pour cette période');
                return false;
            }
        }
        return true;
    }

    /*
     * @param Booking $booking
     * @return bool
     * Check whether the start date is after today, return true if it is, false otherwise
     */
    public function checkStartDate(Booking $booking): bool
    {
        if ($booking->getStartDate() < new \DateTime('now')) {
            $this->session->getFlashBag()->add('error', 'Vous ne pouvez pas réserver avant demain');
            return false;
        }
        return true;
    }

    public function checkSize(Booking $booking, Housing $housing): bool
    {
        if (($booking->getNbrAdults() + $booking->getNbrChildren()) > $housing->getSize()) {
            $this->session->getFlashBag()->add('error', sprintf('Il ne peut y avoir que %d personnes dans ce logement', $housing->getSize()));
            return false;
        }
        return true;
    }
}