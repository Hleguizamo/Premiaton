<?php

namespace Cpdg\AdministradorBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Cpdg\AdministradorBundle\Entity\EventosEmpleados;
use Cpdg\AdministradorBundle\Form\EventosEmpleadosType;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
 
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Cpdg\UsuarioBundle\Controller\GlobalesController;

/**
 * EventosEmpleados controller.
 *
 */
class EventosEmpleadosController extends Controller
{
   public $baseBundle = "CpdgAdministradorBundle";
   public $entityMain = "EventosEmpleados";
   public $titulo = "Eventos Internos";
   public function __construct(){
        //----------------------------------
        $this->campos["etiquetas"][] = "Nombre Evento";
        $this->campos["etiquetas"][] = "Fecha Inicio";
        $this->campos["etiquetas"][] = "Fecha Fin";

        $this->campos["tipos"][] = "text";
        $this->campos["tipos"][] = "date";
        $this->campos["tipos"][] = "date";

        $this->campos["campos"][] = "e.nombre";
        $this->campos["campos"][] = "e.fechaInicio";
        $this->campos["campos"][] = "e.fechaFin";
    }
    /**
     * Lists all entities.
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
        $etiquetas[] = "Nombre";
        $etiquetas[] = "Fecha Inicio";
        $etiquetas[] = "Hora Inicio";
        $etiquetas[] = "Fecha Fin";
        $etiquetas[] = "Hora Fin";
        $etiquetas[] = "Periodicidad";
        $etiquetas[] = "Ganadores";
        $etiquetas[] = "Activo";
        $etiquetas[] = "Imagen";
        $etiquetas[] = "Ganadores";
        $etiquetas[] = "Pantalla Ganadores";        
        $etiquetas[] = "Editar";

        //----------------------------------
        $consulta = $this->getDoctrine()->getRepository($this->baseBundle.":".$this->entityMain)->createQueryBuilder('e');
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
        $newf = new EventosEmpleados();
        $form = $this->createForm("Cpdg\AdministradorBundle\Form\\".$this->entityMain."Type", $newf);        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) { 
            $em = $this->getDoctrine()->getManager();

            $useridObj = $this->get('security.token_storage')->getToken()->getUser('Article', 1);
            $userid=$useridObj->getId();
            $idUsuarioObj = $em->getReference('CpdgAdministradorBundle:Administrador', $userid);                 
            
            $archivo1 = $newf->getImagen();
            if($archivo1 != ""){
                $fileName1 = md5(uniqid()).'.jpg';
                $ArchivoDir = $this->container->getParameter('kernel.root_dir').'/../web/images/eventos/';
                $archivo1->move($ArchivoDir, $fileName1);
                $newf->setImagen($fileName1);
            }else{
                $newf->setImagen("");
            }

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
    public function editAction(Request $request, EventosEmpleados $postvar)
    {
        $editForm = $this->createForm("Cpdg\AdministradorBundle\Form\\".$this->entityMain."Type", $postvar);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $data = $request->request->all();
            $em = $this->getDoctrine()->getManager();

            $archivo1 = $postvar->getImagen();
            if(is_object($archivo1) == 1){
               $fileName1 = md5(uniqid()).'.jpg';
               $ArchivoDir = $this->container->getParameter('kernel.root_dir').'/../web/images/eventos/';
               $archivo1->move($ArchivoDir, $fileName1);
               $postvar->setImagen($fileName1);
            }else{
               $postvar->setImagen($data["imagenvar"]);
            }

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
            'imagenvar' => $postvar->getImagen(),
        ));
    }
    /**
     * Deletes a entity.
     *
     */
    public function deleteAction(Request $request, EventosEmpleados $entityvar)
    {
        $data = $request->request->all();
        $this->addFlash('success', 'Eliminado correctamente');
        $em = $this->getDoctrine()->getManager();
        $em->remove($entityvar);
        $em->flush();        
        return $this->redirectToRoute(strtolower($this->entityMain).'_index');
    }

    public function cargarGanadoresAction(Request $request, $id)
    {
        if($request->isXmlHttpRequest())
        {
            $em = $this->getDoctrine()->getManager();
            $data = $request->request->all();
            $encoders = array(new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());
             $x = 0;
            $ganadores = Array();

            $eventosGanadores = $this->getDoctrine()->getRepository('CpdgAdministradorBundle:EventosEmpleadosGanadores')
                        ->createQueryBuilder('e')
                        ->where('e.idEventosEmpleados = :idEventosEmpleados')->setParameter('idEventosEmpleados', $data["idEvento"])
                        ->orderBy('e.id','asc')
                        ->getQuery()->execute();
           

            foreach ($eventosGanadores as $eventosInscripcione) {
               $ganadores[$x]["id"] = $eventosInscripcione->getId();
               $ganadores[$x]["centro"] = $eventosInscripcione->getIdEmpleados()->getIdCentro()->getCentro();
               $ganadores[$x]["codigo"] = $eventosInscripcione->getIdEmpleados()->getCodigo();
               $ganadores[$x]["nombre"] = $eventosInscripcione->getIdEmpleados()->getNombre();
               $ganadores[$x]["cedula"] = $eventosInscripcione->getIdEmpleados()->getCedula();
               $ganadores[$x]["direccion"] = $eventosInscripcione->getIdEmpleados()->getDireccion();
               $ganadores[$x]["departamento"] = $eventosInscripcione->getIdEmpleados()->getCiudad();
               $ganadores[$x]["ciudad"] = $eventosInscripcione->getIdEmpleados()->getDepartamento();
               $ganadores[$x]["telefono"] = $eventosInscripcione->getIdEmpleados()->getTelefono();

               $ganadores[$x]["fecha"] = $eventosInscripcione->getIdSorteo()->getFechaCierre()->format('Y-m-d');
               $ganadores[$x]["hora"] = $eventosInscripcione->getIdSorteo()->getFechaCierre()->format('H:i:s');;
               $x++;
            }
 
            $serializer = new Serializer($normalizers, $encoders);
            
            $response = new JsonResponse();
            $response->setStatusCode(200);
            $response->setData(array(
                'response' => 'success',
                'ganadores' => $ganadores,
                'total' => $x,
            ));
            return $response;
        }
    }
    //--------------------------

    public function pantallaGanadoresAction(Request $request, $id)
    {
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
        $completeSearch = "false";         
        $cantidadPorPagina = 20;
        $startPagination = ($page - 1) * $cantidadPorPagina;
        //----------------------------------
        $etiquetas[] = "#";
        $etiquetas[] = "Nombre";
        $etiquetas[] = "Pantalla Ganadores";        

        //----------------------------------
        $consulta = $this->getDoctrine()->getRepository($this->baseBundle.":".$this->entityMain)->createQueryBuilder('e')
        ->where('e.id = :id')->setParameter('id', $id);
        //----------------------------------
        $paginator  = $this->get('knp_paginator');
        $resultset = $paginator->paginate(
            $consulta,
            $page,$cantidadPorPagina
        );
         $proveedores = $this->getDoctrine()->getRepository($this->baseBundle.":Proveedores")->createQueryBuilder('e')->orderBy("e.nombre", "asc")->getQuery()->execute();
        return $this->render('CpdgAdministradorBundle:Templates:pantallaGanadoresbaseEmpleados.html.twig', array(
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
