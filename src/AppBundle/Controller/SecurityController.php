<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     * @return Response
     */
    public function loginAction(): Response
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        // Erreur de login s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();

        // Dernier username donné par l'utilisateur
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'error'         => $error,
            'last_username' => $lastUsername
        ]);
    }

    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $em = $this->getDoctrine()
                ->getManager();
            $em->persist($user);
            $em->flush();

            // Flash message
            $this->addFlash('notice', 'Vous êtes maintenant enregistré !');

            return $this->redirectToRoute('homepage');
        }

        return $this->render('security/register.html.twig', array(
            'form'   => $form->createView(),
            'errors' => $form->getErrors(true)
        ));
    }

    /**
     * @param Request $request
     * @param User $user
     * @return Response
     *
     * @Route("/profile/{id}", name="profile_edit")
     * @Method({"GET","POST"})
     */
    public function editUserAction(Request $request, User $user): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {

            // TODO: Verif id
            if ($this->getUser() !== $user) {
                $this->addFlash('error', 'Vous ne pouvez pas accéder à cette page');

                return $this->redirectToRoute('homepage');
            }

            $form = $this->createForm('AppBundle\Form\EditUserType', $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                try {
                    $this->getDoctrine()
                        ->getManager()
                        ->flush();
                } catch (UniqueConstraintViolationException $exception) {
                    // On regarde si un utilisateur a le même nom d'utilisateur
                    $this->addFlash('error', 'Un utilisateur a déjà cet identifiant.');

                    return $this->redirectToRoute('profile_edit', [
                        'id' => $this->getUser()->getId()
                    ]);
                }

                $this->addFlash('notice', 'Vos informations ont bien été mises à jour !');

                return $this->redirectToRoute('homepage');
            }

            return $this->render(':security:edit.html.twig', array(
                'form' => $form->createView(),
            ));
        }

        $this->addFlash('error', 'Connectez-vous pour accéder à cette page');

        return $this->redirectToRoute('homepage');
    }
}
