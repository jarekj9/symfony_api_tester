<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ApiForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url', null, array(
                'label' => 'URL: ',
                'attr' => array('style' => 'width:500px'),
            ))
            ->add('var', null, array(
                'label' => 'Variable: ',
                'attr' => array('style' => 'width: 200px'),
            ))
            ->add('value', null, array(
                'label' => 'Value: ',
                'attr' => array('style' => 'width: 200px'),
            ))
            ->add('type', ChoiceType::class, array(
                'choices' => [
                    'GET' => 'GET',
                    'POST' => 'POST',
                ],
                'label' => 'Type: ',
                'attr' => array('style' => 'width: 200px'),
            ))
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
