<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * Affiche la page d'accueil
     *
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
        // L'utilisateur est connecté
        else {

            $sessionFavs = $this->get('session')->get('favorites');
            $em = $this->getDoctrine()
                ->getManager();

            // On regarde s'il existe un table de recettes préférées en session et que des recettes ont été ajoutées
            if ($sessionFavs !== null && !empty($sessionFavs)) {

                $addedRecipes = array();

                foreach ($sessionFavs as $recipeId) {

                    $recipe = $em->getRepository('AppBundle:Recipe')
                        ->findBy(['id' => $recipeId])[0];

                    if (!$this->getUser()->getFavoriteRecipes()->contains($recipe)) {
                        $this->getUser()->addFavoriteRecipe($recipe);
                        $addedRecipes[] = $recipe;
                    }
                }

                $em->flush();

                // Destruction tableau session
                $this->get('session')->remove('favorites');

                // Message flash pour indiquer les recettes ajoutés depuis la session
                $flashMessage = 'Recette(s) ajoutée(s) aux favoris : ';
                foreach ($addedRecipes as $recipe) {
                    $flashMessage = $flashMessage . $recipe->getName() . ', ';
                }
                $flashMessage = substr($flashMessage, 0, strlen($flashMessage) - 2);
                $this->addFlash('notice', $flashMessage);
            }
        }

        $aliment = $this->getDoctrine()->getRepository('AppBundle:Aliment')
            ->findBy(['name' => 'Aliment'])[0];

        return $this->render('default/index.html.twig', array(
            'aliment_id' => $aliment->getId()
        ));
    }
}
