<?php

namespace Cpdg\AdministradorBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Cpdg\AdministradorBundle\Entity\EventosProveedores;
use Cpdg\AdministradorBundle\Form\EventosProveedoresType;

/**
 * EventosProveedores controller.
 *
 */
class EventosProveedoresController extends Controller
{
    /**
     * Lists all EventosProveedores entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $eventosProveedores = $em->getRepository('CpdgAdministradorBundle:EventosProveedores')->findAll();

        return $this->render('eventosproveedores/index.html.twig', array(
            'eventosProveedores' => $eventosProveedores,
        ));
    }

    /**
     * Creates a new EventosProveedores entity.
     *
     */
    public function newAction(Request $request)
    {
        $eventosProveedore = new EventosProveedores();
        $form = $this->createForm('Cpdg\AdministradorBundle\Form\EventosProveedoresType', $eventosProveedore);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($eventosProveedore);
            $em->flush();

            return $this->redirectToRoute('eventosproveedores_show', array('id' => $eventosProveedore->getId()));
        }

        return $this->render('eventosproveedores/new.html.twig', array(
            'eventosProveedore' => $eventosProveedore,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a EventosProveedores entity.
     *
     */
    public function showAction(EventosProveedores $eventosProveedore)
    {
        $deleteForm = $this->createDeleteForm($eventosProveedore);

        return $this->render('eventosproveedores/show.html.twig', array(
            'eventosProveedore' => $eventosProveedore,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing EventosProveedores entity.
     *
     */
    public function editAction(Request $request, EventosProveedores $eventosProveedore)
    {
        $deleteForm = $this->createDeleteForm($eventosProveedore);
        $editForm = $this->createForm('Cpdg\AdministradorBundle\Form\EventosProveedoresType', $eventosProveedore);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($eventosProveedore);
            $em->flush();

            return $this->redirectToRoute('eventosproveedores_edit', array('id' => $eventosProveedore->getId()));
        }

        return $this->render('eventosproveedores/edit.html.twig', array(
            'eventosProveedore' => $eventosProveedore,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a EventosProveedores entity.
     *
     */
    public function deleteAction(Request $request, EventosProveedores $eventosProveedore)
    {
        $form = $this->createDeleteForm($eventosProveedore);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($eventosProveedore);
            $em->flush();
        }

        return $this->redirectToRoute('eventosproveedores_index');
    }

    /**
     * Creates a form to delete a EventosProveedores entity.
     *
     * @param EventosProveedores $eventosProveedore The EventosProveedores entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(EventosProveedores $eventosProveedore)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('eventosproveedores_delete', array('id' => $eventosProveedore->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
