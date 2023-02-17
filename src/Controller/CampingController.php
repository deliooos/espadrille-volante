<?php

namespace App\Controller;

use App\Data\CaravanFilter;
use App\Data\MobileHomeFilter;
use App\Data\SpaceFilter;
use App\Entity\Booking;
use App\Entity\Housing;
use App\Form\CaravanFilterType;
use App\Form\MobileHomeBookType;
use App\Form\MobileHomeFilterType;
use App\Form\SpaceFilterType;
use App\Repository\BookingRepository;
use App\Repository\HousingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/camping')]
class CampingController extends AbstractController
{
    #[Route('/', name: 'app_camping')]
    public function index(): Response
    {
        return $this->render('camping/index.html.twig');
    }

    #[Route('/mobile-home', name: 'app_camping_mobile_home')]
    public function mobileHome(Request $request, HousingRepository $housingRepository): Response
    {
        $data = new MobileHomeFilter();
        $form = $this->createForm(MobileHomeFilterType::class, $data);
        $form->handleRequest($request);

        $mobileHomes = $housingRepository->findMobileHomeSearch($data);

        return $this->renderForm('camping/mobile-home/index.html.twig', [
            'mobile_homes' => $mobileHomes,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/mobile-home/{id}', name: 'app_camping_mobile_home_book')]
    public function mobileHomeBook(Housing $housing, Request $request, BookingRepository $bookingRepository): Response
    {
        $booking = new Booking();

        $form = $this->createForm(MobileHomeBookType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // On vérifie que le logement n'est pas déjà loué à la date choisie
            $bookings = $housing->getBookings();
            foreach ($bookings as $b) {
                if ($b->getStartDate() <= $booking->getStartDate() && $b->getEndDate() >= $booking->getEndDate()) {
                    $this->addFlash('error', 'Le mobile home n\'est pas disponible aux dates que vous avez choisi');
                    return $this->redirectToRoute('app_camping_mobile_home_book', ['id' => $housing->getId()]);
                }
            }

            // On vérifie que la date de réservation soit après aujourd'hui
            if ($booking->getStartDate() < new \DateTime('now')) {
                $this->addFlash('error', 'Vous ne pouvez pas réserver avant demain');
                return $this->redirectToRoute('app_camping_mobile_home_book', ['id' => $housing->getId()]);
            }

            if (($booking->getNbrAdults() + $booking->getNbrChildren()) > $housing->getSize()) {
                $this->addFlash('error', sprintf('Il ne peut y avoir que %d personnes dans ce logement', $housing->getSize()));
                return $this->redirectToRoute('app_camping_mobile_home_book', ['id' => $housing->getId()]);
            }

            $booking->setHousing($housing);
            $bookingRepository->save($booking, true);



            $this->addFlash('success', 'Votre réservation à bien été prise en compte');
            return $this->redirectToRoute('app_home');
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
}
