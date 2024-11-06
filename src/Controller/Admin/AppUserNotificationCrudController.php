<?php

namespace App\Controller\Admin;

use App\Entity\AppUserNotification;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;


class AppUserNotificationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AppUserNotification::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('App Notification')
            ->setEntityLabelInPlural('App Notifications')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // the labels used to refer to this entity in titles, buttons, etc.
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE)
            ->remove(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN)
            ;
    }
    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id')->hideOnForm();
        $header= TextField::new('head','Header');
       // $receiver = AssociationField::new('notification','Receiver');
        $body= TextareaField::new('body','Body');
        $state = BooleanField::new('state',"Status");
        $dateModified= DateField::new('dateModified','dateModified');
        $dateCreated= DateField::new('dateCreated','dateCreated');
        

        switch ($pageName) {
            case Action::INDEX:
                return [
                   // $receiver,
                    $header,
                    $state,
                    $dateCreated
            ];
                break;
            case Action::EDIT:
                return [ 
                    $header,
                    $body,
                    $state,
                    $dateCreated                 
            ];
                break;
            case Action::NEW:
                return [
                    $header,
                    $body,
                    $state,
                    $dateCreated
            ];
                break;
            default:
            return [
                $header,
                $body,
                $state,
                $dateCreated
        ];
                break;
            }
    }
}
