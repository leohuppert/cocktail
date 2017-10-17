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
        $superAliments = $this->getBreadcrumb($this->getSuperAliments($aliment));

        return $this->render('aliment/show.html.twig', array(
            'aliment'        => $aliment,
            'super_aliments' => $superAliments,
        ));
    }

    /**
     * Returns an array filled with the SuperAliment tree for a single Aliment.
     *
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
                $res[] = $superAliments->first();
                return $this->getSuperAliments($superAliments->first(), $res);

            default:
                $arr = array();
                $tmp = array();

                foreach ($superAliments as $key => $super) {
                    $tmp[$key] = $super;
                    $arr[] = $this->getSuperAliments($super);
                }
                $res[] = $tmp;

                for ($i = 0; $i < count($arr); $i++) {
                    $res = array_merge($res, $arr[$i]);
                }
                return $res;
        }
    }

    /**
     * Returns an array filled with every breadcrumb possible.
     *
     * @param array $superAliments
     * @return array
     */
    private function getBreadcrumb(array $superAliments)
    {
        $res = array();
        $arr = array();
        $cpt = 0;
        foreach ($superAliments as $key => $super) {
            if (is_array($super)) {
                for ($i = 0; $i < count($super); $i++) {
                    if (isset($res[$cpt])) {
                        $arr[$i] = $res[$cpt];
                    }
                    $arr[$i][] = $super[$i];
                }
                $res = $arr;
            } else {
                $res[$cpt][] = $super;
                if ($super->getName() === "Aliment") {
                    $cpt++;
                }
            }
        }

        foreach ($res as $key => $re) {
            $res[$key] = array_reverse($re);
        }

        return $res;
    }
}
