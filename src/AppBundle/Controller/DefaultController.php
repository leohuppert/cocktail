<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        // Si l'utilisateur n'est pas connecté
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $session = $this->get('session');

            // Si le tableau de recette préférées n'a pas encore été créé
            if ($session->get('favorites') === null) {
                $session->set('favorites', array());
            }
        }

        return $this->render('default/index.html.twig');
    }
}
