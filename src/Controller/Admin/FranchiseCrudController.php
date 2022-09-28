<?php

namespace App\Controller\Admin;

use App\Entity\Admin;
use App\Entity\Franchise;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class FranchiseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Franchise::class;
    }

    public function createEntity(string $entityFqcn)
    {
        $franchise = new Franchise();
        $franchise->setRoles(array('ROLE_FRANCHISE'));

        return $franchise;
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id')->hideOnForm();
        $email = EmailField::new('email')->setLabel('Adresse mail');
        $password = TextField::new('password')->setFormType(PasswordType::class)->setLabel('Mot de passe');
        $isActive = BooleanField::new('is_active')->setLabel('Est actif ?');
        $name = TextField::new('name')->setLabel('Nom');

        if ($pageName === Crud::PAGE_INDEX) {
            return [$id, $name, $email, $isActive];
        } else if ($pageName === Crud::PAGE_NEW) {
            return [$name, $email, $password, $isActive];
        } else if ($pageName === Crud::PAGE_EDIT) {
            return [$name, $password, $isActive];
        }
    }
}
