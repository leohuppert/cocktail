<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Aliment;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Aliment controller.
 *
 * @Route("aliment")
 */
class AlimentController extends Controller
{
    /**
     * Liste tous les aliments
     *
     * @Route("/", name="aliment_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()
            ->getManager();

        $aliments = $em->getRepository('AppBundle:Aliment')
            ->findAll();

        return $this->render('aliment/index.html.twig', array(
            'aliments' => $aliments,
        ));
    }

    /**
     * Affiche un aliment et les recettes qui en dépendent + ses sous aliments
     *
     * @Route("/{id}", name="aliment_show")
     * @Method("GET")
     * @param Aliment $aliment
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Aliment $aliment)
    {
        $superAliments = $this->getBreadcrumb($aliment);

        $subAliments = $this->getSubAliments2($aliment);
        $subAliments[] = $aliment;

        $filteredRecipes = array();

        foreach ($subAliments as $a) {
            foreach ($a->getRecipes() as $r) {
                $filteredRecipes[] = $r;
            }
        }

        $filteredRecipes = array_unique($filteredRecipes);
        sort($filteredRecipes);

        return $this->render('aliment/show.html.twig', array(
            'aliment'        => $aliment,
            'super_aliments' => $superAliments,
            'recipes'        => $filteredRecipes,
        ));
    }

    /**
     * Retourne un tableau contenant le fil d'ariane
     *
     * @param Aliment $aliment
     * @return array
     */
    private function getBreadcrumb(Aliment $aliment)
    {
        $sessionBreadcrumb = $this->get('session')
            ->get('breadcrumb');

        // si un fil d'ariane est en session
        if ($sessionBreadcrumb !== null) {

            return $this->getSessionBreadcrumb($aliment, $sessionBreadcrumb);
        } else {
            $breadcrumb = array(0 => $aliment->getId());
            $breadcrumb = array_reverse(array_merge($breadcrumb, $this->getDefaultBreadcrumb($aliment)));
            $this->get('session')->set('breadcrumb', $breadcrumb);

            $breadcrumb = $this->buildBreadcrumb($breadcrumb);
            return $breadcrumb;
        }
    }

    /**
     * Retourne le fil d'ariane stocké en session
     * @param Aliment $aliment
     * @param array $sessionBreadcrumb
     * @return array
     */
    private function getSessionBreadcrumb(Aliment $aliment, array $sessionBreadcrumb)
    {
        $em = $this->getDoctrine()
            ->getManager();

        $sessionAliments = array();
        foreach ($sessionBreadcrumb as $sessionAliment) {
            $tmp = $em->getRepository('AppBundle\Entity\Aliment')
                ->findBy(array('id' => $sessionAliment));
            $sessionAliments[] = array_shift($tmp);
        }
        $lastSessionAliment = end($sessionAliments);

        // Cas $lastSessionAliment = un superAliment de l'aliment en cours
        // On descend dans le fil d'ariane
        if ($aliment->getSuperAliments()->contains($lastSessionAliment)) {
            $sessionBreadcrumb[] = $aliment->getId();
            $this->get('session')->set('breadcrumb', $sessionBreadcrumb);

            return $this->buildBreadcrumb($sessionBreadcrumb);

            // Cas $lastSessionAliment = un subAliment proche ou lointain de l'aliment en cours
            // On remonte dans le fil d'ariane
        } else if (in_array($aliment, $sessionAliments)) {
            $length = array_search($aliment, $sessionAliments) + 1;
            $sessionBreadcrumb = array_slice($sessionBreadcrumb, 0, $length);
            $this->get('session')->set('breadcrumb', $sessionBreadcrumb);


            return $this->buildBreadcrumb($sessionBreadcrumb);

            // sinon vider session et retourner le fil d'ariane par défaut
        } else {
            $this->get('session')->remove('breadcrumb');

            $breadcrumb = array(0 => $aliment->getId());
            $breadcrumb = array_reverse(array_merge($breadcrumb, $this->getDefaultBreadcrumb($aliment)));
            $this->get('session')->set('breadcrumb', $breadcrumb);

            $breadcrumb = $this->buildBreadcrumb($breadcrumb);
            return $breadcrumb;
        }
    }

    /**
     * Retourne un fil d'ariane à afficher par défaut
     * @param Aliment $aliment
     * @param array $res
     * @return array
     */
    private function getDefaultBreadcrumb(Aliment $aliment, array $res = array())
    {
        $superAliments = $aliment->getSuperAliments();

        if ($superAliments->count() === 0) {
            return $res;
        } else {
            $res[] = $superAliments->first()->getId();
            return $this->getDefaultBreadcrumb($superAliments->first(), $res);
        }
    }

    /**
     * Builds a breadcrumb array that matches the order of displaying aliments.
     * @param array $breadcrumb
     * @return array
     */
    private function buildBreadcrumb(array $breadcrumb)
    {
        $em = $this->getDoctrine()->getManager();
        $finalBreadcrumb = array();

        foreach ($breadcrumb as $alimentId) {
            $finalBreadcrumb[] = $em->getRepository('AppBundle\Entity\Aliment')
                ->findBy(array('id' => $alimentId))[0];
        }

        return $finalBreadcrumb;
    }

    /**
     * @param Aliment $aliment
     * @param array $res
     * @return array
     */
    private function getSubAliments(Aliment $aliment, $res = array())
    {
        foreach ($aliment->getSubAliments() as $subAliment) {
            if (!in_array($subAliment->getId(), $res)) {
                $res[] = $subAliment->getId();
                if ($subAliment->getSubAliments()->count() > 0) {
                    $res = $this->getSubAliments($subAliment, $res);
                }
            }
        }

        return $res;
    }

    private function getSubAliments2(Aliment $aliment, $res = array())
    {
        foreach ($aliment->getSubAliments() as $subAliment) {
            if (!in_array($subAliment, $res)) {
                $res[] = $subAliment;
                if ($subAliment->getSubAliments()->count() > 0) {
                    $res = $this->getSubAliments2($subAliment, $res);
                }
            }
        }

        return $res;
    }
}
