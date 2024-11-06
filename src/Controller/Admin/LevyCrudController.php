<?php

namespace App\Controller\Admin;

use App\Entity\Levy;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class LevyCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Levy::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('Levy')
            ->setEntityLabelInPlural('Levies')
        ;
    }
    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id')->hideOnForm();     
        $name = TextField::new('name','Name')->setFormTypeOptions(['required'=>true]);
        $description = TextareaField::new('description','Description')->setFormTypeOptions([]);
        $amount = IntegerField::new('amount','Pecentage')->setFormTypeOptions(['required'=>true]);
        $mandatory = BooleanField::new('mandatory','Mandatory')->setFormTypeOptions([]);
        $active = BooleanField::new('active','Active')->setFormTypeOptions(['required'=>true]);

        
        switch ($pageName) {
            case Action::INDEX:
                return [                    
                        $name,
                        $description,
                        $amount,
                        $mandatory,
                        $active                    
                        ];
                break;
            case Action::EDIT:
                return [ 
                $id ,                    
                $name,
                $description,
                $amount,
                $mandatory,
                $active            
            ];
                break;
            case Action::NEW:
                return [
                    $id ,                     
                    $name,
                    $description,
                    $amount,
                    $mandatory,
                    $active
                      
            ];
                break;
            default:
            return [
                $id ,                     
                $name,
                $description,
                $amount,
                $mandatory,
                $active
];
                break;
            }
    }
}
