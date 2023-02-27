<?php

namespace App\Controller\Owner;

use App\Entity\Housing;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class HousingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Housing::class;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        // Making sure that only the owner can see his own housing, admins can see all
        if ($this->isGranted('ROLE_ADMIN')) {
            return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        }
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->andWhere('entity.owner = :owner')
            ->setParameter('owner', $this->getUser());
    }

    public function createEntity(string $entityFqcn)
    {
        // Overriding the createEntity method to set the owner of the housing to the current user if he is not an admin, otherwise, it means that the camping owns the housing
        $housing = new Housing();
        if ($this->isGranted('ROLE_ADMIN')) {
            $housing->setOwner(null);
        }
        $housing->setOwner($this->getUser());
        $housing->setType('mobilehome');
        return $housing;
    }

    public function configureFields(string $pageName): iterable
    {
        // Setting all the necessary fields for the CRUD and making sure that only admins can change the type of the housing
        yield ChoiceField::new('type')->setChoices([
            'Mobile Home' => 'mobilehome',
            'Caravan' => 'caravan',
            'Emplacement' => 'space',
        ])->setPermission('ROLE_ADMIN');
        yield TextField::new('name');
        yield TextareaField::new('description');
        yield IntegerField::new('size');
        yield IntegerField::new('surface');
        yield IntegerField::new('price');
        yield ImageField::new('thumbnail')->setBasePath('uploads/housing/')->setUploadDir('public/uploads/housing/')->setUploadedFileNamePattern('[randomhash].[extension]');
    }
}
