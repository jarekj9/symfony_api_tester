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
use App\Entity\ApiVarsValues;
use App\Entity\User;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class ApiSendDefinedForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('urlkey', EntityType::class, array(
            
                'class' => ApiUrls::class,
                'query_builder' => function (EntityRepository $er) use($options)   //show choicefield with urls, owned only by current user
                                        {
                                        return $er->createQueryBuilder('url')
                                            ->where('url.userkey = '.$options['user']->getId())
                                            ->orderBy('url.name', 'ASC');
                                        },
                'placeholder' => '',
                'label' => 'URL: ',
                )
            );

        $formModifier = function (FormInterface $form, ApiUrls $url = null) {
            $variables = null === $url ? [] : $url->getApiVarsValues();

            $form->add('var', EntityType::class, [
                'class' => 'App\Entity\ApiVarsValues',
                'placeholder' => '',
                'choices' => $variables,
            ]);
        };
        $builder->addEventListener(               //only to set initial empty fields for variable...
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event)  use ($formModifier) {
                    $data = $event->getData();
                    $url = null;
                    if($data) $url = $data->getUrlkey();
                    $formModifier($event->getForm(), $url);
                }
            );
        $builder->get('urlkey')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $url = $event->getForm()->getData();
                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $formModifier($event->getForm()->getParent(), $url);
            }
        );
        $builder->add('type', ChoiceType::class, array(
                'choices' => [
                    'GET' => 'GET',
                    'POST' => 'POST',
                ],
                'label' => 'Type: ',
                'attr' => array('style' => 'width: 200px'),
                "mapped" => false, //so this form can be mapped to ApiVarsValues entity (entity does not have 'type')
                )
            );
        $builder->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);

        //when creating form it requires to pass object: array('userkey' => $this->getUser())
        $resolver->setRequired('user');

        // type validation - User instance or int, you can also pick just one.
        $resolver->setAllowedTypes('user', array(User::class, 'int'));
    }
}
