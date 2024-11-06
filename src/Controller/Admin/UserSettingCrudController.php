<?php

namespace App\Controller\Admin;

use App\Entity\UserSetting;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class UserSettingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UserSetting::class;
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('User Setting')
            ->setEntityLabelInPlural('User Settings')
        ;
    }



    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id')->hideOnForm();
        $user = AssociationField::new("addedby","Owner");;
        $notifications = BooleanField::new('notifications','notifications');
        $account = BooleanField::new('account','account');
        $sms = BooleanField::new('sms','Sms');
        $email = BooleanField::new('email','Email');
    

        switch ($pageName) {
            case Action::INDEX:
                return [
                    $user,
                    $notifications,
                    $account,
                    $sms,
                    $email
            ];
                break;
            case Action::EDIT:
                return [                    
                    $user,
                    $notifications,
                    $account,
                    $sms,
                    $email
            ];
                break;
            case Action::NEW:
                return [                   
                    $user,
                    $notifications,
                    $account,
                    $sms,
                    $email
            ];
                break;
            default:
            return [                   
                $user,
                $notifications,
                $account,
                $sms,
                $email
                ];
                break;
            }
    }
}
