<?php

namespace App\Controller\Admin;

use App\Entity\Enum\AccountTypeEnum;
use App\Entity\UserAdmin;
use App\Form\Admin\CarAdditionalType;
use App\Form\Admin\CarType;
use App\Form\Admin\ChecklistType;
use App\Form\Admin\DrivingRestrictionType;
use App\Form\Admin\RentalType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;

class UserAdminCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UserAdmin::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('User')
            ->setEntityLabelInPlural('Users')
        ;
    }
    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id')->hideOnForm();
        $username= TextField::new('username','Username');
        $fullname = TextField::new('fullname','Fullname');
        $address = TextField::new('address','Address');
        $phone = TextField::new('phone','Phone');
        $email = TextField::new('email','Email');
        $password = TextField::new('password','Password');
        $salt = TextField::new('salt','Salt');
        $type = ChoiceField::new('type','Type')->setChoices(AccountTypeEnum::choices());;
        $state = IntegerField::new('state','State');
        $lastPasswordResetRequestDate = DateField::new('lastPasswordResetRequestDate','Last Password Reset Request Date')->setFormTypeOptions(['data'=>new \DateTime('now')]);
        $lastlogin = DateField::new('lastlogin','Last login')->setFormTypeOptions(['data'=>new \DateTime('now')]);
        $lastlogout = DateField::new('lastlogout','Last logout')->setFormTypeOptions(['data'=>new \DateTime('now')]);
        $dateModified= DateField::new('dateModified','dateModified');
        $dateCreated= DateField::new('dateCreated','dateCreated');
        $cars =  CollectionField::new('cars','Cars')->setFormTypeOptions([
            "entry_type"=>CarType::class,
            'by_reference'=>false
        ])
        ->setEntryIsComplex(true)
        ->allowAdd()
        ->allowDelete();
        $rentals =  CollectionField::new('rentals','Rentals')->setFormTypeOptions([
            "entry_type"=>RentalType::class,
            'by_reference'=>false
        ])
        ->setEntryIsComplex(true)
        ->allowAdd()
        ->allowDelete();
        $userCarAdditionals =  CollectionField::new('userCarAdditionals','Car Additionals')->setFormTypeOptions([
            "entry_type"=>CarAdditionalType::class,
            'by_reference'=>false
        ])
        ->setEntryIsComplex(true)
        ->allowAdd()
        ->allowDelete();
        $userDrivingRestrictions =  CollectionField::new('userDrivingRestrictions','Driving Restrictions')->setFormTypeOptions([
            "entry_type"=>DrivingRestrictionType::class,
            'by_reference'=>false
        ])
        ->setEntryIsComplex(true)
        ->allowAdd()
        ->allowDelete();
        $userTripChecklists =  CollectionField::new('userTripChecklists','Trip Checklists')->setFormTypeOptions([
            "entry_type"=>ChecklistType::class,
            'by_reference'=>false
        ])
        ->setEntryIsComplex(true)
        ->allowAdd()
        ->allowDelete();

        $carsCount =  AssociationField::new('cars','Cars');
        $rentalsCount =  AssociationField::new('rentals','Rentals');
        $userCarAdditionalsCount =  AssociationField::new('userCarAdditionals','Car Additionals');
        $userDrivingRestrictionsCount =  AssociationField::new('userDrivingRestrictions','Driving Restrictions');
        $userTripChecklistsCount =  AssociationField::new('userTripChecklists','Trip Checklists');

        switch ($pageName) {
            case Action::INDEX:
                return [
                    $username,  
                    $email,  
                    $carsCount,  
                    $userTripChecklistsCount,
                    $userDrivingRestrictionsCount, 
                    $userCarAdditionalsCount,  
                    $rentalsCount,         
                    $type ,
            ];
                break;
            case Action::EDIT:
                return [ $id ,
                $fullname,
                $address,
                $phone,
                $email,
                $type ,
                $state ,
                $cars,  
                $userTripChecklists,
                $userDrivingRestrictions, 
                $userCarAdditionals,  
                $rentals,                       
            ];
                break;
            case Action::NEW:
                return [
                    $id ,
                    $fullname,
                    $address,
                    $phone,
                    $email,
                    $password,
                    $salt,
                    $type ,
                    $state , 
                    $cars,  
                    $userTripChecklists,
                    $userDrivingRestrictions, 
                    $userCarAdditionals,  
                    $rentals,
                      
            ];
                break;
            default:
            return [
                $id ,
                $username,
                $fullname,
                $address,
                $phone,
                $email,
                $password,
                $salt,
                $type ,
                $state ,
                $lastPasswordResetRequestDate,
                $lastlogin,
                $lastlogout,   
                $dateModified,
                $dateCreated, 
                $cars,  
                $userTripChecklists,
                $userDrivingRestrictions, 
                $userCarAdditionals,  
                $rentals,
];
                break;
            }
    }
}
