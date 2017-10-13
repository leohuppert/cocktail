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
        //dump($superAliments);die();

        //$superAliments = $this->getAriane($superAliments);
        //dump($superAliments);die();

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
        $superAliments = $aliment->getSuperAliments();

        switch ($superAliments->count()) {

            case 0:
                return $res;

            case 1:
                $res[$aliment->getName()] = $superAliments->first();
                return $this->getSuperAliments($superAliments->first(), $res);

            default:
                $arr = array();

                foreach ($superAliments as $key => $super) {
                    $res[$aliment->getName()][$key] = $super;
                    $arr[] = $this->getSuperAliments($super);
                }

                for ($i = 0; $i < count($arr); $i++) {
                    $res = array_merge($res, $arr[$i]);
                }
                return $res;
        }
    }

    /**
     * Should return an array containing all the "fil d'ariane" for an aliment.
     *
     * @param array $superAliments
     * @return array
     */
    private function getAriane(array $superAliments)
    {
        $res = array();
        $counter = 1;
        foreach ($superAliments as $aliment => $supers) { // while (isset($supers->getSuperAliments()))
            if (is_array($supers)) {
                $counter *= count($supers);
                $arr = array();

                for ($i = 0; $i < $counter; $i++) {
                    for($j = 0; $j < count($res); $j++) {
                        $arr[] = $res[$j]; // not bad, but not good either
                    }

                    $arr[$i][] = $supers[$i]->getName(); //good ?
                }

                $res = $arr;

            } else {
                for ($i = 0; $i < $counter; $i++) {
                    $res[$i][] = $supers->getName(); // mauvais, voir avec l'idée du while plus haut
                }
            }
        }

        return $res;
    }
}
