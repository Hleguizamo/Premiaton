<?php

namespace Cpdg\UsuarioBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Cpdg\UsuarioBundle\Entity\ProveedoresUsuarios;
use Cpdg\UsuarioBundle\Form\ProveedoresUsuariosType;

/**
 * ProveedoresUsuarios controller.
 *
 */
class ProveedoresUsuariosController extends Controller
{
   public $baseBundle = "CpdgUsuarioBundle";
   public $entityMain = "ProveedoresUsuarios";
   public $titulo = "Usuarios Proveedores";
   public $campos;

   public function __construct(){
        //----------------------------------
        $this->campos["etiquetas"][] = "Nombre";
        $this->campos["etiquetas"][] = "Teléfono";
        $this->campos["etiquetas"][] = "Usuario / Email";

        $this->campos["tipos"][] = "text";
        $this->campos["tipos"][] = "text";
        $this->campos["tipos"][] = "text";

        $this->campos["campos"][] = "e.nombre";
        $this->campos["campos"][] = "e.telefono";
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
        $usertype=$useridObj->getSuperUsuario();
        if($usertype != 1){
            return $this->redirectToRoute('usuario_inicio');
        }
        //-------------------------------------
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $userid=$useridObj->getIdProveedor()->getId();
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
        $etiquetas[] = "Nombre";
        $etiquetas[] = "telefono";
        $etiquetas[] = "Email";
        $etiquetas[] = "Contrasena";
        

        $etiquetas[] = "Editar";
        
        
        //----------------------------------
        $consulta = $this->getDoctrine()->getRepository($this->baseBundle.":".$this->entityMain)->createQueryBuilder('e')
        ->andWhere('e.idProveedor = :idProveedor')->setParameter('idProveedor', $userid);
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

        return $this->render('CpdgUsuarioBundle:Templates:indexbase.html.twig', array(
            'titulo' => $this->titulo,
            'namevar' => strtolower($this->entityMain)."usr",
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
        $usertype=$useridObj->getSuperUsuario();
        if($usertype != 1){
            return $this->redirectToRoute('usuario_inicio');
        }
        //-------------------------------------

        $newf = new ProveedoresUsuarios();
        $userid=$useridObj->getIdProveedor()->getId();
        $form = $this->createForm("Cpdg\UsuarioBundle\Form\\".$this->entityMain."Type", $newf);        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {                    
            $data = $request->request->all();
            $em = $this->getDoctrine()->getManager();

            $idProveedorObj = $em->getReference('CpdgUsuarioBundle:Proveedores', $userid);
            $newf->setIdProveedor($idProveedorObj);
            $newf->setFechaActualizacionContrasena(new \DateTime(date("Y-m-d")));
            $newf->setSuperUsuario(0);
            $newf->setPublic(1);
            $newf->setFechaActualizacionContrasena(new \DateTime(date("Y-m-d H:i:s")));
            $newf->setProgramarActualizacionContrasena(0);
            $em->persist($newf);
            $em->flush();
            $this->addFlash('success', 'Usuario creado correctamente');
            return $this->redirectToRoute(strtolower($this->entityMain).'usr_index');
        }
        return $this->render('CpdgUsuarioBundle:Templates:newbase.html.twig', array(
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
    public function editAction(Request $request, ProveedoresUsuarios $postvar)
    {
        //-------------------------------------
        $useridObj = $this->get('security.token_storage')->getToken()->getUser('Article', 1);
        $usertype=$useridObj->getSuperUsuario();
        $userid=$useridObj->getId();

        if($usertype != 1){
            return $this->redirectToRoute('usuario_inicio');
        }

        $consulta = $this->getDoctrine()->getRepository($this->baseBundle.":".$this->entityMain)->createQueryBuilder('e')
        ->where('e.idProveedor = :idProveedor')->setParameter('idProveedor', $userid)
        ->andWhere('e.id = :id')->setParameter('id', $postvar->getId())
        ->getQuery()
        ->execute();
        $x = 0;
        foreach ($consulta as $value) {
            $x++;
        }
        //$this->addFlash('success', $x);
        if($x == 0){
            return $this->redirectToRoute('usuario_inicio');
        }
        //-------------------------------------
        $editForm = $this->createForm("Cpdg\UsuarioBundle\Form\\".$this->entityMain."Type", $postvar);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $data = $request->request->all();
            $em = $this->getDoctrine()->getManager();
            $postvar->setFechaActualizacionContrasena(new \DateTime(date("Y-m-d H:i:s")));
            $postvar->setProgramarActualizacionContrasena(0);
            $em->persist($postvar);
            $em->flush();
        $this->addFlash('success', 'Registro actualizado correctamente');
            return $this->redirectToRoute(strtolower($this->entityMain).'usr_index');
        }

        return $this->render('CpdgUsuarioBundle:Templates:editbase.html.twig', array(
            'postvar' => $postvar,
            'edit_form' => $editForm->createView(),
            'namevar' => strtolower($this->entityMain),
            'titulo' => $this->titulo,
        ));
    }
    public function updateContrasenaAction(Request $request, ProveedoresUsuarios $postvar)
    {
        //-------------------------------------
        $useridObj = $this->get('security.token_storage')->getToken()->getUser('Article', 1);
        $usertype=$useridObj->getSuperUsuario();
        $userid=$useridObj->getId();

        $consulta = $this->getDoctrine()->getRepository($this->baseBundle.":".$this->entityMain)->createQueryBuilder('e')
        ->where('e.idProveedor = :idProveedor')->setParameter('idProveedor', $userid)
        ->andWhere('e.id = :id')->setParameter('id', $postvar->getId())
        ->getQuery()
        ->execute();
        $x = 0;
        foreach ($consulta as $value) {
            $x++;
        }
        //$this->addFlash('success', $x);
        if($x == 0){
            return $this->redirectToRoute('usuario_inicio');
        }
        //-------------------------------------
        $editForm = $this->createForm("Cpdg\UsuarioBundle\Form\\".$this->entityMain."contrasenaType", $postvar);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $data = $request->request->all();
            $em = $this->getDoctrine()->getManager();

            $postvar->setFechaActualizacionContrasena(new \DateTime(date("Y-m-d H:i:s")));
            $postvar->setProgramarActualizacionContrasena(0);
            $em->persist($postvar);
            $em->flush();
        $this->addFlash('success', 'Contraseña actualizada correctamente');
            return $this->redirectToRoute('usuario_inicio');
        }

        return $this->render('CpdgUsuarioBundle:Templates:editbase.html.twig', array(
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
    public function deleteAction(Request $request, ProveedoresUsuarios $entityvar)
    {
        //-------------------------------------
        $useridObj = $this->get('security.token_storage')->getToken()->getUser('Article', 1);
        $usertype=$useridObj->getSuperUsuario();
        $userid=$useridObj->getId();

        if($usertype != 1){
            return $this->redirectToRoute('usuario_inicio');
        }

        $consulta = $this->getDoctrine()->getRepository($this->baseBundle.":".$this->entityMain)->createQueryBuilder('e')
        ->where('e.idProveedor = :idProveedor')->setParameter('idProveedor', $userid)
        ->andWhere('e.id = :id')->setParameter('id', $entityvar->getId())
        ->getQUery()
        ->execute();
        $x = 0;
        foreach ($consulta as $value) {
            $x++;
        }
        //$this->addFlash('success', $x);
        if($x == 0){
            return $this->redirectToRoute('usuario_inicio');
        }
        //-------------------------------------
        
        $data = $request->request->all();
        $this->addFlash('success', 'Eliminado correctamente');
        $em = $this->getDoctrine()->getManager();
        $em->remove($entityvar);
        $em->flush();        
        return $this->redirectToRoute(strtolower($this->entityMain).'usr_index');
    }
    

    public function processLogAction($tipoUsuario, $usuario, $mensaje)
    {
        $em = $this->getDoctrine()->getManager();
        $log = new Logs();
        $log->setTipoUsuario($tipoUsuario);
        $log->setUsuario($usuario);
        $log->setAccion($mensaje);
        $log->setIp($_SERVER['REMOTE_ADDR']);
        $log->setFecha(new \DateTime(date("Y-m-d H:i:s")));
        $em->persist($log);
        $em->flush();
    }
}
