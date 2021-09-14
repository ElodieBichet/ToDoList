<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/users", name="user_list")
     * @IsGranted("ROLE_ADMIN", message="You have to be authenticated as an admin to see this page")
     */
    public function listAction()
    {
        return $this->render('user/list.html.twig', ['users' => $this->getDoctrine()->getRepository(User::class)->findAll()]);
    }

    /**
     * @Route("/users/create", name="user_create")
     */
    public function createAction(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $password = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            // Add ROLE_ADMIN to user roles if admin checkbox is checked 
            if ($this->isGranted("ROLE_ADMIN")) {
                ($form->get('admin')->getData()) ? $user->setRoles(['ROLE_ADMIN']) : $user->setRoles([]);
            }

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', "Le compte utilisateur a bien été créé.");

            if ($this->isGranted("ROLE_ADMIN")) {
                return $this->redirectToRoute('user_list');
            }
            return $this->redirectToRoute('login');
        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/users/{id}/edit", name="user_edit")
     * @IsGranted("USER_EDIT", subject="user", message="You can only edit your own account")
     */
    public function editAction(User $user, Request $request, UserPasswordEncoderInterface $encoder)
    {
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            // Add/Remove ROLE_ADMIN to/from user roles if admin checkbox is/isn't checked
            if ($this->isGranted("ROLE_ADMIN")) {
                ($form->get('admin')->getData()) ? $user->setRoles(['ROLE_ADMIN']) : $user->setRoles([]);
            }

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "Le compte utilisateur a bien été modifié");

            if ($this->isGranted("ROLE_ADMIN")) {
                return $this->redirectToRoute('user_list');
            }
            return $this->redirectToRoute('homepage');
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }

    /**
     * @Route("/users/{id}/delete", name="user_delete")
     * @IsGranted("USER_DELETE", subject="user", message="You can't delete users")
     */
    public function deleteUserAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'L\'utilisateur a bien été supprimé.');

        return $this->redirectToRoute('user_list');
    }
}
