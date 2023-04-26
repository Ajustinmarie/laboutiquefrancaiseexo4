<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountPasswordController extends AbstractController
{

     private $entityManager;

     public function __construct(EntityManagerInterface $entityManager)
     {
         $this->entityManager = $entityManager;
     }


    #[Route('/compte/modifier-mon-mot-de-passe', name: 'account_password')]
    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response
    {

      $notification=null;
      

      $user=$this->getUser();
      $form=$this->createForm(ChangePasswordType::class, $user);

       $form->handleRequest($request);
    
      if ($form->isSubmitted() && $form->isValid()) { 
     //  $form->submit($encoder->encodePassword($user, $user->getPlainPassword()));
       //  $form->handleRequest($request);


       $old_password=$form->get('old_password')->getData();

         if($encoder->isPasswordValid($user, $old_password))
         {

          $new_password=$form->get('new_password')->getData();

          $new_crypt_password=$encoder->encodePassword($user, $new_password);
          
        //  $user=$this->getUser();
          $user->setPassword($new_crypt_password);

       
              $this->entityManager->persist($user);
              $this->entityManager->flush();
              $notification='Votre mot de passe a bien été mise a jour';

       //      $this->entityManager->persist($user);
       //      $this->entityManager->flush();
         }
         else
         {
          $notification='Votre mot de passe actuel n\'est pas le bon';
         }

     }


      return $this->render('account/account_password.twig', [
          'form' => $form->createView(),
          'notification' => $notification,
      ]);
    }
}
