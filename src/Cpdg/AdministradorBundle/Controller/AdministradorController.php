<?php

namespace Cpdg\AdministradorBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Cpdg\AdministradorBundle\Entity\Administrador;
use Cpdg\AdministradorBundle\Form\AdministradorType;

/**
 * Administrador controller.
 *
 */
class AdministradorController extends Controller
{
   public $baseBundle = "CpdgAdministradorBundle";
   public $entityMain = "Administrador";
   public $titulo = "Administradores";

   public function __construct(){
        //----------------------------------
        $this->campos["etiquetas"][] = "Contacto";
        $this->campos["etiquetas"][] = "Usuario";
        $this->campos["etiquetas"][] = "Email";

        $this->campos["tipos"][] = "text";
        $this->campos["tipos"][] = "text";
        $this->campos["tipos"][] = "text";

        $this->campos["campos"][] = "e.nombre";
        $this->campos["campos"][] = "e.usuario";
        $this->campos["campos"][] = "e.email";
    }
    /**
     * Lists all entities.
     *
     */
   public function indexAction(Request $request)
    {
        //-------------------------------------
        $useridObj = $this->get('security.token_storage')->getToken()->getUser('Article', 1);
        $usertype=$useridObj->getSuperadmin();
        if($usertype != "1"){
            return $this->redirectToRoute('administrador_inicio');
        }
        //-------------------------------------

        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $page =  $request->query->get('page');
        if(!isset($page)){ $page = 1; }
        //----------------------------------
        $edit = "true";
        $delete = "true";
        $new = "true";
        $excel = "false";
        $pdf = "false";
        $pdfone = "false";
        $advancedsearch = "false"; 
        $completeSearch = "true";       
        $cantidadPorPagina = 20;
        $startPagination = ($page - 1) * $cantidadPorPagina;
        //----------------------------------
        $etiquetas[] = "#";
        $etiquetas[] = "Centro";
        $etiquetas[] = "Contacto";
        $etiquetas[] = "Teléfono";
        $etiquetas[] = "Usuario";
        $etiquetas[] = "Contraseña";
        $etiquetas[] = "Email";

        $etiquetas[] = "Editar";
        $etiquetas[] = "Eliminar";
        //----------------------------------
        $consulta = $this->getDoctrine()->getRepository($this->baseBundle.":".$this->entityMain)->createQueryBuilder('e');

        if(isset($data["completeSearchCounter"])){
            $x = 0;
            for($y = 1; $y <= intval($data["completeSearchCounter"]); $y ++){
                if(isset($data["findField_".$y])){
                    $campoexp = explode(".", $data["fieldType_".$y]);
                    
                    if($data["fieldQueryAux_".$y] == "CONTIENE"){
                        $consulta->orWhere($data["fieldType_".$y].' LIKE :'.$campoexp[1].$y)->setParameter($campoexp[1].$y, '%'.$data["findField_".$y].'%');
                    }elseif($data["fieldQueryAux_".$y] == "ESIGUAL"){
                        $consulta->orWhere($data["fieldType_".$y].' LIKE :'.$campoexp[1].$y)->setParameter($campoexp[1].$y, $data["findField_".$y]);
                    }elseif($data["fieldQueryAux_".$y] == "NOESIGUAL"){
                        $consulta->orWhere($data["fieldType_".$y].' != :'.$campoexp[1].$y)->setParameter($campoexp[1].$y, $data["findField_".$y]);
                    }elseif($data["fieldQueryAux_".$y] == "MENORQUE"){
                        $consulta->orWhere($data["fieldType_".$y].' < :'.$campoexp[1].$y)->setParameter($campoexp[1].$y, $data["findField_".$y]);
                    }elseif($data["fieldQueryAux_".$y] == "MAYORQUE"){
                        $consulta->orWhere($data["fieldType_".$y].' > :'.$campoexp[1].$y)->setParameter($campoexp[1].$y, $data["findField_".$y]);
                    }
                    
                }
                $x++;
            }

            $cantidadPorPagina = 100;
        }
        $consulta->andWhere('e.superadmin = :superadmin')->setParameter('superadmin', '1');
        //----------------------------------
        $paginator  = $this->get('knp_paginator');
        $resultset = $paginator->paginate(
            $consulta,
            $page,$cantidadPorPagina
        );

        return $this->render('CpdgAdministradorBundle:Templates:indexbase.html.twig', array(
            'titulo' => $this->titulo,
            'namevar' => strtolower($this->entityMain),
            'bundlevar' => $this->baseBundle.":".strtolower($this->entityMain),
            'resultset' => $resultset,
            'edit' => $edit,
            'delete' => $delete,
            'new' => $new,
            'etiquetas' => $etiquetas,            
            'excel' => $excel,
            'pdf' => $pdf,
            'pdfone' => $pdfone,
            'advancedsearch' => $advancedsearch,            
            'startPagination' => $startPagination,  
            'completeSearch' => $completeSearch,
            'completeSearchFields' => $this->campos,          
        ));
    }
    /**
     * Creates a new entity.
     *
     */
    public function newAction(Request $request)
        {

        //-------------------------------------
        $useridObj = $this->get('security.token_storage')->getToken()->getUser('Article', 1);
        $usertype=$useridObj->getSuperadmin();
        if($usertype != "1"){
            return $this->redirectToRoute('administrador_inicio');
        }
        //-------------------------------------

        $newf = new Administrador();
        $form = $this->createForm("Cpdg\AdministradorBundle\Form\\".$this->entityMain."Type", $newf);        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {                    
            $em = $this->getDoctrine()->getManager();
            $newf->setPublic(1);
            $newf->setSuperadmin(1);

            $newf->setFechaActualizacionContrasena(new \DateTime(date("Y-m-d H:i:s")));
            $newf->setProgramarActualizacionContrasena(0);

            $em->persist($newf);
            $em->flush();
            $this->addFlash('success', 'Administrador creado correctamente');
            return $this->redirectToRoute(strtolower($this->entityMain).'_index');
        }
        return $this->render('CpdgAdministradorBundle:Templates:newbase.html.twig', array(
            'newf' => $newf,
            'namevar' => strtolower($this->entityMain),
            'titulo' => $this->titulo,
            'form' => $form->createView(),
        ));
    }
    /**
     * Displays a form to edit an existing entity.
     *
     */
    public function editAction(Request $request, Administrador $postvar)
    {
        //-------------------------------------
        $useridObj = $this->get('security.token_storage')->getToken()->getUser('Article', 1);
        $usertype=$useridObj->getSuperadmin();
        if($usertype != "1"){
            return $this->redirectToRoute('administrador_inicio');
        }
        //-------------------------------------

        $editForm = $this->createForm("Cpdg\AdministradorBundle\Form\\".$this->entityMain."Type", $postvar);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($postvar);
            $em->flush();
        $this->addFlash('success', 'Registro actualizado correctamente');
            return $this->redirectToRoute(strtolower($this->entityMain).'_index');
        }

        return $this->render('CpdgAdministradorBundle:Templates:editbase.html.twig', array(
            'postvar' => $postvar,
            'edit_form' => $editForm->createView(),
            'namevar' => strtolower($this->entityMain),
            'titulo' => $this->titulo,
        ));
    }

    public function updateContrasenaAction(Request $request, Administrador $postvar)
    {
        //-------------------------------------
        $useridObj = $this->get('security.token_storage')->getToken()->getUser('Article', 1);
        $usertype=$useridObj->getSuperadmin();
        if($usertype != "1"){
            return $this->redirectToRoute('administrador_inicio');
        }
        //-------------------------------------

        $editForm = $this->createForm("Cpdg\AdministradorBundle\Form\\".$this->entityMain."contrasenaType", $postvar);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $postvar->setFechaActualizacionContrasena(new \DateTime(date("Y-m-d H:i:s")));
            $postvar->setProgramarActualizacionContrasena(0);
            $em->persist($postvar);
            $em->flush();
            $this->addFlash('success', 'Contraseña actualizada correctamente');
            return $this->redirectToRoute('administrador_inicio');
        }

        return $this->render('CpdgAdministradorBundle:Templates:editbase.html.twig', array(
            'postvar' => $postvar,
            'edit_form' => $editForm->createView(),
            'namevar' => strtolower($this->entityMain),
            'titulo' => 'Contraseña',
        ));
    }
    /**
     * Deletes a entity.
     *
     */
    public function deleteAction(Request $request, Administrador $entityvar)
    {
        //-------------------------------------
        $useridObj = $this->get('security.token_storage')->getToken()->getUser('Article', 1);
        $usertype=$useridObj->getSuperadmin();
        if($usertype != "1"){
            return $this->redirectToRoute('administrador_inicio');
        }
        //-------------------------------------

        $data = $request->request->all();
        $this->addFlash('success', 'Eliminado correctamente');
        $em = $this->getDoctrine()->getManager();
        $em->remove($entityvar);
        $em->flush();        
        return $this->redirectToRoute(strtolower($this->entityMain).'_index');
    }
}
