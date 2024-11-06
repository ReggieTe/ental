<?php

namespace App\Controller\Admin;

use App\Entity\Enum\SectionEnum;
use App\Entity\InformationCenter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class InformationCenterCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return InformationCenter::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('Information center item')
            ->setEntityLabelInPlural('Information center items')
        ;
    }
    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id')->hideOnForm();     
        $header = TextField::new('header','Header')->setFormTypeOptions(['required'=>true]);
        $section = ChoiceField::new('section','Section')->setChoices(SectionEnum::choices())->setFormTypeOptions(['required'=>true]);
        $body = TextareaField::new('body','Body')->setFormTypeOptions(['required'=>true]);
        $status = BooleanField::new('status','Status')->setFormTypeOptions(['required'=>true]);
        $list = IntegerField::new('list','Sorting order')->setFormTypeOptions(['required'=>true]);

        
        switch ($pageName) {
            case Action::INDEX:
                return [
                    $header, 
                    $section,
                    $list,
                    $status
                    
            ];
                break;
            case Action::EDIT:
                return [ 
                $id , 
                $section,
                $header,
                $body,
                $list,
                $status
                                     
            ];
                break;
            case Action::NEW:
                return [
                    $id , 
                    $section,
                    $header,
                    $body,
                    $list,
                    $status
                      
            ];
                break;
            default:
            return [
                $id , 
                $section,
                $header,
                $body,
                $list,
                $status
];
                break;
            }
    }
}
