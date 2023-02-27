<?php

namespace App\Service;

use App\Entity\Booking;
use App\Entity\Housing;
use App\Entity\Invoice;
use App\Repository\TaxRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CaravanChecker
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

        /*
         * Check if the booking is start and end date are within the dates of another booking
         */
        foreach ($bookings as $b) {
            if ($booking->getStartDate() >= $b->getStartDate() && $booking->getStartDate() <= $b->getEndDate()) {
                $this->session->getFlashBag()->add('error', 'Ce logement est déjà réservé pour cette période');
                return false;
            }
            if ($booking->getEndDate() >= $b->getStartDate() && $booking->getEndDate() <= $b->getEndDate()) {
                $this->session->getFlashBag()->add('error', 'Ce logement est déjà réservé pour cette période');
                return false;
            }
            if ($booking->getStartDate() <= $b->getStartDate() && $booking->getEndDate() >= $b->getEndDate()) {
                $this->session->getFlashBag()->add('error', 'Ce logement est déjà réservé pour cette période');
                return false;
            }
        }

        if ($booking->getStartDate() <= DateTime::createFromFormat('d-m-Y', '04-05-2023')) {
            $this->session->getFlashBag()->add('error', 'Vous ne pouvez pas réserver avant le 05/05/2023');
            return false;
        }

        if ($booking->getEndDate() > DateTime::createFromFormat('d-m-Y', '10-10-2023')) {
            $this->session->getFlashBag()->add('error', 'Vous ne pouvez pas réserver après le 10/10/2023');
            return false;
        }

        if ($booking->getStartDate() < new \DateTime('now')) {
            $this->session->getFlashBag()->add('error', 'Vous ne pouvez pas réserver avant demain');
            return false;
        }

        if (($booking->getNbrAdults() + $booking->getNbrChildren()) > $housing->getSize()) {
            $this->session->getFlashBag()->add('error', 'Vous ne pouvez pas réserver plus de ' . $housing->getSize() . ' personnes');
            return false;
        }

        if ($booking->getPoolDays() > ($booking->getStartDate()->diff($booking->getEndDate())->days)) {
            $this->session->getFlashBag()->add('error', 'Vous ne pouvez pas réserver plus de jours de piscine que de jours de séjour');
            return false;
        }

        return true;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function makeInvoice(Booking $booking, TaxRepository $taxRepository, Security $security, EntityManagerInterface $em, RequestStack $requestStack): array
    {
        $discount = 0;
        $highSeason = false;

        /*
        * Check whether the housing is being booked during high season (from 21/07 to 31/08)
        */
        if ($booking->getStartDate() >= DateTime::createFromFormat('d-m-Y', '21-07-2023') && $booking->getEndDate() <= DateTime::createFromFormat('d-m-Y', '31-08-2023')) {
            $highSeason = true;
        }

        /*
         * Count how many weeks are in the booking and apply -5% discount for each week
         */
        $weeks = floor($booking->getStartDate()->diff($booking->getEndDate())->days / 7);
        for ($i = 0; $i < $weeks; $i++) {
            $discount += $booking->getHousing()->getPrice() * 0.05;
        }

        $bookingDuration = $booking->getStartDate()->diff($booking->getEndDate())->days;

        $housingTotal = $booking->getHousing()->getPrice() - $discount;
        $adultsPrice = ($booking->getNbrAdults() * $taxRepository->getAdultStayTax()->getApplicantTax()) * $bookingDuration;
        $childrenPrice = ($booking->getNbrChildren() * $taxRepository->getChildStayTax()->getApplicantTax()) * $bookingDuration;
        $adultsPoolPrice = ($booking->getNbrAdults() * $taxRepository->getAdultPoolTax()->getApplicantTax()) * $bookingDuration;
        $childrenPoolPrice = ($booking->getNbrChildren() * $taxRepository->getChildPoolTax()->getApplicantTax()) * $bookingDuration;
        $total = $housingTotal + $adultsPrice + $childrenPrice + $adultsPoolPrice + $childrenPoolPrice;

        $invoice = [];
        $invoice['type'] = 'Reservation Espadrille Volante';
        $invoice['reference'] = 'Reservation ' . $booking->getHousing()->getName() . '#' . $booking->getId();
        $invoice['date'] = date('d-m-Y', strtotime($booking->getCreated()->format('d-m-Y')));
        $invoice['time'] = date('H:i', strtotime($booking->getCreated()->format('H:i')));
        $invoice['from'] = 'Camping Espadrille Volante, Rue de la plage, 1234 Plage, Le Soler, France';
        $invoice['to'] = $booking->getFirstName() . ' ' . $booking->getLastName();

        $invoice['logement']['name'] = $booking->getHousing()->getName();
        $invoice['logement']['description'] = $booking->getHousing()->getDescription();
        $invoice['logement']['price'] = $booking->getHousing()->getPrice();
        $invoice['logement']['discount'] = $discount;
        $invoice['logement']['total'] = $housingTotal;

        $invoice['taxes']['adults']['name'] = 'Taxe de sejour adultes';
        $invoice['taxes']['adults']['description'] = 'Taxe de sejour appliquée au total d\'adultes profitants du sejour';
        $invoice['taxes']['adults']['quantity'] = $booking->getNbrAdults();
        $invoice['taxes']['adults']['price'] = $taxRepository->getAdultStayTax()->getApplicantTax();
        $invoice['taxes']['adults']['total'] = $adultsPrice;

        $invoice['taxes']['children']['name'] = 'Taxe de sejour enfants';
        $invoice['taxes']['children']['description'] = 'Taxe de sejour appliquee au total d\'enfants profitants du sejour';
        $invoice['taxes']['children']['quantity'] = $booking->getNbrChildren();
        $invoice['taxes']['children']['price'] = $taxRepository->getChildStayTax()->getApplicantTax();
        $invoice['taxes']['children']['total'] = $childrenPrice;

        $invoice['taxes']['adultsPool']['name'] = 'Taxe de piscine adultes';
        $invoice['taxes']['adultsPool']['description'] = 'Taxe de piscine appliquee au total d\'adultes profitants de la piscine';
        $invoice['taxes']['adultsPool']['quantity'] = $booking->getNbrAdults();
        $invoice['taxes']['adultsPool']['price'] = $taxRepository->getAdultPoolTax()->getApplicantTax();
        $invoice['taxes']['adultsPool']['total'] = $adultsPoolPrice;

        $invoice['taxes']['childrenPool']['name'] = 'Taxe de piscine enfants';
        $invoice['taxes']['childrenPool']['description'] = 'Taxe de piscine appliquee au total d\'enfants profitants de la piscine';
        $invoice['taxes']['childrenPool']['quantity'] = $booking->getNbrChildren();
        $invoice['taxes']['childrenPool']['price'] = $taxRepository->getChildPoolTax()->getApplicantTax();
        $invoice['taxes']['childrenPool']['total'] = $childrenPoolPrice;

        $invoice['total']['pretax'] = $total;

        $invoice['total']['highseasontax'] = $highSeason ? $booking->getHousing()->getPrice() * 0.15 : 0;

        $invoice['total']['total'] = $total + ($highSeason ? $booking->getHousing()->getPrice() * 0.15 : 0);

        $invoiceSaver = new Invoice();

        $invoiceSaver->setReference($invoice['reference']);
        $invoiceSaver->setAdressedTo($invoice['to']);
        $invoiceSaver->setAdressedMail($booking->getEmail());
        $invoiceSaver->setAdressedPhone($booking->getPhone());
        $invoiceSaver->setHousingIdentifier($invoice['logement']['name'] . '#' . $booking->getHousing()->getId());
        $invoiceSaver->setHousingTotal($invoice['logement']['price']);
        $invoiceSaver->setAdultsStayTax($invoice['taxes']['adults']['total']);
        $invoiceSaver->setChildrenStayTax($invoice['taxes']['children']['total']);
        $invoiceSaver->setAdultsPoolTax($invoice['taxes']['adultsPool']['total']);
        $invoiceSaver->setChildrenPoolTax($invoice['taxes']['childrenPool']['total']);
        $invoiceSaver->setTotalPretax($invoice['total']['pretax']);
        $invoiceSaver->setTotalAftertax($invoice['total']['total']);

        $em->persist($invoiceSaver);
        $em->flush();

        return $invoice;
    }
}