<?php

namespace Cpdg\AdministradorBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Cpdg\AdministradorBundle\Entity\Logs;
use Cpdg\AdministradorBundle\Form\LogsType;

/**
 * Logs controller.
 *
 */
class LogsController extends Controller
{
    public $baseBundle = "CpdgAdministradorBundle";
   public $entityMain = "Logs";
   public $titulo = "Registro";

   public function __construct(){
        //----------------------------------
        $this->campos["etiquetas"][] = "Usuario";
        $this->campos["etiquetas"][] = "Accion";
        $this->campos["etiquetas"][] = "Fecha";

        $this->campos["tipos"][] = "text";
        $this->campos["tipos"][] = "text";
        $this->campos["tipos"][] = "date";

        $this->campos["campos"][] = "e.usuario";
        $this->campos["campos"][] = "e.accion";
        $this->campos["campos"][] = "e.fecha";
    }
    /**
     * Lists all entities.
     *
     */
   public function indexAction(Request $request)
    {
        //-------------------------------------

        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $page =  $request->query->get('page');
        if(!isset($page)){ $page = 1; }
        //----------------------------------
        $edit = "false";
        $delete = "false";
        $new = "false";
        $excel = "false";
        $pdf = "false";
        $pdfone = "false";
        $advancedsearch = "false"; 
        $completeSearch = "true";       
        $cantidadPorPagina = 20;
        $startPagination = ($page - 1) * $cantidadPorPagina;
        //----------------------------------
        $etiquetas[] = "#";
        $etiquetas[] = "Tipo Usuario";
        $etiquetas[] = "Usuario";
        $etiquetas[] = "Accion";
        $etiquetas[] = "Ip";
        $etiquetas[] = "Fecha";
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
        $consulta->orderBy("e.fecha", "desc");
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

        $newf = new Logs();
        $form = $this->createForm("Cpdg\AdministradorBundle\Form\\".$this->entityMain."Type", $newf);        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {                    
            $em = $this->getDoctrine()->getManager();
            $newf->setPublic(1);
            $newf->setSuperadmin(1);

            $idcentro = $em->getReference('CpdgAdministradorBundle:Centros', 1); 
            $newf->setIdCentro($idcentro); 

            $em->persist($newf);
            $em->flush();
            $this->addFlash('success', 'Registro creado correctamente');
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
    public function editAction(Request $request, Logs $postvar)
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
    /**
     * Deletes a entity.
     *
     */
    public function deleteAction(Request $request, Logs $entityvar)
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
