<?php

namespace App\Controller\Admin;

use App\Entity\Car;
use App\Entity\Enum\FuelEnum;
use App\Entity\Enum\TransmissionEnum;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CarCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Car::class;
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('vehicle')
            ->setEntityLabelInPlural('vehicles')
        ;
    }



    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id')->hideOnForm();
        $owner = AssociationField::new("owner","Owner");
        $name = TextField::new('name','Name');
        $description = TextareaField::new('description','Description');
        $ratePerDay = IntegerField::new('ratePerDay','Rate Per Day');
        $seatNumber= IntegerField::new('seat_number','Seat number');
        $refundableDeposit = IntegerField::new('refundableDeposit','Refundable Deposit');
        $bagNumber = IntegerField::new('bag_number','Bag number');
        $doorNumber = IntegerField::new('door_number','Door number');
        $transmission = ChoiceField::new('transmission','Transmission')->setChoices(TransmissionEnum::choices());;;
        $fuel = ChoiceField::new('fuel','Fuel')->setChoices(FuelEnum::choices());;;
        $aircon = BooleanField::new('Aircon','Aircon');
        $leatherUpholstery = BooleanField::new('leatherUpholstery','Leather Upholstery');
        $gps = BooleanField::new('gps','Gps');
        $active = BooleanField::new('active','Active')  ;
    

        switch ($pageName) {
            case Action::INDEX:
                return [
                    $owner,
                    $name,
                    $ratePerDay,
                    $refundableDeposit,
                    $transmission,
                    $active,
            ];
                break;
            case Action::EDIT:
                return [
                    $owner,
                    $name,
                    $description,
                    $ratePerDay,
                    $seatNumber,
                    $refundableDeposit,
                    $bagNumber,
                    $doorNumber,
                    $transmission,
                    $fuel,
                    $aircon,
                    $leatherUpholstery ,
                    $gps,
                    $active,
            ];
                break;
            case Action::NEW:
                return [ 
                    $owner,
                    $name,
                    $description,
                    $ratePerDay,
                    $seatNumber,
                    $refundableDeposit,
                    $bagNumber,
                    $doorNumber,
                    $transmission,
                    $fuel,
                    $aircon,
                    $leatherUpholstery ,
                    $gps,
                    $active,
                ];
                break;
            default:
            return [ 
                $owner,
                $name,
                $description,
                $ratePerDay,
                $seatNumber,
                $refundableDeposit,
                $bagNumber,
                $doorNumber,
                $transmission,
                $fuel,
                $aircon,
                $leatherUpholstery ,
                $gps,
                $active,
                ];
                break;
            }
    }
}
