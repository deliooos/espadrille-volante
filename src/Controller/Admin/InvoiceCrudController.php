<?php

namespace App\Controller\Admin;

use App\Entity\Invoice;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class InvoiceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Invoice::class;
    }

    public function configureFields(string $pageName): iterable
    {
        // Setting all the necessary fields for the CRUD
        yield TextField::new('reference');
        yield TextField::new('adressedTo');
        yield TextField::new('adressedMail');
        yield TextField::new('adressedPhone');
        yield TextField::new('housingIdentifier');
        yield NumberField::new('housingTotal');
        yield NumberField::new('adultsStayTax');
        yield NumberField::new('childrenStayTax');
        yield NumberField::new('adultsPoolTax');
        yield NumberField::new('childrenPoolTax');
        yield NumberField::new('totalPretax');
        yield NumberField::new('totalAftertax');
    }
}
