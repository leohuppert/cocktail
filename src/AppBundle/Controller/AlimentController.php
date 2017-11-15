<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Aliment;
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
     * Lists all aliment entities.
     *
     * @Route("/", name="aliment_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $aliments = $em->getRepository('AppBundle:Aliment')->findAll();

        return $this->render('aliment/index.html.twig', array(
            'aliments' => $aliments,
        ));
    }

    /**
     * Finds and displays a aliment entity.
     *
     * @Route("/{id}", name="aliment_show")
     * @Method("GET")
     * @param Aliment $aliment
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Aliment $aliment)
    {
        $sa = $this->getSuperAliments($aliment);
        $superAliments = $this->getBreadcrumb($aliment);

        return $this->render('aliment/show.html.twig', array(
            'aliment' => $aliment,
            'super_aliments' => $superAliments,
        ));
    }

    /**
     * Returns an array filled with every breadcrumb possible.
     *
     * @param Aliment $aliment
     * @return array
     */
    private function getBreadcrumb(Aliment $aliment)
    {
        $this->get('session')->set('breadcrumb', array(0 => 251));

        $sessionBreadcrumb = $this->get('session')
            ->get('breadcrumb');

        // si un fil d'ariane est en session
        if ($sessionBreadcrumb !== null) {
            return $this->getSessionBreadcrumb($aliment, $sessionBreadcrumb);
        } else {
            return $this->getDefaultBreadcrumb($aliment);
        }
    }

    private function getSessionBreadcrumb(Aliment $aliment, array $sessionBreadcrumb)
    {
        $em = $this->getDoctrine()
            ->getManager();

        $sessionAliment = $em->getRepository('AppBundle\Entity\Aliment')
            ->findBy(array('id' => $sessionBreadcrumb[count($sessionBreadcrumb) - 1]))[0];

        // Cas $sessionAliment = un superAliment de l'aliment en cours
        if ($aliment->getSuperAliments()->contains($sessionAliment)) {
            $sessionBreadcrumb[] = $aliment->getId();
            $this->get('session')->set('breadcrumb', $sessionBreadcrumb);

            return $this->buildBreadcrumb($sessionBreadcrumb);

            // Cas $sessionAliment = un subAliment de l'aliment en cours
        } else if ($aliment->getSubAliments()->contains($sessionAliment)) {
            unset($sessionBreadcrumb[count($sessionBreadcrumb) - 1]);
            $this->get('session')->set('breadcrumb', $sessionBreadcrumb);

            return $this->buildBreadcrumb($sessionBreadcrumb);

            // sinon si $sessionAliment différent de l'aliment en cours vider
            // session et retourner le fil d'ariane par défaut
        } else if ($aliment !== $sessionAliment) {
            $this->get('session')->remove('breadcrumb');

            return $this->getDefaultBreadcrumb($aliment);
        }
    }

    /**
     * @param Aliment $aliment
     * @param array $res
     * @return array
     */
    private function getDefaultBreadcrumb(Aliment $aliment, array $res = array())
    {
        $superAliments = $aliment->getSuperAliments();

        switch ($superAliments->count()) {

            case 0:
                return $res;

            default:
                $res[] = $superAliments->first();
                return $this->getSuperAliments($superAliments->first(), $res);
        }

    }

    /**
     * @param array $breadcrumb
     */
    private
    function buildBreadcrumb(array $breadcrumb)
    {

    }
}
