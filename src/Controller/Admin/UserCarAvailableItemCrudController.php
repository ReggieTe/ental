<?php

namespace App\Controller\Admin;

use App\Entity\UserCarAvailableItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class UserCarAvailableItemCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UserCarAvailableItem::class;
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('Car Available Item')
            ->setEntityLabelInPlural('Car Available Items')
        ;
    }



    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id')->hideOnForm();
        $user = AssociationField::new("checklist","Owner");
        $description = TextareaField::new('description','Description');
        $amount = IntegerField::new('amount','Amount');
        $measurement = IntegerField::new('measurement','measurement');
    

        switch ($pageName) {
            case Action::INDEX:
                return [
                    $user,
                    $description,
                    $amount,
                    $measurement,
            ];
                break;
            case Action::EDIT:
                return [
                    $id,
                    $user,
                    $description,
                    $amount,                   
                    $measurement
            ];
                break;
            case Action::NEW:
                return [
                    $id,
                    $user,
                    $description,
                    $amount,                    
                    $measurement
            ];
                break;
            default:
            return [
                $id,
                $user,
                $description,
                $amount,                
                $measurement,
                ];
                break;
            }
    }
}
