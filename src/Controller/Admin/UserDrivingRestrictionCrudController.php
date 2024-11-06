<?php

namespace App\Controller\Admin;

use App\Entity\UserDrivingRestriction;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class UserDrivingRestrictionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UserDrivingRestriction::class;
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('Driving Restriction')
            ->setEntityLabelInPlural('Driving Restrictions')
        ;
    }



    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id')->hideOnForm();
        $user = AssociationField::new("owner","Owner");
        $description = TextareaField::new('description','Description');
        $amount = IntegerField::new('fine','Fine');
        $state = BooleanField::new('state','State');
    

        switch ($pageName) {
            case Action::INDEX:
                return [
                    $user,
                    $description,
                    $amount,
                    $state,
            ];
                break;
            case Action::EDIT:
                return [
                    $id,
                    $user,
                    $description,
                    $amount,
                    $state,
            ];
                break;
            case Action::NEW:
                return [
                    $id,
                    $user,
                    $description,
                    $amount,
            ];
                break;
            default:
            return [
                $id,
                $user,
                $description,
                $amount,
                ];
                break;
            }
    }
}
