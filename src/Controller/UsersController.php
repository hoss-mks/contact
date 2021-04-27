<?php

namespace App\Controller;

use App\Entity\Contacts;
use App\Entity\Users;
use App\Form\ContactType;
use App\Form\FilterType;
use App\Form\loupeType;
use App\Form\UserEditType;
use App\Repository\ContactsRepository;
use App\Repository\UsersRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/users")
*/
class UsersController extends AbstractController
{
    /**
     * @Route("/", name="users")
     */
    public function index(Request $request, ContactsRepository $contactsRepo): Response
    {
        $filtreForm = $this->createForm(FilterType::class) ;
        $loupeForm=$this->createForm(loupeType::class);

        $filtreForm->handleRequest($request);

        $loupeForm->handleRequest($request);

        if($loupeForm->isSubmitted() && $loupeForm->isValid() ){

        $contacts=$contactsRepo->filtre($filtreForm->getData(),$loupeForm->getData());
        return $this->render('users/index.html.twig', [
            'controller_name' => 'UsersController',
            'filtreForm'=> $filtreForm->createView(),
            'loupeForm'=>$loupeForm->createView(),
            'contacts'=> $contacts
        ]);
        }
        
        if($filtreForm->isSubmitted() && $filtreForm->isValid() ){

            $contacts=$contactsRepo->filtre($filtreForm->getData(),$loupeForm->getData());
            return $this->render('users/index.html.twig', [
                'controller_name' => 'UsersController',
                'filtreForm'=> $filtreForm->createView(),
                'loupeForm'=>$loupeForm->createView(),
                'contacts'=> $contacts
            ]);
        }

        $contacts= $contactsRepo->findAll();
        return $this->render('users/index.html.twig', [
            'controller_name' => 'UsersController',
            'filtreForm'=> $filtreForm->createView(),
            'loupeForm'=>$loupeForm->createView(),
            'contacts'=> $contacts
        ]);
    }

    /**
     * @Route("/ajouter", name="ajouterContact")
     */
    public function ajouterContact(Request $request): Response
    {
        $contact=new Contacts();
        $contact->setDate(new DateTime());
        $form=$this->createForm(ContactType::class,$contact);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em=$this->getDoctrine()->getManager();
            $contact->setUser($this->getUser());
            $em->persist($contact);
            $em->flush();

            return $this->redirectToRoute('users');
        }
        return $this->render('users/AjouterContact.html.twig', [
            'contactform' => $form->createView(),
        ]);
    }

    /**
     * @Route("/modifier", name="modifier")
     */
    public function modifier(Request $request): Response
    {
        $user= $this->getUser();

        $editform = $this->createForm(UserEditType::class,$user);
        return $this->render('users/EditUsers.html.twig', [
            'editform' => $editform->createView(),
        ]);


    }
}
