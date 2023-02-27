<?php

namespace App\Controller\Admin;

use App\Entity\Booking;
use App\Entity\Housing;
use App\Entity\Invoice;
use App\Repository\BookingRepository;
use App\Repository\InvoiceRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractDashboardController
{
    #[Route('/admin', name: 'app_admin')]
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
         return $this->render('admin/index.html.twig');
    }

    #[Route('/admin/alertes', name: 'app_admin_alerts')]
    public function alerts(BookingRepository $bookingRepository, InvoiceRepository $invoiceRepository): Response
    {
        // Getting booking and invoice alerts
        // Booking that need to be deleted in a week or less
        // Invoices that need to be deleted in a week or less
        $bookingAlerts = $bookingRepository->findAlerts();
        $invoiceAlerts = $invoiceRepository->findAlerts();

        return $this->render('admin/alerts.html.twig', [
            'booking_alerts' => $bookingAlerts,
            'invoice_alerts' => $invoiceAlerts,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            // Configuring the name in the navbar and the title in the header
            ->setTitle('Espadrille Volante');
    }

    public function configureMenuItems(): iterable
    {
        // Configuring the navbar menu with the different sections and links
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section('Logements');
        yield MenuItem::linkToCrud('Logements', 'fas fa-home', Housing::class);
        yield MenuItem::linkToCrud('Réservations', 'fas fa-calendar-check', Booking::class);
        yield MenuItem::section('Factures');
        yield MenuItem::linkToCrud('Factures', 'fas fa-file-invoice', Invoice::class);
        yield MenuItem::section('Alertes');
        yield MenuItem::linkToRoute('Alertes', 'fas fa-exclamation-triangle', 'app_admin_alerts');
    }
}
