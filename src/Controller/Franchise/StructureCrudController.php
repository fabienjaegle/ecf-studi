<?php

namespace App\Controller\Franchise;

use App\Entity\Franchise;
use App\Entity\Structure;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class StructureCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Structure::class;
    }

    public function createEntity(string $entityFqcn)
    {
        $structure = new Structure();

        // In case of a Franchise user instance, set data for the new Structure
        if ($this->getUser() instanceof Franchise) {
            $structure->setFranchise($this->getUser());
        }

        $structure->setRoles(array('ROLE_STRUCTURE'));
        return $structure;
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id')->hideOnForm();
        $name = TextField::new('name');
        $address = TextField::new('address');
        $zipcode = TextField::new('zipcode');
        $city = TextField::new('city');
        $email = EmailField::new('email')->setLabel('Adresse mail');
        $password = TextField::new('password')->setFormType(PasswordType::class)->setLabel('Mot de passe');
        $active = BooleanField::new('active');

        // In case of a Franchise, do not giving the possibility for user
        // to set the Franchise for another one!
        if (!($this->getUser() instanceof Franchise)) {
            $franchises = AssociationField::new('franchise')->setCrudController(FranchiseCrudController::class);
        } else {
            $franchises = AssociationField::new('franchise')->setCrudController(FranchiseCrudController::class)->hideOnForm();
        }

        if ($pageName === Crud::PAGE_INDEX) {
            return [$id, $name, $address, $zipcode, $city, $email, $active];
        } else if ($pageName === Crud::PAGE_NEW) {
            return [$name, $address, $zipcode, $city, $email, $password, $active, $franchises];
        } else if ($pageName === Crud::PAGE_EDIT) {
            return [$name, $address, $zipcode, $city, $password, $active, $franchises];
        }
    }
}
