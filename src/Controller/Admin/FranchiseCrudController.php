<?php

namespace App\Controller\Admin;

use App\Entity\Franchise;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class FranchiseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Franchise::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
