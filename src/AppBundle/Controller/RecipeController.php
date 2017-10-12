<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Recipe;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Recipe controller.
 *
 * @Route("recipe")
 */
class RecipeController extends Controller
{
    /**
     * Lists all recipe entities.
     *
     * @Route("/", name="recipe_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $recipes = $em->getRepository('AppBundle:Recipe')->findAll();

        return $this->render('recipe/index.html.twig', array(
            'recipes' => $recipes,
        ));
    }

    /**
     * @Route("/favorites", name="recipe_favorites")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showFavorites()
    {
        // Deux cas: connecté ou pas connecté
        // Si connecté : favoris à charger de la BDD
        // Pas connecté : session

        $favorites = array();
        $em = $this->getDoctrine()
            ->getManager();

        // Utilisateur non connecté => Session
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            if ($this->get('session')->get('favorites') !== null) {
                foreach ($this->get('session')->get('favorites') as $recipeId) {

                    $r = $em->getRepository('AppBundle:Recipe')
                        ->findBy(array('id' => $recipeId))[0];
                    $favorites[] = $r;
                }
            }
        }
        // Utilisateur connecté => BDD
        else {

            $favorites = $this->getUser()->getFavoriteRecipes();
        }

        return $this->render(':recipe:favorites.html.twig', array(
            'favorites' => $favorites,
        ));
    }

    /**
     * @Route("/add-session-favorite/{id}", name="recipe_add_session_favorite")
     * @Method({"GET", "POST"})
     * @param Recipe $recipe
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addSessionFavoriteRecipe(Recipe $recipe)
    {
        // On vérifie que l'utilisateur n'est pas connecté à l'aplication
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {

            // On vérifie l'existence du tableau de recettes préférées en session
            if ($this->get('session')->get('favorites') !== null) {

                // On vérifie que la recette n'est pas déjà dans les favoris
                if (!in_array($recipe->getId(), $this->get('session')->get('favorites'))) {
                    $tmp = $this->get('session')->get('favorites');
                    $tmp[] = $recipe->getId();
                    $this->get('session')->set('favorites', $tmp);
                }
            }
        }

        return $this->redirectToRoute('recipe_show', array(
            'id' => $recipe->getId()
        ));
    }

    /**
     * @Route("/remove-session-favorite/{id}", name="recipe_remove_session_favorite")
     * @Method({"GET", "POST"})
     * @param Recipe  $recipe
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeSessionFavoriteRecipe(Recipe $recipe, Request $request)
    {
        // On vérifie que l'utilisateur n'est pas connecté à l'aplication
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {

            // On vérifie l'existence du tableau de recettes préférées en session
            if ($this->get('session')->get('favorites') !== null) {

                // On vérifie que la recette à supprimer est bien dans les favoris
                if (in_array($recipe->getId(), $this->get('session')->get('favorites'))) {

                    $tmp = $this->get('session')->get('favorites');

                    if(($key = array_search($recipe->getId(), $tmp)) !== false) {
                        unset($tmp[$key]);
                        $this->get('session')->set('favorites', $tmp);
                    }
                }
            }
        }

        // Route qui a redirigé => favorites ; alors on redirige vers favorites
        if (preg_match('/^.*\/recipe\/favorites/', $request->headers->get('referer'))) {
            return $this->redirectToRoute('recipe_favorites');
        }

        return $this->redirectToRoute('recipe_show', array(
            'id' => $recipe->getId(),
        ));
    }

    /**
     * @Route("/add-user-favorite/{id}", name="recipe_add_user_favorite")
     * @Method({"GET", "POST"})
     * @param Recipe $recipe
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addUserFavoriteRecipe(Recipe $recipe)
    {
        // On vérifie que l'utilisateur est bien connecté
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {

            $em = $this->getDoctrine()
                ->getManager();

            /* @var User $user */
            $user = $this->getUser();
            $user->addFavoriteRecipe($recipe);

            $em->flush();
        }

        return $this->redirectToRoute('recipe_show', array(
            'id' => $recipe->getId(),
        ));
    }

    /**
     * @Route("/remove-user-favorite/{id}", name="recipe_remove_user_favorite")
     * @Method({"GET", "POST"})
     * @param Recipe $recipe
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeUserFavoriteRecipe(Recipe $recipe, Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {

            $em = $this->getDoctrine()
                ->getManager();

            /* @var User $user */
            $user = $this->getUser();
            $user->removeFavoriteRecipe($recipe);

            $em->flush();
        }

        // Route qui a redirigé => favorites ; alors on redirige vers favorites
        if (preg_match('/^.*\/recipe\/favorites/', $request->headers->get('referer'))) {
            return $this->redirectToRoute('recipe_favorites');
        }

        return $this->redirectToRoute('recipe_show', array(
            'id' => $recipe->getId(),
        ));
    }

    /**
     * Finds and displays a recipe entity.
     *
     * @Route("/{id}", name="recipe_show", options={"expose" = true})
     * @Method("GET")
     * @param Recipe $recipe
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Recipe $recipe)
    {
        $isFavorite = false;

        // Pas connecté
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {

            if ($this->get('session')->get('favorites') !== null) {

                // On regarde si la recette se trouve dans les favoris
                $isFavorite = in_array($recipe->getId(), $this->get('session')->get('favorites'));
            }
        }
        // Connecté
        else {
            $isFavorite = $this->getUser()->getFavoriteRecipes()->contains($recipe);
        }

        return $this->render('recipe/show.html.twig', array(
            'recipe'      => $recipe,
            'is_favorite' => $isFavorite,
        ));
    }
}
