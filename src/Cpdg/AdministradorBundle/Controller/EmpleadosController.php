<?php

namespace Cpdg\AdministradorBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Cpdg\AdministradorBundle\Entity\Empleados;
use Cpdg\AdministradorBundle\Form\EmpleadosType;

/**
 * Empleados controller.
 *
 */
class EmpleadosController extends Controller
{
   public $baseBundle = "CpdgAdministradorBundle";
   public $entityMain = "Empleados";
   public $titulo = "Empleados";
   public function __construct(){
        //----------------------------------
        $this->campos["etiquetas"][] = "Centro";
        $this->campos["etiquetas"][] = "Código";
        $this->campos["etiquetas"][] = "Nombre";
        $this->campos["etiquetas"][] = "Cédula";
        $this->campos["etiquetas"][] = "Dirección";
        $this->campos["etiquetas"][] = "Departamento";
        $this->campos["etiquetas"][] = "Ciudad";
        $this->campos["etiquetas"][] = "Teléfono";

        $this->campos["tipos"][] = "text";
        $this->campos["tipos"][] = "text";
        $this->campos["tipos"][] = "text";
        $this->campos["tipos"][] = "text";
        $this->campos["tipos"][] = "text";
        $this->campos["tipos"][] = "text";
        $this->campos["tipos"][] = "text";
        $this->campos["tipos"][] = "text";

        $this->campos["campos"][] = "c.centro";
        $this->campos["campos"][] = "e.codigo";
        $this->campos["campos"][] = "e.nombre";
        $this->campos["campos"][] = "e.cedula";
        $this->campos["campos"][] = "e.direccion";
        $this->campos["campos"][] = "e.departamento";
        $this->campos["campos"][] = "e.ciudad";
        $this->campos["campos"][] = "e.telefono";
    }
    /**
     * Lists all Empleados entities.
     *
     */
    public function indexAction(Request $request)
    {
        $useridObj = $this->get('security.token_storage')->getToken()->getUser('Article', 1);
        $usertype=$useridObj->getSuperadmin();
        $usercentro=$useridObj->getIdCentro();
        if($usertype != "1"){
           // return $this->redirectToRoute('administrador_inicio');
        }

        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $page =  $request->query->get('page');
        if(!isset($page)){ $page = 1; }
        //----------------------------------
        $edit = "true";
        $delete = "false";
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
        $etiquetas[] = "Nit Empleado";
        $etiquetas[] = "Nombre Completo";
        $etiquetas[] = "Cédula";
        $etiquetas[] = "Dirección";
        $etiquetas[] = "Teléfono";
        $etiquetas[] = "Ciudad";
        $etiquetas[] = "Departamento";
        $etiquetas[] = "¿Activo?";      
        $etiquetas[] = "Editar";

        //----------------------------------
        $consulta = $this->getDoctrine()->getRepository($this->baseBundle.":".$this->entityMain)->createQueryBuilder('e')
        ->leftJoin('CpdgAdministradorBundle:Centros', 'c', 'WITH', 'c.id = e.idCentro');
        if($usertype != "1"){
            $consulta->orWhere('e.idCentro = :idCentro')->setParameter('idCentro', $usercentro);
        }
        //--Search-------------------
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

        //----------------------------------
        $paginator  = $this->get('knp_paginator');
        $resultset = $paginator->paginate(
            $consulta,
            $page,$cantidadPorPagina
        );
         $proveedores = $this->getDoctrine()->getRepository($this->baseBundle.":Proveedores")->createQueryBuilder('e')->orderBy("e.nombre", "asc")->getQuery()->execute();
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
            'proveedores' => $proveedores,          
        ));
    }
    /**
     * Creates a new entity.
     *
     */
    public function newAction(Request $request)
        {
        $newf = new Empleados();
        $form = $this->createForm("Cpdg\AdministradorBundle\Form\\".$this->entityMain."Type", $newf);        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) { 
            $em = $this->getDoctrine()->getManager();

            $useridObj = $this->get('security.token_storage')->getToken()->getUser('Article', 1);
            $userid=$useridObj->getId();
            $idUsuarioObj = $em->getReference('CpdgAdministradorBundle:Administrador', $userid);                 

            $newf->setIdAdministrador($idUsuarioObj);   
            $newf->setFechaCreacion(new \DateTime(date("Y-m-d H:i:s")));            
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
    public function editAction(Request $request, Empleados $postvar)
    {
        $editForm = $this->createForm("Cpdg\AdministradorBundle\Form\\".$this->entityMain."Type", $postvar);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $data = $request->request->all();
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
    public function deleteAction(Request $request, Empleados $entityvar)
    {
        $data = $request->request->all();
        $this->addFlash('success', 'Eliminado correctamente');
        $em = $this->getDoctrine()->getManager();
        $em->remove($entityvar);
        $em->flush();        
        return $this->redirectToRoute(strtolower($this->entityMain).'_index');
    }
}
