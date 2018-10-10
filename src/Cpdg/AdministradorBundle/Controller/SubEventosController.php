<?php

namespace Cpdg\AdministradorBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Cpdg\AdministradorBundle\Entity\SubEventos;
use Cpdg\AdministradorBundle\Entity\SubEventosProveedores;
use Cpdg\AdministradorBundle\Form\SubEventosType;
use Cpdg\UsuarioBundle\Entity\Logs;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
 
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Cpdg\UsuarioBundle\Controller\GlobalesController;

/**
 * SubEventos controller.
 *
 */
class SubEventosController extends Controller
{
   public $baseBundle = "CpdgAdministradorBundle";
   public $entityMain = "SubEventos";
   public $titulo = "Sub Eventos";
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
   public function indexAction(Request $request, $id)
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
        $etiquetas[] = "Evento";
        $etiquetas[] = "Nombre Sub Evento";
        $etiquetas[] = "Fecha Inicio";
        $etiquetas[] = "Hora Inicio";
        $etiquetas[] = "Fecha Fin";
        $etiquetas[] = "Hora Fin";
        $etiquetas[] = "Periodicidad";
        $etiquetas[] = "Ganadores";
        $etiquetas[] = "Estado";
        $etiquetas[] = "Imagen";
        $etiquetas[] = "Proveedores"; 
        $etiquetas[] = "Inscritos";
        $etiquetas[] = "Ganadores";
        $etiquetas[] = "Pantalla Ganadores";        
        $etiquetas[] = "Reporte";
        $etiquetas[] = "Editar";

        //----------------------------------
        $consulta = $this->getDoctrine()->getRepository($this->baseBundle.":".$this->entityMain)->createQueryBuilder('e')
        ->where('e.idEvento = :idEvento')->setParameter('idEvento', $id)
        ;
        /*if($usertype != "1"){
            $consulta->orWhere('e.idCentro = :idCentro')->setParameter('idCentro', $usercentro);
        }*/
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
         $proveedores = $this->getDoctrine()->getRepository($this->baseBundle.":EventosProveedores")->createQueryBuilder('e')
            ->select("p.nombre, p.id")
            ->leftJoin('CpdgAdministradorBundle:Proveedores', 'p', 'WITH', 'p.id = e.idProveedor')
            ->where('e.idEvento = :idEvento')->setParameter('idEvento', $id)
            ->orderBy("p.nombre", "asc")
            ->getQuery()->execute();
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
        $newf = new SubEventos();
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
            return $this->redirectToRoute('subeventos_index', array('id' => $newf->getIdEvento()->getId()));
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
    public function editAction(Request $request, SubEventos $postvar)
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
            return $this->redirectToRoute('subeventos_index', array('id' => $postvar->getIdEvento()->getId()));
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
    public function deleteAction(Request $request, SubEventos $entityvar)
    {
        $data = $request->request->all();
        $this->addFlash('success', 'Eliminado correctamente');
        $em = $this->getDoctrine()->getManager();
        $em->remove($entityvar);
        $em->flush();        
        return $this->redirectToRoute(strtolower($this->entityMain).'_index');
    }

    public function cargarProveedoresAction(Request $request, $id)
    {
        if($request->isXmlHttpRequest())
        {
            $em = $this->getDoctrine()->getManager();
            $data = $request->request->all();
            $encoders = array(new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());
             $x = 0;
            $proveedores = Array();

            $eventosProveedores = $this->getDoctrine()->getRepository('CpdgAdministradorBundle:SubEventosProveedores')
                        ->createQueryBuilder('e')
                        ->where('e.idSubEvento = :idSubEvento')->setParameter('idSubEvento', $data["idEvento"])
                        ->getQuery()->execute();           

            foreach ($eventosProveedores as $eventosProveedore) {
               $proveedores[$x]["id"] = $eventosProveedore->getId();
               $proveedores[$x]["idProveedor"] = $eventosProveedore->getIdProveedor()->getId();
               $x++;
            }
 
            $serializer = new Serializer($normalizers, $encoders);
            
            $response = new JsonResponse();
            $response->setStatusCode(200);
            $response->setData(array(
                'response' => 'success',
                'proveedores' => $proveedores,
                'total' => $x,
            ));
            return $response;
        }
    }

    public function setProveedorAction(Request $request, $id)
    {
        if($request->isXmlHttpRequest())
        {
            $em = $this->getDoctrine()->getManager();
            $data = $request->request->all();
            $encoders = array(new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());
            
            $serializer = new Serializer($normalizers, $encoders);            
            $response = new JsonResponse();
            $response->setStatusCode(200);

            $evento = $em->getRepository('CpdgAdministradorBundle:SubEventos')->find($data["idEvento"]);


            if($data["valuevar"] == "true"){
                //----------------------------------
                //Verificar Limite de Inscritos
                //----------------------------------
                $verificarInscritos = $this->getDoctrine()->getRepository('CpdgAdministradorBundle:SubEventosProveedores')
                        ->createQueryBuilder('e')
                        ->select('COUNT(e.id)')
                        ->where('e.idSubEvento = :idSubEvento')->setParameter('idSubEvento', $data["idEvento"])
                        ->getQuery()
                        ->getSingleScalarResult();
                //Si esta en el Limite de Inscritos retorna limite
                if($verificarInscritos >= $evento->getNumeroProveedores()){                    
                    $response->setData(array(
                        'response' => 'limite',
                        'total' => $verificarInscritos,
                    ));
                    return $response;
                }
                //----------------------------------
                // Verificar que no esté inscrito
                //----------------------------------
                $verificar = $this->getDoctrine()->getRepository('CpdgAdministradorBundle:SubEventosProveedores')
                        ->createQueryBuilder('e')
                        ->select('COUNT(e.id)')
                        ->where('e.idSubEvento = :idSubEvento')->setParameter('idSubEvento', $data["idEvento"])
                        ->andWhere('e.idProveedor = :idProveedor')->setParameter('idProveedor', $data["idProveedor"])
                        ->getQuery()
                        ->getSingleScalarResult();
                //Si esta Inscrito retorna success
                if($verificar > 0){
                    $response->setData(array(
                        'response' => 'success',
                        'total' => $verificar,
                    ));
                    return $response;
                }else{
                    $idProveedorObj = $em->getReference('CpdgAdministradorBundle:Proveedores', $data["idProveedor"]);
                    $idEventoObj = $em->getReference('CpdgAdministradorBundle:SubEventos', $data["idEvento"]);
                    $eventosProveedores = new SubEventosProveedores();
                    $eventosProveedores->setIdSubEvento($idEventoObj);
                    $eventosProveedores->setIdProveedor($idProveedorObj);
                    $eventosProveedores->setFecha(new \DateTime(date("Y-m-d H:i:s")));            
                    $em->persist($eventosProveedores);
                    $em->flush();

                    $useridObj = $this->get('security.token_storage')->getToken()->getUser('Article', 1);
                    $userid=$useridObj->getId();
                    $usernombre=$useridObj->getNombre();
                    $evento = $em->getRepository('CpdgUsuarioBundle:SubEventos')->find($data["idEvento"]);
                    $this->processLogAction(1,$userid, "Administrador asocia proveedor al subevento, 
                                            Sub Evento: ".$evento->getId()." - ".$evento->getNombre().", 
                                            Administrador: ".$userid." - ".$usernombre.", 
                                            Proveedor: ".$eventosProveedores->getIdProveedor()->getId()." - ".$eventosProveedores->getIdProveedor()->getNombre());

                    $response->setData(array(
                        'response' => 'success',
                        'total' => $verificar,
                    ));
                    return $response;
                }
            }else{
                //----------------------------------
                // Verificar que no hallan ganadores
                //----------------------------------
                $verificar = $this->getDoctrine()->getRepository('CpdgAdministradorBundle:SubEventosGanadores')
                        ->createQueryBuilder('e')
                        ->select('COUNT(e.id)')
                        ->where('e.idSubEvento = :idSubEvento')->setParameter('idSubEvento', $data["idEvento"])
                        ->andWhere('e.idProveedor = :idProveedor')->setParameter('idProveedor', $data["idProveedor"])
                        ->getQuery()
                        ->getSingleScalarResult();
                //Si esta Inscrito retorna success
                if($verificar > 0){
                    $response->setData(array(
                        'response' => 'ganador',
                        'total' => $verificar,
                    ));
                    return $response;
                }else{


                    $eventosProveedoresc = $this->getDoctrine()->getRepository('CpdgAdministradorBundle:SubEventosProveedores')
                        ->createQueryBuilder('e')
                        ->where('e.idSubEvento = :idSubEvento')->setParameter('idSubEvento', $data["idEvento"])
                        ->andWhere('e.idProveedor = :idProveedor')->setParameter('idProveedor', $data["idProveedor"])
                        ->getQuery()->execute();
                    foreach ($eventosProveedoresc as $value) {
                        $idDel = $value->getId();
                    }
                    $eventosProveedores = $em->getRepository('CpdgAdministradorBundle:SubEventosProveedores')->find($idDel);

                    $useridObj = $this->get('security.token_storage')->getToken()->getUser('Article', 1);
                    $userid=$useridObj->getId();
                    $usernombre=$useridObj->getNombre();
                    $evento = $em->getRepository('CpdgUsuarioBundle:SubEventos')->find($data["idEvento"]);
                    $this->processLogAction(1,$userid, "Administrador elimina proveedor del subevento, 
                                            Sub Evento: ".$evento->getId()." - ".$evento->getNombre().", 
                                            Administrador: ".$userid." - ".$usernombre.", 
                                            Proveedor: ".$eventosProveedores->getIdProveedor()->getId()." - ".$eventosProveedores->getIdProveedor()->getNombre());

                    $em->remove($eventosProveedores);
                    $em->flush();

                    $response->setData(array(
                        'response' => 'successeliminar',
                        'total' => $verificar,
                    ));
                    return $response;
                }

            }

            $response->setData(array(
                'response' => 'successeliminar',
                'total' => "0",
            ));
            return $response;
        }
    }

    public function cargarInscripcionesAction(Request $request, $id)
    {
        if($request->isXmlHttpRequest())
        {
            $em = $this->getDoctrine()->getManager();
            $data = $request->request->all();
            $encoders = array(new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());
             $x = 0;
            $inscripciones = Array();

            $subEventosProveedores = $this->getDoctrine()->getRepository('CpdgAdministradorBundle:SubEventosProveedores')
                        ->createQueryBuilder('e')
                        ->where('e.idSubEvento = :idSubEvento')->setParameter('idSubEvento', $data["idVar"])
                        ->orderBy('e.id','asc')
                        ->getQuery()->execute();
           
            $proveedores = Array();
            foreach ($subEventosProveedores as $subEventosProveedore){
                $proveedores[] = $subEventosProveedore->getIdProveedor()->getId(); 
            }

            $eventosInscripciones = $this->getDoctrine()->getRepository('CpdgAdministradorBundle:EventosInscripciones')
                        ->createQueryBuilder('e')
                        ->where('e.idEvento = :idEvento')->setParameter('idEvento', $data["idEvento"])
                        ->andWhere('e.idProveedor IN (:idProveedor)')->setParameter('idProveedor', $proveedores)
                        ->orderBy('e.id','asc')
                        ->getQuery()->execute();
           

            foreach ($eventosInscripciones as $eventosInscripcione) {
               $inscripciones[$x]["id"] = $eventosInscripcione->getId();
               $inscripciones[$x]["idProveedor"] = $eventosInscripcione->getIdProveedor();

               $inscripciones[$x]["proveedor"] = $eventosInscripcione->getIdProveedor()->getNombre();
               $inscripciones[$x]["nit"] = $eventosInscripcione->getIdAsociado()->getNit();
               $inscripciones[$x]["asociado"] = $eventosInscripcione->getIdAsociado()->getNombreAsociado();
               $inscripciones[$x]["codigo"] = $eventosInscripcione->getIdAsociado()->getCodigo();
               $inscripciones[$x]["nombreDrogueria"] = $eventosInscripcione->getIdAsociado()->getNombreDrogueria();
               $inscripciones[$x]["fecha"] = $eventosInscripcione->getFecha()->format('Y-m-d');
               $inscripciones[$x]["hora"] = $eventosInscripcione->getHora()->format('H:i:s');;
               $x++;
            }
 
            $serializer = new Serializer($normalizers, $encoders);
            
            $response = new JsonResponse();
            $response->setStatusCode(200);
            $response->setData(array(
                'response' => 'success',
                'inscripciones' => $inscripciones,
                'total' => $x,
            ));
            return $response;
        }
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

            $eventosGanadores = $this->getDoctrine()->getRepository('CpdgAdministradorBundle:SubEventosGanadores')
                        ->createQueryBuilder('e')
                        ->where('e.idSubEvento = :idSubEvento')->setParameter('idSubEvento', $data["idEvento"])
                        ->getQuery()->execute();
           

            foreach ($eventosGanadores as $eventosInscripcione) {
               $ganadores[$x]["id"] = $eventosInscripcione->getId();
               $ganadores[$x]["idProveedor"] = $eventosInscripcione->getIdProveedor()->getId();

               $ganadores[$x]["proveedor"] = $eventosInscripcione->getIdProveedor()->getNombre();
               $ganadores[$x]["nit"] = $eventosInscripcione->getIdAsociado()->getNit();
               $ganadores[$x]["asociado"] = $eventosInscripcione->getIdAsociado()->getNombreAsociado();
               $ganadores[$x]["codigo"] = $eventosInscripcione->getIdAsociado()->getCodigo();
               $ganadores[$x]["nombreDrogueria"] = $eventosInscripcione->getIdAsociado()->getNombreDrogueria();
               $ganadores[$x]["fecha"] = $eventosInscripcione->getFecha()->format('Y-m-d');
               $ganadores[$x]["hora"] = $eventosInscripcione->getHora()->format('H:i:s');;
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
        return $this->render('CpdgAdministradorBundle:Templates:pantallaGanadoresbaseSubEvento.html.twig', array(
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

    //----------------------------------------------------------
    public function generarReporteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager(); 
        $data = $request->request->all();
        $query = $em->getRepository($this->baseBundle.':'.$this->entityMain)
                ->createQueryBuilder('e')
                ->where('e.id = :id')->setParameter('id', $id);
        
        $result = $query->getQuery()->getResult();

        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()
            ->setCreator("Premiatón")
            ->setLastModifiedBy("Premiatón")
            ->setTitle("Premiatón")
            ->setSubject("Premiatón")
            ->setDescription("Premiatón")
            ->setKeywords("Premiatón");

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Premiatón - Reporte Evento: ');
        
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A2', 'Centro')
            ->setCellValue('B2', 'Ciudad')
            ->setCellValue('C2', 'Nombre del Sub Evento')
            ->setCellValue('D2', 'Fecha de creación')
            ->setCellValue('E2', 'Fecha inicio')
            ->setCellValue('F2', 'Fecha Fin')
            ->setCellValue('G2', 'Hora Inicio')
            ->setCellValue('H2', 'Hora Fin')
            ->setCellValue('I2', 'Periodicidad')
            ->setCellValue('J2', 'Numero de ganadores por sorteo')
            ->setCellValue('K2', 'Numero de proveedores')
            ->setCellValue('L2', 'Premio')
            ->setCellValue('M2', 'Valor')
            ->setCellValue('N2', 'Repite Proveedor')
            ->setCellValue('O2', 'Estado')
            ;

        // fijamos un ancho a las distintas columnas
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('A')
            ->setWidth(25);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('B')
            ->setWidth(25);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('C')
            ->setWidth(25);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('D')
            ->setWidth(25);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('E')
            ->setWidth(25);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('F')
            ->setWidth(25);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('G')
            ->setWidth(25);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('H')
            ->setWidth(25);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('I')
            ->setWidth(25);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('J')
            ->setWidth(25);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('K')
            ->setWidth(25);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('L')
            ->setWidth(25);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('M')
            ->setWidth(25);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('N')
            ->setWidth(25);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('O')
            ->setWidth(25);


        // recorremos los registros obtenidos de la consulta a base de datos escribiéndolos en las celdas correspondientes
        $row = 3;
        foreach ($result as $item) {
            $idEvento_ = $item->getIdEvento(); 
            $nombreEvento = $item->getNombre();
            switch($item->getEstado()){
                case "0": $estado = "TERMINADO"; break;
                case "1": $estado = "ACTIVO"; break;
                case "9": $estado = "EN CURSO"; break;
                case "5": $estado = "EN PAUSA"; break;
            }
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$row, $item->getIdEvento()->getIdCentro()->getCentro())
                ->setCellValue('B'.$row, $item->getIdEvento()->getIdCiudad()->getNombre())
                ->setCellValue('C'.$row, $item->getNombre())
                ->setCellValue('D'.$row, $item->getFechaCreacion()->format("Y-m-d H:i:s"))
                ->setCellValue('E'.$row, $item->getFechaInicio()->format("Y-m-d"))
                ->setCellValue('F'.$row, $item->getFechaFin()->format("Y-m-d"))
                ->setCellValue('G'.$row, $item->getHoraInicio()->format("H:i:s"))
                ->setCellValue('H'.$row, $item->getHoraFin()->format("H:i:s"))
                ->setCellValue('I'.$row, $item->getPeriodicidad())
                ->setCellValue('J'.$row, $item->getNumeroGanadores())
                ->setCellValue('K'.$row, $item->getNumeroProveedores())
                ->setCellValue('L'.$row, $item->getPremio())
                ->setCellValue('M'.$row, $item->getValor())
                ->setCellValue('N'.$row, $item->getRepiteProveedor())
                ->setCellValue('O'.$row, $estado)
                ;
            $row++;
        }

        // PROVEEDORES

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A5', 'Proveedores asociados al evento');
        
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A6', 'Nombre')
            ->setCellValue('B6', 'Nit')
            ->setCellValue('C6', 'Representante')
            ->setCellValue('D6', 'Telefono')
            ->setCellValue('E6', 'Email')
            ->setCellValue('F6', 'Fecha y Hora')
            ;

        // fijamos un ancho a las distintas columnas
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('A')
            ->setWidth(40);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('B')
            ->setWidth(25);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('C')
            ->setWidth(25);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('D')
            ->setWidth(25);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('E')
            ->setWidth(25);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('F')
            ->setWidth(25);

        $query2 = $em->getRepository('CpdgAdministradorBundle:SubEventosProveedores')
                ->createQueryBuilder('e')
                ->where('e.idSubEvento = :idSubEvento')->setParameter('idSubEvento', $id)
                ->leftJoin('CpdgAdministradorBundle:Proveedores', 'p', 'WITH', 'p.id = e.idProveedor')
                ;        
        $result2 = $query2->getQuery()->getResult();

        $row ++;
        $row ++;
        $row ++;
        foreach ($result2 as $item2) {
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$row, $item2->getIdProveedor()->getNombre())
                ->setCellValue('B'.$row, $item2->getIdProveedor()->getNit())
                ->setCellValue('C'.$row, $item2->getIdProveedor()->getRepresentante())
                ->setCellValue('D'.$row, $item2->getIdProveedor()->getTelefono())
                ->setCellValue('E'.$row, $item2->getIdProveedor()->getEmail())
                ->setCellValue('F'.$row, $item2->getFecha()->format("Y-m-d H:i:s"))
                ;
            $row++;
        }

        $row++;

        // INSCRITOS
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$row, '');
        $row++;
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$row, 'Asociados inscritos al evento por los proveedores');
        $row++;
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$row, 'Proveedor')
            ->setCellValue('B'.$row, 'Centro')
            ->setCellValue('C'.$row, 'Nombre Asociado')
            ->setCellValue('D'.$row, 'Nombre Drogueria')
            ->setCellValue('E'.$row, 'Codigo')
            ->setCellValue('F'.$row, 'Email')
            ->setCellValue('G'.$row, 'Nit')
            ->setCellValue('H'.$row, 'Ciudad')
            ->setCellValue('I'.$row, 'Departamento')
            ->setCellValue('J'.$row, 'Fecha y Hora')
            ;

        $query3a = $em->getRepository('CpdgAdministradorBundle:SubEventosProveedores')
                ->createQueryBuilder('e')
                ->where('e.idSubEvento = :idSubEvento')->setParameter('idSubEvento', $id)
                ->leftJoin('CpdgAdministradorBundle:Proveedores', 'p', 'WITH', 'p.id = e.idProveedor')
                ;        
        $result3a = $query3a->getQuery()->getResult();

        $row ++;
        $idsProveedores = array();
        foreach ($result3a as $item3a) {
            $idsProveedores[] = $item3a->getIdProveedor();
        }

        $query3 = $em->getRepository('CpdgAdministradorBundle:EventosInscripciones')
                ->createQueryBuilder('e')
                ->where('e.idEvento = :idEvento')->setParameter('idEvento', $idEvento_)
                ->andWhere('e.idProveedor IN (:idProveedor)')->setParameter('idProveedor', $idsProveedores)
                ->leftJoin('CpdgAdministradorBundle:Proveedores', 'p', 'WITH', 'p.id = e.idProveedor')
                ->leftJoin('CpdgAdministradorBundle:Asociados', 'a', 'WITH', 'a.id = e.idAsociado')
                ;        
        $result3 = $query3->getQuery()->getResult();

        $row ++;
        foreach ($result3 as $item3) {
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$row, $item3->getIdProveedor()->getNombre())
                ->setCellValue('B'.$row, $item3->getIdAsociado()->getIdCentro()->getCentro())
                ->setCellValue('C'.$row, $item3->getIdAsociado()->getNombreAsociado())
                ->setCellValue('D'.$row, $item3->getIdAsociado()->getNombreDrogueria())
                ->setCellValue('E'.$row, $item3->getIdAsociado()->getCodigo())
                ->setCellValue('F'.$row, $item3->getIdAsociado()->getEmail())
                ->setCellValue('G'.$row, $item3->getIdAsociado()->getNit())
                ->setCellValue('H'.$row, $item3->getIdAsociado()->getCiudad())
                ->setCellValue('I'.$row, $item3->getIdAsociado()->getDepartamento())
                ->setCellValue('J'.$row, $item3->getFecha()->format('Y-m-d')." ".$item3->getHora()->format('H:i:s'))
                ;
            $row++;
        }


        // GANADORES
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$row, '');
        $row++;
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$row, 'Ganadores del Sub evento');
        $row++;
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$row, 'Proveedor')
            ->setCellValue('B'.$row, 'Centro')
            ->setCellValue('C'.$row, 'Nombre Asociado')
            ->setCellValue('D'.$row, 'Nombre Drogueria')
            ->setCellValue('E'.$row, 'Codigo')
            ->setCellValue('F'.$row, 'Email')
            ->setCellValue('G'.$row, 'Nit')
            ->setCellValue('H'.$row, 'Ciudad')
            ->setCellValue('I'.$row, 'Departamento')
            ->setCellValue('J'.$row, 'Fecha y Hora')
            ;

        // fijamos un ancho a las distintas columnas
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('A')
            ->setWidth(25);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('B')
            ->setWidth(35);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('C')
            ->setWidth(35);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('D')
            ->setWidth(25);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('E')
            ->setWidth(35);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('F')
            ->setWidth(25);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('G')
            ->setWidth(25);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('H')
            ->setWidth(25);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('I')
            ->setWidth(25);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('J')
            ->setWidth(25);



        $query3 = $em->getRepository('CpdgAdministradorBundle:SubEventosGanadores')
                ->createQueryBuilder('e')
                ->where('e.idSubEvento = :idSubEvento')->setParameter('idSubEvento', $id)
                ->leftJoin('CpdgAdministradorBundle:Proveedores', 'p', 'WITH', 'p.id = e.idProveedor')
                ->leftJoin('CpdgAdministradorBundle:Asociados', 'a', 'WITH', 'a.id = e.idAsociado')
                ;        
        $result3 = $query3->getQuery()->getResult();

        $row ++;
        foreach ($result3 as $item3) {
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$row, $item3->getIdProveedor()->getNombre())
                ->setCellValue('B'.$row, $item3->getIdAsociado()->getIdCentro()->getCentro())
                ->setCellValue('C'.$row, $item3->getIdAsociado()->getNombreAsociado())
                ->setCellValue('D'.$row, $item3->getIdAsociado()->getNombreDrogueria())
                ->setCellValue('E'.$row, $item3->getIdAsociado()->getCodigo())
                ->setCellValue('F'.$row, $item3->getIdAsociado()->getEmail())
                ->setCellValue('G'.$row, $item3->getIdAsociado()->getNit())
                ->setCellValue('H'.$row, $item3->getIdAsociado()->getCiudad())
                ->setCellValue('I'.$row, $item3->getIdAsociado()->getDepartamento())
                ->setCellValue('J'.$row, $item3->getFecha()->format('Y-m-d')." ".$item3->getHora()->format('H:i:s'))
                ;
            $row++;
        }


        // se crea el writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // se crea el response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // y por último se añaden las cabeceras
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT,'Reporte_SubEvento'.date("YmdHis").'.xls');
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
    //-----------------------------------------------------------------
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