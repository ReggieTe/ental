<?php

namespace App\Controller\Admin;

use App\Entity\AppSettings;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AppSettingsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AppSettings::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('App setting')
            ->setEntityLabelInPlural('App settings')
        ;
    }
    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id')->hideOnForm();     
        $name = TextField::new('name','Name')->setFormTypeOptions(['required'=>true]);
        $defaultValue = TextField::new('defaultValue','Default Value')->setFormTypeOptions(['required'=>true]);
        $customValue = TextField::new('customValue','Custom Value')->setFormTypeOptions(['required'=>true]);

        
        switch ($pageName) {
            case Action::INDEX:
                return [
                    $name,
                    $defaultValue,
                    $customValue                    
            ];
                break;
            case Action::EDIT:
                return [ 
                $id ,
                $name,
                $defaultValue,
                $customValue  
                                     
            ];
                break;
            case Action::NEW:
                return [
                    $id ,
                    $name,
                    $defaultValue,
                    $customValue  
                      
            ];
                break;
            default:
            return [
                $id , 
                $name,
                $defaultValue,
                $customValue  
];
                break;
            }
    }
}
