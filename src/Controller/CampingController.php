<?php

namespace App\Controller;

use App\Data\MobileHomeFilter;
use App\Form\MobileHomeFilterType;
use App\Repository\HousingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
}
