<?php

namespace App\Controller\Admin;

use App\Entity\Enum\AgreedEnum;
use App\Entity\Enum\ChecklistEnum;
use App\Entity\UserTripChecklist;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class UserTripChecklistCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UserTripChecklist::class;
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('Trip Checklist')
            ->setEntityLabelInPlural('Trip Checklists')
        ;
    }



    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id')->hideOnForm(); 
        $owner   = AssociationField::new("owner","Owner");
        $rental = AssociationField::new("rental","Rental");
        $type = ChoiceField::new('type',"Type")->setChoices(ChecklistEnum::choices());
        $damageDescription = TextareaField::new('damageDescription',"Damage Description");
        $milleage = IntegerField::new('milleage',"Milleage");
        $ownerAgreed =  ChoiceField::new('ownerAgreed',"Owner Agreed")->setChoices(AgreedEnum::choices());
        $renterAgreed =  ChoiceField::new('renterAgreed',"Renter Agreed")->setChoices(AgreedEnum::choices());
        $fuel_available = IntegerField::new('fuel_available',"Fuel available");
        $dateSignedByOwner = DateTimeField::new('dateSignedByOwner',"Date Signed By Owner");
        $dateSignedByRenter = DateTimeField::new('dateSignedByRenter',"Date Signed By Renter");
        $userCarIssues = AssociationField::new("userCarIssues","Car Issues");
        $userCarAvailableItems = AssociationField::new("userCarAvailableItems","Car Available Items");
        $userCarMissingItems = AssociationField::new("userCarMissingItems","Car Missing Items");
        $status = ChoiceField::new('status',"Status")->setChoices(AgreedEnum::choices());
        $requestRenterToSign =  BooleanField::new('requestRenterToSign',"Request Renter To Sign");
    

        switch ($pageName) {
            case Action::INDEX:
                return [
                    $owner,
                    //$rental,
                    $type,
                    $ownerAgreed,
                    $renterAgreed,
                    $userCarIssues,
                    $userCarAvailableItems,
                    $userCarMissingItems,
                    $status,
                    $requestRenterToSign
            ];
                break;
            case Action::EDIT:
                return [ 
                    $id,
                    $owner,
                    $rental,
                    $type,
                    $damageDescription,
                    $milleage,
                    $ownerAgreed,
                    $renterAgreed,
                    $fuel_available,
                    $dateSignedByOwner,
                    $dateSignedByRenter,
                    $userCarIssues,
                    $userCarAvailableItems,
                    $userCarMissingItems,
                    $status,
                    $requestRenterToSign
            ];
                break;
            case Action::NEW:
                return [
                    $id,
                    $owner,
                    $rental,
                    $type,
                    $damageDescription,
                    $milleage,
                    $ownerAgreed,
                    $renterAgreed,
                    $fuel_available,
                    $dateSignedByOwner,
                    $dateSignedByRenter,
                    $userCarIssues,
                    $userCarAvailableItems,
                    $userCarMissingItems,
                    $status,
                    $requestRenterToSign
            ];
                break;
            default:
            return [  
                $id,
                $owner,
                $rental,
                $type,
                $damageDescription,
                $milleage,
                $ownerAgreed,
                $renterAgreed,
                $fuel_available,
                $dateSignedByOwner,
                $dateSignedByRenter,
                $userCarIssues,
                $userCarAvailableItems,
                $userCarMissingItems,
                $status,
                $requestRenterToSign
                ];
                break;
            }
    }
}
