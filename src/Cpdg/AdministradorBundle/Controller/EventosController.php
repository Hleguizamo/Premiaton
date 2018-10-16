<?php

namespace Cpdg\AdministradorBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Cpdg\AdministradorBundle\Entity\Eventos;
use Cpdg\AdministradorBundle\Form\EventosType;
use Cpdg\AdministradorBundle\Entity\EventosProveedores;
use Cpdg\AdministradorBundle\Entity\EventosGanadoresAleatoriosProveedores;
use Cpdg\AdministradorBundle\Entity\EventosGanadoresAleatoriosAsociados;

use Cpdg\AdministradorBundle\Entity\Logs;
use Cpdg\AdministradorBundle\Entity\Sorteos;

use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Cpdg\AdministradorBundle\Controller\GlobalesController;

/**
 * Eventos controller.
 *
 */
class EventosController extends Controller
{
   public $baseBundle = "CpdgAdministradorBundle";
   public $entityMain = "Eventos";
   public $titulo = "Eventos";
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
        $etiquetas[] = "Máximo de Ganadores";
        $etiquetas[] = "Estado";
        $etiquetas[] = "Imagen";
        $etiquetas[] = "Proveedores";
        $etiquetas[] = "Inscritos";
        $etiquetas[] = "Ganadores";
        $etiquetas[] = "Pantalla Ganadores";
        $etiquetas[] = "Otras Tareas";
        $etiquetas[] = "Sub Eventos";
        $etiquetas[] = "Reporte";
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
        $consulta->orderBy("e.id", "desc");
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
        return $this->render('CpdgAdministradorBundle:Templates:pantallaGanadoresbase.html.twig', array(
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
    //--------------------------
    /**
     * Creates a new entity.
     *
     */
    public function newAction(Request $request)
        {
        $newf = new Eventos();
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
            $newf->setFechaInicio(new \DateTime($newf->getFechaInicio()->format('Y-m-d').' '.$newf->getHoraInicio()->format('H:i:s')));
            $newf->setFechaFin(new \DateTime($newf->getFechaFin()->format('Y-m-d').' '.$newf->getHoraFin()->format('H:i:s')));
            $em->persist($newf);
            $em->flush();

            $newSorteo = new Sorteos();
            $newSorteo->setIdEvento($newf->getId());
            $newSorteo->setTipo(1);
            $newSorteo->setEstado(0);
            $newSorteo->setFechaCreacion(new \DateTime(date("Y-m-d H:i:s")));
            $newSorteo->setFechaCierre(new \DateTime(date("Y-m-d H:i:s")));
            $newSorteo->setResponse("Inicio la inscripción de Asociados");

            $em->persist($newSorteo);
            $em->flush();

            $this->addFlash('success', 'Evento creado correctamente');

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
    public function editAction(Request $request, Eventos $postvar)
    {
        $editForm = $this->createForm("Cpdg\AdministradorBundle\Form\\".$this->entityMain."Type", $postvar);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $data = $request->request->all();
            $em = $this->getDoctrine()->getManager();

            $addProcess = "";
            if($data['idCentro'] != $postvar->getIdCentro()->getId()){
                $addProcess .= "idCentro: ".$data['idCentro']."->".$postvar->getIdCentro()->getCentro()."<br>";
            }
            if($data['nombre'] != $postvar->getNombre()){
                $addProcess .= "nombre: ".$data['nombre']."->".$postvar->getNombre()."<br>";
            }
            if($data['idCiudad'] != $postvar->getIdCiudad()->getId()){
                $addProcess .= "idCiudad: ".$data['idCiudad']."->".$postvar->getIdCiudad()->getId().". ".$postvar->getIdCiudad()->getNombre()."<br>";
            }
            if($data['fechaInicio'] != $postvar->getFechaInicio()->format('Y-m-d')){
                $addProcess .= "fechaInicio: ".$data['fechaInicio']."->".$postvar->getFechaInicio()->format('Y-m-d')."<br>";
            }
            if($data['horaInicio'] != $postvar->getHoraInicio()->format('H:i:s')){
                $addProcess .= "horaInicio: ".$data['horaInicio']."->".$postvar->getHoraInicio()->format('H:i:s')."<br>";
            }
            if($data['fechaFin'] != $postvar->getFechaFin()->format('Y-m-d')){
                $addProcess .= "fechaFin: ".$data['fechaFin']."->".$postvar->getFechaFin()->format('Y-m-d')."<br>";
            }
            if($data['horaFin'] != $postvar->getHoraFin()->format('H:i:s')){
                $addProcess .= "horaFin: ".$data['horaFin']."->".$postvar->getHoraFin()->format('H:i:s')."<br>";
            }
            if($data['numeroGanadores'] != $postvar->getNumeroGanadores()){
                $addProcess .= "numeroGanadores: ".$data['numeroGanadores']."->".$postvar->getNumeroGanadores()."<br>";
            }
            if($data['numeroMaximoGanadores'] != $postvar->getNumeroMaximoGanadores()){
                $addProcess .= "numeroMaximoGanadores: ".$data['numeroMaximoGanadores']."->".$postvar->getNumeroMaximoGanadores()."<br>";
            }
            if($data['numeroProveedores'] != $postvar->getNumeroProveedores()){
                $addProcess .= "numeroProveedores: ".$data['numeroProveedores']."->".$postvar->getNumeroProveedores()."<br>";
            }
            if($data['imagenvar'] != $postvar->getImagen()){
                $addProcess .= "imagenvar: ".$data['imagenvar']."->".$postvar->getImagen()."<br>";
            }
            if($data['premio'] != $postvar->getPremio()){
                $addProcess .= "premio: ".$data['premio']."->".$postvar->getPremio()."<br>";
            }
            if($data['mostrarValor'] != $postvar->getMostrarValor()){
                $addProcess .= "mostrarValor: ".$data['mostrarValor']."->".$postvar->getMostrarValor()."<br>";
            }
            if($data['repiteProveedor'] != $postvar->getRepiteProveedor()){
                $addProcess .= "repiteProveedor: ".$data['repiteProveedor']."->".$postvar->getRepiteProveedor()."<br>";
            }
            if($data['estado'] != $postvar->getEstado()){
                switch($postvar->getEstado()){
                    case "0": $estadov = "Evento Terminado"; break;
                    case "1": $estadov = "Activo"; break;
                    case "9": $estadov = "En Curso"; break;
                    case "5": $estadov = "En Pausa"; break;
                }
                $addProcess .= "estado: ".$data['estado']."->".$postvar->getEstado().". ".$estadov."<br>";
            }

            $useridObj = $this->get('security.token_storage')->getToken()->getUser('Article', 1);
            $userid=$useridObj->getId();
            $usernombre=$useridObj->getNombre();

            $this->processLogAction(1,$userid, "Administrador cambia evento,
                                            Evento: ".$postvar->getId()." - ".$postvar->getNombre().",
                                            Administrador: ".$userid." - ".$usernombre.",<br>$addProcess");

            $archivo1 = $postvar->getImagen();
            if(is_object($archivo1) == 1){
               $fileName1 = md5(uniqid()).'.jpg';
               $ArchivoDir = $this->container->getParameter('kernel.root_dir').'/../web/images/eventos/';
               $archivo1->move($ArchivoDir, $fileName1);
               $postvar->setImagen($fileName1);
            }else{
               $postvar->setImagen($data["imagenvar"]);
            }
            $postvar->setFechaInicio(new \DateTime($postvar->getFechaInicio()->format('Y-m-d').' '.$postvar->getHoraInicio()->format('H:i:s')));
            $postvar->setFechaFin(new \DateTime($postvar->getFechaFin()->format('Y-m-d').' '.$postvar->getHoraFin()->format('H:i:s')));
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

            'idCentro' => $postvar->getIdCentro()->getId(),
            'nombre' => $postvar->getNombre(),
            'idCiudad' => $postvar->getIdCiudad()->getId(),
            'fechaInicio' => $postvar->getFechaInicio()->format('Y-m-d'),
            'horaInicio' => $postvar->getHoraInicio()->format('H:i:s'),
            'fechaFin' => $postvar->getFechaFin()->format('Y-m-d'),
            'horaFin' => $postvar->getHoraFin()->format('H:i:s'),
            'numeroGanadores' => $postvar->getNumeroGanadores(),
            'numeroMaximoGanadores' => $postvar->getNumeroMaximoGanadores(),
            'numeroProveedores' => $postvar->getNumeroProveedores(),
            'imagenvar' => $postvar->getImagen(),
            'premio' => $postvar->getPremio(),
            'mostrarValor' => $postvar->getMostrarValor(),
            'repiteProveedor' => $postvar->getRepiteProveedor(),
            'estado' => $postvar->getEstado(),
        ));
    }
    /**
     * Deletes a entity.
     *
     */
    public function deleteAction(Request $request, Eventos $entityvar)
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

            $eventosProveedores = $this->getDoctrine()->getRepository('CpdgAdministradorBundle:EventosProveedores')
                        ->createQueryBuilder('e')
                        ->where('e.idEvento = :idEvento')->setParameter('idEvento', $data["idEvento"])
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

            $evento = $em->getRepository('CpdgAdministradorBundle:Eventos')->find($data["idEvento"]);


            if($data["valuevar"] == "true"){
                //----------------------------------
                //Verificar Limite de Inscritos
                //----------------------------------
                $verificarInscritos = $this->getDoctrine()->getRepository('CpdgAdministradorBundle:EventosProveedores')
                        ->createQueryBuilder('e')
                        ->select('COUNT(e.id)')
                        ->where('e.idEvento = :idEvento')->setParameter('idEvento', $data["idEvento"])
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
                $verificar = $this->getDoctrine()->getRepository('CpdgAdministradorBundle:EventosProveedores')
                        ->createQueryBuilder('e')
                        ->select('COUNT(e.id)')
                        ->where('e.idEvento = :idEvento')->setParameter('idEvento', $data["idEvento"])
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
                    $idEventoObj = $em->getReference('CpdgAdministradorBundle:Eventos', $data["idEvento"]);
                    $eventosProveedores = new EventosProveedores();
                    $eventosProveedores->setIdEvento($idEventoObj);
                    $eventosProveedores->setIdProveedor($idProveedorObj);
                    $eventosProveedores->setFecha(new \DateTime(date("Y-m-d H:i:s")));
                    $em->persist($eventosProveedores);
                    $em->flush();

                    $useridObj = $this->get('security.token_storage')->getToken()->getUser('Article', 1);
                    $userid=$useridObj->getId();
                    $usernombre=$useridObj->getNombre();
                    $evento = $em->getRepository('CpdgAdministradorBundle:Eventos')->find($data["idEvento"]);
                    $this->processLogAction(1,$userid, "Administrador asocia proveedor al evento,
                                            Evento: ".$evento->getId()." - ".$evento->getNombre().",
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
                // Verificar que no hay ganadores
                //----------------------------------
                $verificar = $this->getDoctrine()->getRepository('CpdgAdministradorBundle:EventosGanadores')
                        ->createQueryBuilder('e')
                        ->select('COUNT(e.id)')
                        ->where('e.idEvento = :idEvento')->setParameter('idEvento', $data["idEvento"])
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

                    $eventosProveedoresc = $this->getDoctrine()->getRepository('CpdgAdministradorBundle:EventosProveedores')
                        ->createQueryBuilder('e')
                        ->where('e.idEvento = :idEvento')->setParameter('idEvento', $data["idEvento"])
                        ->andWhere('e.idProveedor = :idProveedor')->setParameter('idProveedor', $data["idProveedor"])
                        ->getQuery()->execute();
                    foreach ($eventosProveedoresc as $value) {
                        $idDel = $value->getId();
                    }
                    $eventosProveedores = $em->getRepository('CpdgAdministradorBundle:EventosProveedores')->find($idDel);

                    $useridObj = $this->get('security.token_storage')->getToken()->getUser('Article', 1);
                    $userid=$useridObj->getId();
                    $usernombre=$useridObj->getNombre();
                    $evento = $em->getRepository('CpdgAdministradorBundle:Eventos')->find($data["idEvento"]);
                    $this->processLogAction(1,$userid, "Administrador elimina proveedor del evento,
                                            Evento: ".$evento->getId()." - ".$evento->getNombre().",
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

            $eventosInscripciones = $this->getDoctrine()->getRepository('CpdgAdministradorBundle:EventosInscripciones')
                        ->createQueryBuilder('e')
                        ->where('e.idEvento = :idEvento')->setParameter('idEvento', $data["idEvento"])
                        ->orderBy('e.idSorteo','desc')
                        ->getQuery()->execute();


            foreach ($eventosInscripciones as $eventosInscripcione) {
               $inscripciones[$x]["id"] = $eventosInscripcione->getId();
               $inscripciones[$x]["idProveedor"] = $eventosInscripcione->getIdProveedor();
               $idsorteo = str_pad($eventosInscripcione->getIdSorteo()->getId(), 6, "0", STR_PAD_LEFT);
               $inscripciones[$x]["idSorteo"] = $idsorteo;
               $inscripciones[$x]["proveedor"] = $eventosInscripcione->getIdProveedor()->getNombre();
               $inscripciones[$x]["nit"] = $eventosInscripcione->getIdAsociado()->getNit();
               $inscripciones[$x]["asociado"] = $eventosInscripcione->getIdAsociado()->getNombreAsociado();
               $inscripciones[$x]["codigo"] = $eventosInscripcione->getIdAsociado()->getCodigo();
               $inscripciones[$x]["nombreDrogueria"] = $eventosInscripcione->getIdAsociado()->getNombreDrogueria();
               $inscripciones[$x]["fecha"] = $eventosInscripcione->getFecha()->format('Y-m-d');
               $inscripciones[$x]["hora"] = $eventosInscripcione->getHora()->format('H:i:s');
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
             $xb = 0;
            $ganadores = Array();
            $todoslosganadores = Array();

            $sorteos = $this->getDoctrine()->getRepository('CpdgAdministradorBundle:Sorteos')
                    ->createQueryBuilder('e')
                    ->where('e.idEvento = :idEvento')->setParameter('idEvento', $data["idEvento"])
                    ->andWhere('e.tipo = :tipo')->setParameter('tipo', '1')
                    ->andWhere('e.estado = :estado')->setParameter('estado', '1')
                    ->orderBy("e.id","asc")
                    ->getQuery()->execute();
            $y = 0;
            foreach ($sorteos as $value) {
                $idSorteo = $value->getId();
                $y++;
            }

            if($y != 0){
                $eventosGanadores = $this->getDoctrine()->getRepository('CpdgAdministradorBundle:EventosGanadores')
                            ->createQueryBuilder('e')
                            ->where('e.idEvento = :idEvento')->setParameter('idEvento', $data["idEvento"])
                            ->andWhere('e.idSorteo = :idSorteo')->setParameter('idSorteo', $idSorteo)
                            ->getQuery()->execute();


                foreach ($eventosGanadores as $eventosGanador) {
                   $ganadores[$x]["id"] = $eventosGanador->getId();
                   $ganadores[$x]["idProveedor"] = $eventosGanador->getIdProveedor();

                   $ganadores[$x]["proveedor"] = $eventosGanador->getIdProveedor()->getNombre();
                   $ganadores[$x]["nit"] = $eventosGanador->getIdAsociado()->getNit();
                   $ganadores[$x]["asociado"] = $eventosGanador->getIdAsociado()->getNombreAsociado();
                   $ganadores[$x]["codigo"] = $eventosGanador->getIdAsociado()->getCodigo();
                   $ganadores[$x]["nombreDrogueria"] = $eventosGanador->getIdAsociado()->getNombreDrogueria();
                   $ganadores[$x]["fecha"] = $eventosGanador->getIdSorteo()->getFechaCierre()->format('Y-m-d');
                   $ganadores[$x]["hora"] = $eventosGanador->getIdSorteo()->getFechaCierre()->format('H:i:s');
                   $x++;
                }

                $eventosGanadoresb = $this->getDoctrine()->getRepository('CpdgAdministradorBundle:EventosGanadores')
                            ->createQueryBuilder('e')
                            ->where('e.idEvento = :idEvento')->setParameter('idEvento', $data["idEvento"])
                            ->orderBy("e.idSorteo","desc")
                            ->getQuery()->execute();


                foreach ($eventosGanadoresb as $value) {
                   $todoslosganadores[$xb]["id"] = $value->getId();
                   $todoslosganadores[$xb]["idProveedor"] = $value->getIdProveedor();
                   $todoslosganadores[$xb]["idSorteo"] = str_pad($value->getIdSorteo()->getId(), 6, "0", STR_PAD_LEFT);

                   $todoslosganadores[$xb]["proveedor"] = $value->getIdProveedor()->getNombre();
                   $todoslosganadores[$xb]["nit"] = $value->getIdAsociado()->getNit();
                   $todoslosganadores[$xb]["asociado"] = $value->getIdAsociado()->getNombreAsociado();
                   $todoslosganadores[$xb]["codigo"] = $value->getIdAsociado()->getCodigo();
                   $todoslosganadores[$xb]["nombreDrogueria"] = $value->getIdAsociado()->getNombreDrogueria();
                   $todoslosganadores[$xb]["fecha"] = $value->getFecha()->format('Y-m-d');
                   $todoslosganadores[$xb]["hora"] = $value->getHora()->format('H:i:s');
                   $xb++;
                }
            }

            $aleatoriosProveedores = array();
            $gAleatoriosProveedores = $this->getDoctrine()->getRepository('CpdgAdministradorBundle:EventosGanadoresAleatoriosProveedores')
                            ->createQueryBuilder('e')
                            ->where('e.idEvento = :idEvento')->setParameter('idEvento', $data["idEvento"])
                            ->orderBy("e.id","desc")
                            ->getQuery()->execute();
            $xyz = 0;
            foreach ($gAleatoriosProveedores as $key => $value) {
                $aleatoriosProveedores[$xyz]["id"] = $value->getId();
                $aleatoriosProveedores[$xyz]["nombre"] = $value->getIdProveedor()->getNombre();
                $aleatoriosProveedores[$xyz]["fecha"] = $value->getFecha()->format("Y-m-d");
                $aleatoriosProveedores[$xyz]["hora"] = $value->getHora()->format("H:i:s");
                $xyz++;
            }

            $aleatoriosAsociados = array();
            $gAleatoriosAsociados = $this->getDoctrine()->getRepository('CpdgAdministradorBundle:EventosGanadoresAleatoriosAsociados')
                            ->createQueryBuilder('e')
                            ->where('e.idEvento = :idEvento')->setParameter('idEvento', $data["idEvento"])
                            ->orderBy("e.id","desc")
                            ->getQuery()->execute();
            $xyz = 0;
            foreach ($gAleatoriosAsociados as $key => $value) {
                $aleatoriosAsociados[$xyz]["id"] = $value->getId();
                //$aleatoriosAsociados[$xyz]["nombre"] = $value->getIdAsociado()->getNombreDrogueria();
                $aleatoriosAsociados[$xyz]["nombre"] = $value->getIdAsociado()->getNombreAsociado();
                $aleatoriosAsociados[$xyz]["fecha"] = $value->getFecha()->format("Y-m-d");
                $aleatoriosAsociados[$xyz]["hora"] = $value->getHora()->format("H:i:s");
                $xyz++;
            }

            $serializer = new Serializer($normalizers, $encoders);

            $response = new JsonResponse();
            $response->setStatusCode(200);
            $response->setData(array(
                'response' => 'success',
                'ganadores' => $ganadores,
                'todoslosganadores' => $todoslosganadores,
                'aleatoriosProveedores' => $aleatoriosProveedores,
                'aleatoriosAsociados' => $aleatoriosAsociados,
                'total' => $x,
                'totalb' => $xb,
            ));
            return $response;
        }
    }

    public function cargarAleatorioAction(Request $request, $id)
    {
        if($request->isXmlHttpRequest())
        {
            $em = $this->getDoctrine()->getManager();
            $data = $request->request->all();
            $encoders = array(new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());
            $x = 0;
            $ganadores = Array();

            if($data["idvar"] == "1"){

                $max = $em->createQuery('SELECT COUNT(q.id) FROM CpdgAdministradorBundle:EventosProveedores q
                            WHERE q.idEvento = '.$data["idEvento"].'
                    ')
                ->getSingleScalarResult();

                $offset = max(0, rand(0, ($max - 1)));

                $aleatorio = $em->createQuery('SELECT q FROM CpdgAdministradorBundle:EventosProveedores q
                            WHERE
                             q.idEvento = '.$data["idEvento"].'
                            AND q.id >= :rand
                            ORDER BY q.id ASC')
                            ->setParameter('rand',rand(0,$max))
                            ->setMaxResults(1)
                            ->setFirstResult($offset)
                            ->getSingleResult();

                $ganadores[0]["id"] = $aleatorio->getIdProveedor()->getId();
                $ganadores[0]["nombre"] = $aleatorio->getIdProveedor()->getNombre();
                $ganadores[0]["imagen"] = $aleatorio->getIdProveedor()->getImagen();

                $useridObj = $this->get('security.token_storage')->getToken()->getUser('Article', 1);
                $userid=$useridObj->getId();
                $usernombre=$useridObj->getNombre();
                $evento = $em->getRepository('CpdgAdministradorBundle:Eventos')->find($data["idEvento"]);
                $this->processLogAction(1,$userid, "Administrador genera proveedor aleatorio,
                                        Evento: ".$evento->getId()." - ".$evento->getNombre().",
                                        Administrador: ".$userid." - ".$usernombre.",
                                        Drogueria: ".$aleatorio->getIdProveedor()->getId()." - ".$aleatorio->getIdProveedor()->getNombre());

                $idEventoO = $em->getReference('CpdgAdministradorBundle:Eventos', $evento->getId());
                $idProveedorO = $em->getReference('CpdgAdministradorBundle:Proveedores', $aleatorio->getIdProveedor()->getId());

                $eventosGanadoresAleatoriosProveedores = new EventosGanadoresAleatoriosProveedores();
                $eventosGanadoresAleatoriosProveedores->setIdEvento($idEventoO);
                $eventosGanadoresAleatoriosProveedores->setIdProveedor($idProveedorO);
                $eventosGanadoresAleatoriosProveedores->setFecha(new \DateTime(date("Y-m-d")));
                $eventosGanadoresAleatoriosProveedores->setHora(new \DateTime(date("H:i:s")));
                $em->persist($eventosGanadoresAleatoriosProveedores);
                $em->flush();

            }else{

                //------Se busca la info del evento------------
                $evento = $em->getRepository('CpdgAdministradorBundle:Eventos')->find($data["idEvento"]);

                //------- Se buscan y guardan los asociados ganadores previos --------------
                $arrayGanadoresPrevios = array();


                if($evento->getRepiteAsociadoProveedor() == false){

                    $ganadoresPrevios = $em->getRepository('CpdgAdministradorBundle:EventosGanadores')->findBy(array('idEvento' => $data["idEvento"] ) );
                    foreach($ganadoresPrevios as $previos){
                        $arrayGanadoresPrevios[$previos->getIdAsociado()->getCodigo()] = $previos->getIdAsociado()->getCodigo();
                    }

                    $ganadoresPreviosAleatorios = $em->getRepository('CpdgAdministradorBundle:EventosGanadoresAleatoriosAsociados')->findBy(array('idEvento' => $data["idEvento"] ) );
                    foreach($ganadoresPreviosAleatorios as $gpa){
                        $arrayGanadoresPrevios[$gpa->getIdAsociado()->getCodigo()] = $gpa->getIdAsociado()->getCodigo();
                    }

                }

                //-----------------------------------------------

                $max = $em->createQuery('SELECT COUNT(q.id) FROM CpdgAdministradorBundle:EventosInscripciones q
                    WHERE q.idEvento = '.$data["idEvento"].'
                    ')
                ->getSingleScalarResult();

                $offset = max(0, rand(0, ($max - 1)));


                $aleatorio = $em->createQuery('SELECT q FROM CpdgAdministradorBundle:EventosInscripciones q
                            WHERE q.id >= :rand
                            and q.idEvento = '.$data["idEvento"])
                            ->setParameter('rand',rand(0,$max))
                            ->setMaxResults(1)
                            ->setFirstResult($offset)
                            ->getSingleResult();

                // se implementa la condicion para verificar si el asociado ya ha sido ganador
                if(isset( $arrayGanadoresPrevios[$aleatorio->getIdAsociado()->getCodigo()] ) ){

                    $response = new JsonResponse();
                    $response->setStatusCode(200);
                    $response->setData(array(
                        'response' => 'error',
                        'mensaje' => 'El ganador aleatorio fue elegido ganador previamente. Intente nuevamente.',
                    ));
                    return $response;

                }else{
                    $ganadores[0]["id"] = $aleatorio->getIdAsociado()->getId();
                    $ganadores[0]["nombreAsociado"] = $aleatorio->getIdAsociado()->getNombreAsociado();
                    $ganadores[0]["nombreDrogueria"] = $aleatorio->getIdAsociado()->getNombreDrogueria();
                    $ganadores[0]["codigo"] = $aleatorio->getIdAsociado()->getCodigo();
                    $ganadores[0]["nit"] = $aleatorio->getIdAsociado()->getNit();

                    $useridObj = $this->get('security.token_storage')->getToken()->getUser('Article', 1);
                    $userid=$useridObj->getId();
                    $usernombre=$useridObj->getNombre();

                    //aca
                    //$evento = $em->getRepository('CpdgAdministradorBundle:Eventos')->find($data["idEvento"]);
                    $this->processLogAction(1,$userid, "Administrador genera asociado aleatorio,
                                            Evento: ".$evento->getId()." - ".$evento->getNombre().",
                                            Administrador: ".$userid." - ".$usernombre.",
                                            Drogueria: ".$aleatorio->getIdProveedor()->getId()." - ".$aleatorio->getIdAsociado()->getNit()." - ".$aleatorio->getIdAsociado()->getNombreAsociado());

                    $idEventoO = $em->getReference('CpdgAdministradorBundle:Eventos', $evento->getId());
                    $idAsociadoO = $em->getReference('CpdgAdministradorBundle:Asociados', $aleatorio->getIdAsociado()->getId());

                    $eventosGanadoresAleatoriosAsociados = new EventosGanadoresAleatoriosAsociados();
                    $eventosGanadoresAleatoriosAsociados->setIdEvento($idEventoO);
                    $eventosGanadoresAleatoriosAsociados->setIdAsociado($idAsociadoO);
                    $eventosGanadoresAleatoriosAsociados->setFecha(new \DateTime(date("Y-m-d")));
                    $eventosGanadoresAleatoriosAsociados->setHora(new \DateTime(date("H:i:s")));
                    $em->persist($eventosGanadoresAleatoriosAsociados);
                    $em->flush();
                }



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
            ->setCellValue('C2', 'Nombre del Evento')
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
            $nombreEvento = $item->getNombre();
            switch($item->getEstado()){
                case "0": $estado = "TERMINADO"; break;
                case "1": $estado = "ACTIVO"; break;
                case "9": $estado = "EN CURSO"; break;
                case "5": $estado = "EN PAUSA"; break;
            }
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$row, $item->getIdCentro()->getCentro())
                ->setCellValue('B'.$row, $item->getIdCiudad()->getNombre())
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

        $query2 = $em->getRepository('CpdgAdministradorBundle:EventosProveedores')
                ->createQueryBuilder('e')
                ->where('e.idEvento = :idEvento')->setParameter('idEvento', $id)
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
            ->setCellValue('A'.$row, 'Sorteo')
            ->setCellValue('B'.$row, 'Proveedor')
            ->setCellValue('C'.$row, 'Centro')
            ->setCellValue('D'.$row, 'Nombre Asociado')
            ->setCellValue('E'.$row, 'Nombre Drogueria')
            ->setCellValue('F'.$row, 'Codigo')
            ->setCellValue('G'.$row, 'Email')
            ->setCellValue('H'.$row, 'Nit')
            ->setCellValue('I'.$row, 'Ciudad')
            ->setCellValue('J'.$row, 'Departamento')
            ->setCellValue('K'.$row, 'Fecha y Hora')
            ;

        $query3 = $em->getRepository('CpdgAdministradorBundle:EventosInscripciones')
                ->createQueryBuilder('e')
                ->where('e.idEvento = :idEvento')->setParameter('idEvento', $id)
                ->leftJoin('CpdgAdministradorBundle:Proveedores', 'p', 'WITH', 'p.id = e.idProveedor')
                ->leftJoin('CpdgAdministradorBundle:Asociados', 'a', 'WITH', 'a.id = e.idAsociado')
                ;
        $result3 = $query3->getQuery()->getResult();

        $row ++;
        foreach ($result3 as $item3) {
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$row, str_pad($item3->getIdSorteo()->getId(), 6, "0", STR_PAD_LEFT))
                ->setCellValue('B'.$row, $item3->getIdProveedor()->getNombre())
                ->setCellValue('C'.$row, $item3->getIdAsociado()->getIdCentro()->getCentro())
                ->setCellValue('D'.$row, $item3->getIdAsociado()->getNombreAsociado())
                ->setCellValue('E'.$row, $item3->getIdAsociado()->getNombreDrogueria())
                ->setCellValue('F'.$row, $item3->getIdAsociado()->getCodigo())
                ->setCellValue('G'.$row, $item3->getIdAsociado()->getEmail())
                ->setCellValue('H'.$row, $item3->getIdAsociado()->getNit())
                ->setCellValue('I'.$row, $item3->getIdAsociado()->getCiudad())
                ->setCellValue('J'.$row, $item3->getIdAsociado()->getDepartamento())
                ->setCellValue('K'.$row, $item3->getFecha()->format('Y-m-d')." ".$item3->getHora()->format('H:i:s'))
                ;
            $row++;
        }


        // GANADORES
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$row, '');
        $row++;
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$row, 'Ganadores del evento');
        $row++;
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$row, 'Sorteo')
            ->setCellValue('B'.$row, 'Proveedor')
            ->setCellValue('C'.$row, 'Centro')
            ->setCellValue('D'.$row, 'Nombre Asociado')
            ->setCellValue('E'.$row, 'Nombre Drogueria')
            ->setCellValue('F'.$row, 'Codigo')
            ->setCellValue('G'.$row, 'Email')
            ->setCellValue('H'.$row, 'Nit')
            ->setCellValue('I'.$row, 'Ciudad')
            ->setCellValue('J'.$row, 'Departamento')
            ->setCellValue('K'.$row, 'Fecha y Hora')
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
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('K')
            ->setWidth(25);



        $query3 = $em->getRepository('CpdgAdministradorBundle:EventosGanadores')
                ->createQueryBuilder('e')
                ->where('e.idEvento = :idEvento')->setParameter('idEvento', $id)
                ->leftJoin('CpdgAdministradorBundle:Proveedores', 'p', 'WITH', 'p.id = e.idProveedor')
                ->leftJoin('CpdgAdministradorBundle:Asociados', 'a', 'WITH', 'a.id = e.idAsociado')
                ;
        $result3 = $query3->getQuery()->getResult();

        $row ++;
        foreach ($result3 as $item3) {
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$row, str_pad($item3->getIdSorteo()->getId(), 6, "0", STR_PAD_LEFT))
                ->setCellValue('B'.$row, $item3->getIdProveedor()->getNombre())
                ->setCellValue('C'.$row, $item3->getIdAsociado()->getIdCentro()->getCentro())
                ->setCellValue('D'.$row, $item3->getIdAsociado()->getNombreAsociado())
                ->setCellValue('E'.$row, $item3->getIdAsociado()->getNombreDrogueria())
                ->setCellValue('F'.$row, $item3->getIdAsociado()->getCodigo())
                ->setCellValue('G'.$row, $item3->getIdAsociado()->getEmail())
                ->setCellValue('H'.$row, $item3->getIdAsociado()->getNit())
                ->setCellValue('I'.$row, $item3->getIdAsociado()->getCiudad())
                ->setCellValue('J'.$row, $item3->getIdAsociado()->getDepartamento())
                ->setCellValue('K'.$row, $item3->getIdSorteo()->getFechaCierre()->format("Y-m-d H:i:s"))
                ;
            $row++;
        }


        // GANADORES aleatorio proveedores
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$row, '');
        $row++;
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$row, 'Ganadores sorteo aleatorio proveedores');
        $row++;
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$row, 'Asociado Ganador')
            ->setCellValue('B'.$row, 'Fecha y Hora')
            ;

        $query4 = $em->getRepository('CpdgAdministradorBundle:EventosGanadoresAleatoriosProveedores')
                ->createQueryBuilder('e')
                ->where('e.idEvento = :idEvento')->setParameter('idEvento', $id)
                ->leftJoin('CpdgAdministradorBundle:Proveedores', 'p', 'WITH', 'p.id = e.idProveedor')
                ;
        $result4 = $query4->getQuery()->getResult();

        $row ++;
        foreach ($result4 as $item4) {
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$row, $item4->getIdProveedor()->getNombre())
                ->setCellValue('B'.$row, $item4->getFecha()->format("Y-m-d")." ".$item4->getHora()->format("H:i:s"))
                ;
            $row++;
        }

        // GANADORES aleatorio asociados
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$row, '');
        $row++;
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$row, 'Ganadores sorteo aleatorio asociados');
        $row++;
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$row, 'Asociado Ganador')
            ->setCellValue('B'.$row, 'Fecha y Hora')
            ;

        $query5 = $em->getRepository('CpdgAdministradorBundle:EventosGanadoresAleatoriosAsociados')
                ->createQueryBuilder('e')
                ->where('e.idEvento = :idEvento')->setParameter('idEvento', $id)
                ->leftJoin('CpdgAdministradorBundle:Asociados', 'a', 'WITH', 'a.id = e.idAsociado')
                ;
        $result5 = $query5->getQuery()->getResult();

        $row ++;
        foreach ($result5 as $item5) {
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$row, $item5->getIdAsociado()->getNombreDrogueria())
                ->setCellValue('B'.$row, $item5->getFecha()->format("Y-m-d")." ".$item5->getHora()->format("H:i:s"))
                ;
            $row++;
        }


        // se crea el writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // se crea el response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // y por último se añaden las cabeceras
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT,'Reporte_Evento '.date("YmdHis").'.xls');
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
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
