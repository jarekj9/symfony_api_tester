<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ApiDefineUrl extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                'label' => 'Name: ',
                'attr' => array('style' => 'width:200px'),
            ))
            ->add('url', null, array(
                'label' => 'URL: ',
                'attr' => array('style' => 'width:500px'),
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Save',
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
