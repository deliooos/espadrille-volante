<?php

namespace App\Controller;

use App\Data\CaravanFilter;
use App\Data\MobileHomeFilter;
use App\Data\SpaceFilter;
use App\Entity\Booking;
use App\Entity\Housing;
use App\Entity\Invoice;
use App\Form\CaravanBookType;
use App\Form\CaravanFilterType;
use App\Form\MobileHomeBookType;
use App\Form\MobileHomeFilterType;
use App\Form\SpaceBookType;
use App\Form\SpaceFilterType;
use App\Repository\BookingRepository;
use App\Repository\HousingRepository;
use App\Repository\TaxRepository;
use App\Service\CaravanChecker;
use App\Service\MobileHomeChecker;
use App\Service\SpaceChecker;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/camping')]
class CampingController extends AbstractController
{
    #[Route('/', name: 'app_camping')]
    public function index(): Response
    {
        // Rendering the home page
        return $this->render('camping/index.html.twig');
    }

    #[Route('/mobile-home', name: 'app_camping_mobile_home')]
    public function mobileHome(Request $request, HousingRepository $housingRepository): Response
    {
        // Rendering the mobile homes list page with a form that allows to filter the results
        $data = new MobileHomeFilter();
        $form = $this->createForm(MobileHomeFilterType::class, $data);
        $form->handleRequest($request);

        // Getting the mobile homes list with the filter
        $mobileHomes = $housingRepository->findMobileHomeSearch($data);

        // Rendering the mobile homes list page with the renderForm method to make it "live"
        return $this->renderForm('camping/mobile-home/index.html.twig', [
            'mobile_homes' => $mobileHomes,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/mobile-home/{id}', name: 'app_camping_mobile_home_book')]
    public function mobileHomeBook(Housing $housing, Request $request, BookingRepository $bookingRepository, SessionInterface $session, TaxRepository $taxRepository, Security $security, EntityManagerInterface $em, RequestStack $requestStack, Pdf $knpSnappyPdf): Response
    {
        // Creating the form to book a mobile home
        $booking = new Booking();

        $form = $this->createForm(MobileHomeBookType::class, $booking);
        $form->handleRequest($request);

        // If the form is submitted and valid we handle it
        if ($form->isSubmitted() && $form->isValid()) {

            $mobileHomeChecker = new MobileHomeChecker($session);

            // TODO handle if user is connected

            // Checking if the mobile home is available, and all the fields are good, if not we redirect the user to the mobile home page and reset the form
            if (!$mobileHomeChecker->checkAvailability($booking, $housing)) {
                return $this->redirectToRoute('app_camping_mobile_home_book', ['id' => $housing->getId()]);
            }

            // Saving the booking
            $booking->setHousing($housing);
            $bookingRepository->save($booking, true);

            // Creating the invoice
            $invoice = $mobileHomeChecker->makeInvoice($booking, $taxRepository, $security, $em, $requestStack);

            // Adding a flash message to inform the user that the booking is done
            $this->addFlash('success', 'Votre réservation à bien été prise en compte');

            // Generate PDF with the invoice information
            $html = $this->renderView('invoice/invoice.html.twig', [
                'invoice' => $invoice,
            ]);

            // Force the download of the PDF
            return new PdfResponse(
                $knpSnappyPdf->getOutputFromHtml($html),
                'facture.pdf',
            );

            // Uncomment this and comment the code above to redirect the user to the home page if PDF generation is not needed
//            return $this->redirectToRoute('app_home');
        }

        return $this->renderForm('camping/mobile-home/book.html.twig', [
            'housing' => $housing,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/caravane', name: 'app_camping_caravan')]
    public function caravan(Request $request, HousingRepository $housingRepository): Response
    {
        $data = new CaravanFilter();
        $form = $this->createForm(CaravanFilterType::class, $data);
        $form->handleRequest($request);

        $caravans = $housingRepository->findCaravanSearch($data);

        return $this->renderForm('camping/caravan/index.html.twig', [
            'caravans' => $caravans,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/caravane/{id}', name: 'app_camping_caravan_book')]
    public function caravanBook(Housing $housing, Request $request, BookingRepository $bookingRepository, SessionInterface $session, TaxRepository $taxRepository, Security $security, EntityManagerInterface $em, RequestStack $requestStack, Pdf $knpSnappyPdf): Response
    {
        $booking = new Booking();

        $form = $this->createForm(CaravanBookType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $caravanChecker = new CaravanChecker($session);

            // TODO handle if user is connected

            if (!$caravanChecker->checkAvailability($booking, $housing)) {
                return $this->redirectToRoute('app_camping_caravan_book', ['id' => $housing->getId()]);
            }

            $booking->setHousing($housing);
            $bookingRepository->save($booking, true);

            $invoice = $caravanChecker->makeInvoice($booking, $taxRepository, $security, $em, $requestStack);

            $this->addFlash('success', 'Votre réservation à bien été prise en compte');

            $html = $this->renderView('invoice/invoice.html.twig', [
                'invoice' => $invoice,
            ]);

            return new PdfResponse(
                $knpSnappyPdf->getOutputFromHtml($html),
                'facture.pdf',
            );

//            return $this->redirectToRoute('app_home');
        }

        return $this->renderForm('camping/caravan/book.html.twig', [
            'housing' => $housing,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/emplacement', name: 'app_camping_place')]
    public function place(Request $request, HousingRepository $housingRepository): Response
    {
        $data = new SpaceFilter();
        $form = $this->createForm(SpaceFilterType::class, $data);
        $form->handleRequest($request);

        $spaces = $housingRepository->findSpaceSearch($data);

        return $this->renderForm('camping/space/index.html.twig', [
            'spaces' => $spaces,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/emplacement/{id}', name: 'app_camping_space_book')]
    public function placeBook(Housing $housing, Request $request, BookingRepository $bookingRepository, SessionInterface $session, TaxRepository $taxRepository, Security $security, EntityManagerInterface $em, RequestStack $requestStack, Pdf $knpSnappyPdf): Response
    {
        $booking = new Booking();

        $form = $this->createForm(SpaceBookType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $spaceChecker = new SpaceChecker($session);

            // TODO handle if user is connected

            if (!$spaceChecker->checkAvailability($booking, $housing)) {
                return $this->redirectToRoute('app_camping_place_book', ['id' => $housing->getId()]);
            }

            $booking->setHousing($housing);
            $bookingRepository->save($booking, true);

            $invoice = $spaceChecker->makeInvoice($booking, $taxRepository, $security, $em, $requestStack);

            $this->addFlash('success', 'Votre réservation à bien été prise en compte');

            $html = $this->renderView('invoice/invoice.html.twig', [
                'invoice' => $invoice,
            ]);

            return new PdfResponse(
                $knpSnappyPdf->getOutputFromHtml($html),
                'facture.pdf',
            );

//            return $this->redirectToRoute('app_home');
        }

        return $this->renderForm('camping/space/book.html.twig', [
            'housing' => $housing,
            'form' => $form->createView(),
        ]);
    }
}

