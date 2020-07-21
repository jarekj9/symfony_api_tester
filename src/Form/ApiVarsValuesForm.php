<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use App\Entity\ApiUrls;
use App\Entity\User;


class ApiVarsValuesForm extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name', null, array(
                'label' => 'Name: ',
                'attr' => array('style' => 'width:200px'),
            ))
            ->add('var', null, array(
                'label' => 'Key: ',
                'attr' => array('style' => 'width:200px'),
            ))
            ->add('value', null, array(
                'label' => 'Value: ',
                'attr' => array('style' => 'width:200px'),
            ))
            ->add('urlkey', EntityType::class, array(
                'class' => ApiUrls::class,
                'query_builder' => function (EntityRepository $er) use($options)   //show choicefield with urls, owned only by current user
                                        {
                                        return $er->createQueryBuilder('u')
                                            ->where('u.userkey = '.$options['user']->getId())
                                            ->orderBy('u.name', 'ASC');
                                        },
                'label' => 'URL: ',
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

        //when creating form it requires to pass object: array('user' => $this->getUser())
        $resolver->setRequired('user');

        // type validation - User instance or int, you can also pick just one.
        $resolver->setAllowedTypes('user', array(User::class, 'int'));
    }
}
