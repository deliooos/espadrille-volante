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
        if ($this->isGranted('ROLE_ADMIN')) {
            return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        }
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->andWhere('entity.owner = :owner')
            ->setParameter('owner', $this->getUser());
    }

    public function createEntity(string $entityFqcn)
    {
        $housing = new Housing();
        $housing->setOwner($this->getUser());
        $housing->setType('mobilehome');
        return $housing;
    }

    public function configureFields(string $pageName): iterable
    {
        yield ChoiceField::new('type')->setChoices([
            'Mobile Home' => 'mobilehome',
            'Chalet' => 'chalet',
            'Camping Car' => 'campingcar',
            'Tente' => 'tent',
            'Autre' => 'other',
        ])->setPermission('ROLE_ADMIN');
        yield TextField::new('name');
        yield TextareaField::new('description');
        yield IntegerField::new('size');
        yield IntegerField::new('surface');
        yield IntegerField::new('price');
        yield ImageField::new('thumbnail')->setBasePath('uploads/housing/')->setUploadDir('public/uploads/housing/')->setUploadedFileNamePattern('[randomhash].[extension]');
    }
}
