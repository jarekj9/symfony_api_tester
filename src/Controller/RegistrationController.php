<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use App\Entity\User;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $form = $this->createFormBuilder()
            ->add('username')
            ->add('password', RepeatedType::class,[
                    'type' => PasswordType::class,
                    'required' => true,
                    'first_options' => ['label' => 'Password'],
                    'second_options' => ['label' => 'Confirm Password']
            ])
            ->add('register', SubmitType::class)
            ->getForm()
        ;

        $form->handleRequest($request);

        if($form->isSubmitted()){
            $data = $form->getData();
            $user = new User();
            $user->setUsername($data['username']);
            $user->setPassword( $passwordEncoder->encodePassword($user, $data['password']) );
            $entity = $this->getDoctrine()->getManager();
            $entity->persist($user);
            $entity->flush();

            return $this->redirect($this->generateUrl('app_login'));
        }

        return $this->render('registration/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
