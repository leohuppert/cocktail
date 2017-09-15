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
     * Creates a new aliment entity.
     *
     * @Route("/new", name="aliment_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $aliment = new Aliment();
        $form = $this->createForm('AppBundle\Form\AlimentType', $aliment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($aliment);
            $em->flush();

            return $this->redirectToRoute('aliment_show', array('id' => $aliment->getId()));
        }

        return $this->render('aliment/new.html.twig', array(
            'aliment' => $aliment,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a aliment entity.
     *
     * @Route("/{id}", name="aliment_show")
     * @Method("GET")
     */
    public function showAction(Aliment $aliment)
    {
        $deleteForm = $this->createDeleteForm($aliment);

        return $this->render('aliment/show.html.twig', array(
            'aliment' => $aliment,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing aliment entity.
     *
     * @Route("/{id}/edit", name="aliment_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Aliment $aliment)
    {
        $deleteForm = $this->createDeleteForm($aliment);
        $editForm = $this->createForm('AppBundle\Form\AlimentType', $aliment);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('aliment_edit', array('id' => $aliment->getId()));
        }

        return $this->render('aliment/edit.html.twig', array(
            'aliment' => $aliment,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a aliment entity.
     *
     * @Route("/{id}", name="aliment_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Aliment $aliment)
    {
        $form = $this->createDeleteForm($aliment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($aliment);
            $em->flush();
        }

        return $this->redirectToRoute('aliment_index');
    }

    /**
     * Creates a form to delete a aliment entity.
     *
     * @param Aliment $aliment The aliment entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Aliment $aliment)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('aliment_delete', array('id' => $aliment->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
