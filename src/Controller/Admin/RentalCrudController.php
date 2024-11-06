<?php

namespace App\Controller\Admin;

use App\Entity\Enum\PaymentEnum;
use App\Entity\Enum\PaymentStatusEnum;
use App\Entity\Enum\RentalEnum;
use App\Entity\Rental;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RentalCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Rental::class;
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('Rental')
            ->setEntityLabelInPlural('Rentals')
        ;
    }



    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id')->hideOnForm();
        $user = AssociationField::new("user","Rentee");
        $car = AssociationField::new("car","Vehicle");
        $location = TextField::new('location',"Location");
        $pickupdate = DateTimeField::new('pickupdate',"Pick up date");
        $dropoffdate = DateTimeField::new('dropoffdate',"Drop off date");
        $quoteAmount = IntegerField::new('quoteAmount',"Amount");
        $paidAmount = IntegerField::new('paidAmount',"Paid Amount");
        $paymentStatus = ChoiceField::new('paymentStatus',"Payment Status")->setChoices(PaymentStatusEnum::choices());
        $status = ChoiceField::new('status',"Status")->setChoices(RentalEnum::choices());
        $paymentType = ChoiceField::new('paymentType',"Payment Type")->setChoices(PaymentEnum::choices());
        $userTripChecklists = AssociationField::new("userTripChecklists","Trip Checklists");
    

        switch ($pageName) {
            case Action::INDEX:
                return [
                    $user,
                    $car,
                    $pickupdate,
                    $dropoffdate,
                    $quoteAmount,
                    $paymentStatus,
                    $status,
                    $paymentType,
                    $userTripChecklists,
            ];
                break;
            case Action::EDIT:
                return [
                    $user,
                    $car,
                    $location,
                    $pickupdate,
                    $dropoffdate,
                    $quoteAmount,
                    $paidAmount,
                    $paymentStatus,
                    $status,
                    $paymentType,
                    $userTripChecklists,
            ];
                break;
            case Action::NEW:
                return [
                    $user,
                    $car,
                    $location,
                    $pickupdate,
                    $dropoffdate,
                    $quoteAmount,
                    $paidAmount,
                    $paymentStatus,
                    $status,
                    $paymentType,
                    $userTripChecklists,
            ];
                break;
            default:
            return [ 
                $user,
                $car,
                $location,
                $pickupdate,
                $dropoffdate,
                $quoteAmount,
                $paidAmount,
                $paymentStatus,
                $status,
                $paymentType,
                $userTripChecklists,
        ];
                break;
            }
    }
}
