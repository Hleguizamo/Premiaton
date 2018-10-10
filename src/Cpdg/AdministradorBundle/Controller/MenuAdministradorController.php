<?php

namespace Cpdg\AdministradorBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Cpdg\AdministradorBundle\Entity\MenuAdministrador;
use Cpdg\AdministradorBundle\Form\MenuAdministradorType;

/**
 * MenuAdministrador controller.
 *
 */
class MenuAdministradorController extends Controller
{
    /**
     * Lists all MenuAdministrador entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $menuAdministradors = $em->getRepository('CpdgAdministradorBundle:MenuAdministrador')->findAll();

        return $this->render('menuadministrador/index.html.twig', array(
            'menuAdministradors' => $menuAdministradors,
        ));
    }

    /**
     * Creates a new MenuAdministrador entity.
     *
     */
    public function newAction(Request $request)
    {
        $menuAdministrador = new MenuAdministrador();
        $form = $this->createForm('Cpdg\AdministradorBundle\Form\MenuAdministradorType', $menuAdministrador);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($menuAdministrador);
            $em->flush();

            return $this->redirectToRoute('menuadministrador_show', array('id' => $menuAdministrador->getId()));
        }

        return $this->render('menuadministrador/new.html.twig', array(
            'menuAdministrador' => $menuAdministrador,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a MenuAdministrador entity.
     *
     */
    public function showAction(MenuAdministrador $menuAdministrador)
    {
        $deleteForm = $this->createDeleteForm($menuAdministrador);

        return $this->render('menuadministrador/show.html.twig', array(
            'menuAdministrador' => $menuAdministrador,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing MenuAdministrador entity.
     *
     */
    public function editAction(Request $request, MenuAdministrador $menuAdministrador)
    {
        $deleteForm = $this->createDeleteForm($menuAdministrador);
        $editForm = $this->createForm('Cpdg\AdministradorBundle\Form\MenuAdministradorType', $menuAdministrador);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($menuAdministrador);
            $em->flush();

            return $this->redirectToRoute('menuadministrador_edit', array('id' => $menuAdministrador->getId()));
        }

        return $this->render('menuadministrador/edit.html.twig', array(
            'menuAdministrador' => $menuAdministrador,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a MenuAdministrador entity.
     *
     */
    public function deleteAction(Request $request, MenuAdministrador $menuAdministrador)
    {
        $form = $this->createDeleteForm($menuAdministrador);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($menuAdministrador);
            $em->flush();
        }

        return $this->redirectToRoute('menuadministrador_index');
    }

    /**
     * Creates a form to delete a MenuAdministrador entity.
     *
     * @param MenuAdministrador $menuAdministrador The MenuAdministrador entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(MenuAdministrador $menuAdministrador)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('menuadministrador_delete', array('id' => $menuAdministrador->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
