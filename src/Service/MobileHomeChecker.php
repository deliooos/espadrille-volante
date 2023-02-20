<?php

namespace App\Service;

use App\Entity\Booking;
use App\Entity\Housing;
use App\Entity\Invoice;
use App\Repository\TaxRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Konekt\PdfInvoice\InvoicePrinter;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class MobileHomeChecker
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


        /*
         * Check if the booking doesn't start before the camping opens
         */
        if ($booking->getStartDate() <= DateTime::createFromFormat('d-m-Y', '04-05-2023')) {
            $this->session->getFlashBag()->add('error', 'Vous ne pouvez pas réserver avant le 05/05/2023');
            return false;
        }

        /*
         * Check if the booking doesn't end after the camping closes
         */
        if ($booking->getEndDate() > DateTime::createFromFormat('d-m-Y', '10-10-2023')) {
            $this->session->getFlashBag()->add('error', 'Vous ne pouvez pas réserver après le 10/10/2023');
            return false;
        }

        /*
         * Check if the booking doesn't start before tomorrow
         */
        if ($booking->getStartDate() < new \DateTime('now')) {
            $this->session->getFlashBag()->add('error', 'Vous ne pouvez pas réserver avant demain');
            return false;
        }

        /*
         * Check that the total of people doesn't exceed the size of the housing
         */
        if (($booking->getNbrAdults() + $booking->getNbrChildren()) > $housing->getSize()) {
            $this->session->getFlashBag()->add('error', sprintf('Il ne peut y avoir que %d personnes dans ce logement', $housing->getSize()));
            return false;
        }

        /*
         * Check if the pool days don't exceed the number of days of the booking
         */
        if ($booking->getPoolDays() > ($booking->getStartDate()->diff($booking->getEndDate())->days)) {
            $this->session->getFlashBag()->add('error', 'Le nombre de jours de piscine ne peut pas dépasser la durée de la réservation');
            return false;
        }

        return true;
    }

    /**
     * @param Booking $booking
     * @param TaxRepository $taxRepository
     * @throws NonUniqueResultException
     * Creates the invoice for the booking and saves it in the user's invoices if one is currently logged in
     */
    public function makeInvoice(Booking $booking, TaxRepository $taxRepository, Security $security, EntityManagerInterface $em): void
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

        $invoice = new InvoicePrinter('A4', '€', 'fr');
        $invoice->setType('Réservation Espadrille Volante');
        $invoice->setReference(sprintf("Réservation %s#%d", $booking->getHousing()->getName(), $booking->getId()));
        $invoice->setDate(date('d-m-Y', strtotime($booking->getCreated()->format('d-m-Y'))));
        $invoice->setTime(date('H:i', strtotime($booking->getCreated()->format('H:i'))));
        $invoice->setFrom(["Camping Espadrille Volante", "Rue de la plage", "1234 Plage", "France"]);
        $invoice->setTo([$booking->getFirstName() . ' ' . $booking->getLastName()]);

        $invoice->addItem($booking->getHousing()->getName(), $booking->getHousing()->getDescription(), 1, 0, $booking->getHousing()->getPrice(), $discount, $housingTotal);
        $invoice->addItem('Taxe de séjour adultes', 'Taxe de séjour appliquée au total d\'adultes profitants du séjour', $booking->getNbrAdults(), 0, $taxRepository->getAdultStayTax()->getApplicantTax(), 0, $adultsPrice);
        $invoice->addItem('Taxe de séjour enfants', 'Taxe de séjour appliquée au total d\'enfants profitants du séjour', $booking->getNbrChildren(), 0, $taxRepository->getChildStayTax()->getApplicantTax(), 0, $childrenPrice);
        $invoice->addItem('Taxe de piscine adultes', 'Taxe de piscine appliquée au total d\'adultes profitants de la piscine', $booking->getNbrAdults(), 0, $taxRepository->getAdultPoolTax()->getApplicantTax(), 0, $adultsPoolPrice);
        $invoice->addItem('Taxe de piscine enfants', 'Taxe de piscine appliquée au total d\'enfants profitants de la piscine', $booking->getNbrChildren(), 0, $taxRepository->getChildPoolTax()->getApplicantTax(), 0, $childrenPoolPrice);

        $invoice->addTotal('Total', $total);

        /*
         * If the booking is during high season, add 15% tax
         */
        if ($highSeason) {
            $invoice->addTotal('Taxe haute saison', $booking->getHousing()->getPrice() * 0.15);
        }

        $invoice->addTotal('Total à payer', $total + ($highSeason ? $booking->getHousing()->getPrice() * 0.15 : 0), true);

        $invoice->setFooternote('Merci de votre réservation ! - Camping Espadrille Volante');

        $invoiceSaver = new Invoice();

        $invoiceSaver->setBooking($booking);
        if ($security->getUser()) {
            $invoiceSaver->setClient($security->getUser());
        }

        $em->persist($invoiceSaver);
        $em->flush();

        $invoice->render(sprintf('facture-%s-%s-%s-%d', $booking->getFirstName(), $booking->getLastName(), $booking->getHousing()->getName(), $booking->getHousing()->getId()), 'F');
    }
}