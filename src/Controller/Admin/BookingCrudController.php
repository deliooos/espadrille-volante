<?php

namespace App\Controller\Admin;

use App\Entity\Booking;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class BookingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Booking::class;
    }

    public function configureFields(string $pageName): iterable
    {
        // Setting all the necessary fields for the CRUD and hiding some of them on the form
        yield DateField::new('startDate', 'Début du séjour')->setFormat('dd/MM/yyyy')->hideOnForm();
        yield DateField::new('endDate', 'Fin du séjour')->setFormat('dd/MM/yyyy')->hideOnForm();
        yield AssociationField::new('housing', 'Hébergement')->hideOnForm();
        yield TextField::new('firstName');
        yield TextField::new('lastName');
        yield TextField::new('email');
        yield TextField::new('phone');
        yield IntegerField::new('nbrAdults');
        yield IntegerField::new('nbrChildren');
        yield IntegerField::new('poolDays');
    }
}
