<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Aliment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

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
        $superAliments = $this->getSuperAliments($aliment);

        return $this->render('aliment/show.html.twig', array(
            'aliment'        => $aliment,
            'super_aliments' => array_reverse($superAliments),
        ));
    }

    /**
     * @param Aliment $aliment
     * @param array $res
     * @return array
     */
    private function getSuperAliments(Aliment $aliment, array $res = array())
    {
        if ($aliment->getSuperAliments()->count() === 0) {
            return $res;
        }

        $res[] = $aliment->getSuperAliments()[0];
        return $this->getSuperAliments($aliment->getSuperAliments()[0], $res);
    }
}
