<?php

namespace Cpdg\AdministradorBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Cpdg\AdministradorBundle\Entity\MenuAdministradorPermisos;
use Cpdg\AdministradorBundle\Form\MenuAdministradorPermisosType;

/**
 * MenuAdministradorPermisos controller.
 *
 */
class MenuAdministradorPermisosController extends Controller
{
    /**
     * Lists all MenuAdministradorPermisos entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $menuAdministradorPermisos = $em->getRepository('CpdgAdministradorBundle:MenuAdministradorPermisos')->findAll();

        return $this->render('menuadministradorpermisos/index.html.twig', array(
            'menuAdministradorPermisos' => $menuAdministradorPermisos,
        ));
    }

    /**
     * Creates a new MenuAdministradorPermisos entity.
     *
     */
    public function newAction(Request $request)
    {
        $menuAdministradorPermiso = new MenuAdministradorPermisos();
        $form = $this->createForm('Cpdg\AdministradorBundle\Form\MenuAdministradorPermisosType', $menuAdministradorPermiso);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($menuAdministradorPermiso);
            $em->flush();

            return $this->redirectToRoute('menuadministradorpermisos_show', array('id' => $menuAdministradorPermiso->getId()));
        }

        return $this->render('menuadministradorpermisos/new.html.twig', array(
            'menuAdministradorPermiso' => $menuAdministradorPermiso,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a MenuAdministradorPermisos entity.
     *
     */
    public function showAction(MenuAdministradorPermisos $menuAdministradorPermiso)
    {
        $deleteForm = $this->createDeleteForm($menuAdministradorPermiso);

        return $this->render('menuadministradorpermisos/show.html.twig', array(
            'menuAdministradorPermiso' => $menuAdministradorPermiso,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing MenuAdministradorPermisos entity.
     *
     */
    public function editAction(Request $request, MenuAdministradorPermisos $menuAdministradorPermiso)
    {
        $deleteForm = $this->createDeleteForm($menuAdministradorPermiso);
        $editForm = $this->createForm('Cpdg\AdministradorBundle\Form\MenuAdministradorPermisosType', $menuAdministradorPermiso);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($menuAdministradorPermiso);
            $em->flush();

            return $this->redirectToRoute('menuadministradorpermisos_edit', array('id' => $menuAdministradorPermiso->getId()));
        }

        return $this->render('menuadministradorpermisos/edit.html.twig', array(
            'menuAdministradorPermiso' => $menuAdministradorPermiso,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a MenuAdministradorPermisos entity.
     *
     */
    public function deleteAction(Request $request, MenuAdministradorPermisos $menuAdministradorPermiso)
    {
        $form = $this->createDeleteForm($menuAdministradorPermiso);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($menuAdministradorPermiso);
            $em->flush();
        }

        return $this->redirectToRoute('menuadministradorpermisos_index');
    }

    /**
     * Creates a form to delete a MenuAdministradorPermisos entity.
     *
     * @param MenuAdministradorPermisos $menuAdministradorPermiso The MenuAdministradorPermisos entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(MenuAdministradorPermisos $menuAdministradorPermiso)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('menuadministradorpermisos_delete', array('id' => $menuAdministradorPermiso->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
