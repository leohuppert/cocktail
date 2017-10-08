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
     * Creates a new recipe entity.
     *
     * @Route("/new", name="recipe_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $recipe = new Recipe();
        $form = $this->createForm('AppBundle\Form\RecipeType', $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($recipe);
            $em->flush();

            return $this->redirectToRoute('recipe_show', array('id' => $recipe->getId()));
        }

        return $this->render('recipe/new.html.twig', array(
            'recipe' => $recipe,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a recipe entity.
     *
     * @Route("/{id}", name="recipe_show")
     * @Method("GET")
     * @param Recipe $recipe
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Recipe $recipe)
    {
        $deleteForm = $this->createDeleteForm($recipe);

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
            'delete_form' => $deleteForm->createView(),
            'is_favorite' => $isFavorite,
        ));
    }

    /**
     * Displays a form to edit an existing recipe entity.
     *
     * @Route("/{id}/edit", name="recipe_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Recipe $recipe
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Recipe $recipe)
    {
        $deleteForm = $this->createDeleteForm($recipe);
        $editForm = $this->createForm('AppBundle\Form\RecipeType', $recipe);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('recipe_edit', array('id' => $recipe->getId()));
        }

        return $this->render('recipe/edit.html.twig', array(
            'recipe'      => $recipe,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a recipe entity.
     *
     * @Route("/{id}", name="recipe_delete")
     * @Method("DELETE")
     * @param Request $request
     * @param Recipe  $recipe
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Recipe $recipe)
    {
        $form = $this->createDeleteForm($recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($recipe);
            $em->flush();
        }

        return $this->redirectToRoute('recipe_index');
    }

    /**
     * Creates a form to delete a recipe entity.
     *
     * @param Recipe $recipe The recipe entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Recipe $recipe)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('recipe_delete', array('id' => $recipe->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
