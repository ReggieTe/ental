<?php 
namespace App\Util;


class Form
{
    public static function ErrorMessages(\Symfony\Component\Form\Form $form)
    {
        $errors = array();
        foreach ($form->getErrors() as $key => $error) {
            if ($form->isRoot()) {
                $errors['#'][] = $error->getMessage();
            } else {
                $errors[] = $error->getMessage();
            }
        }

        foreach ($form->all() as $child) {
            if (count(Form::ErrorMessages($child))) {
                $errors[$child->getName()]=current(Form::ErrorMessages($child));
            }
        }
        return $errors;
    }
}