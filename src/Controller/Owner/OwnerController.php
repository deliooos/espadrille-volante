<?php

namespace App\Controller\Owner;

use App\Entity\Housing;
use App\Repository\HousingRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OwnerController extends AbstractDashboardController
{
    #[Route('/proprietaire', name: 'app_owner')]
    public function index(): Response
    {
//        return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        // Rendering a home template in order to show the navbar on the left
        return $this->render('owner/index.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            // Configuring the name in the navbar and the title in the header
            ->setTitle('Espadrille Volante');
    }
    public function configureMenuItems(): iterable
    {
        // Configure the navbar menu with the different sections and links
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section('Logements');
        yield MenuItem::linkToCrud('Mobile Homes', 'fas fa-home', Housing::class);
    }
}
