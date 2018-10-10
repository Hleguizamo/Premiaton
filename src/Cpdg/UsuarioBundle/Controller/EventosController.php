<?php

namespace Cpdg\UsuarioBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Cpdg\UsuarioBundle\Entity\Eventos;
use Cpdg\UsuarioBundle\Entity\Asociados;
use Cpdg\UsuarioBundle\Entity\Logs;
use Cpdg\UsuarioBundle\Entity\EventosInscripciones;
use Cpdg\UsuarioBundle\Form\EventosType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
 
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;


/**
 * Eventos controller.
 *
 */
class EventosController extends Controller
{
   public $baseBundle = "CpdgUsuarioBundle";
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
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $useridObj = $this->get('security.token_storage')->getToken()->getUser('Article', 1);
        $userid=$useridObj->getIdProveedor()->getId();
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
        $etiquetas[] = "Nombre";
        $etiquetas[] = "Fecha Inicio";
        $etiquetas[] = "Hora Inicio";
        $etiquetas[] = "Fecha Fin";
        $etiquetas[] = "Hora Fin";        
        $etiquetas[] = "Inscritos";
        $etiquetas[] = "Ganadores";
        //----------------------------------

        $consulta = $this->getDoctrine()->getRepository($this->baseBundle.":".$this->entityMain)->createQueryBuilder('e')
        ->leftJoin($this->baseBundle.':EventosProveedores', 'eip', 'WITH', 'eip.idEvento = e.id');
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
        $consulta->andWhere('eip.idProveedor = :idProveedor')->setParameter('idProveedor', $userid);
        $consulta->andWhere('e.estado IN (:estado)')->setParameter('estado', array('1','9'));
        //---Advanced Search----------------

        //----------------------------------
        $paginator  = $this->get('knp_paginator');
        $resultset = $paginator->paginate(
            $consulta,
            $page,$cantidadPorPagina
        );
         $proveedores = $this->getDoctrine()->getRepository($this->baseBundle.":Proveedores")->createQueryBuilder('e')->orderBy("e.nombre", "asc")->getQuery()->execute();
        return $this->render('CpdgUsuarioBundle:Templates:indexbase.html.twig', array(
            'titulo' => $this->titulo,
            'namevar' => strtolower($this->entityMain."usr"),
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

    public function cargarInscripcionesAction(Request $request, $id)
    {
        if($request->isXmlHttpRequest())
        {
            $em = $this->getDoctrine()->getManager();
            $data = $request->request->all();
            $useridObj = $this->get('security.token_storage')->getToken()->getUser('Article', 1);
            $userid=$useridObj->getIdProveedor()->getId();
            $encoders = array(new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());
            $x = 0;
            $inscripciones = Array();

            $eventosInscripciones = $this->getDoctrine()->getRepository('CpdgUsuarioBundle:EventosInscripciones')
                        ->createQueryBuilder('e')
                        ->where('e.idEvento = :idEvento')->setParameter('idEvento', $data["idEvento"])
                        ->andWhere('e.idProveedor = :idProveedor')->setParameter('idProveedor', $userid)
                        ->orderBy('e.fecha, e.hora','desc')
                        ->getQuery()->execute();
           

            foreach ($eventosInscripciones as $eventosInscripcione) {
               $inscripciones[$x]["id"] = $eventosInscripcione->getId();
               $inscripciones[$x]["idProveedor"] = $eventosInscripcione->getIdProveedor()->getId();
               $inscripciones[$x]["idSorteo"] =str_pad($eventosInscripcione->getIdSorteo()->getId(), 6, "0", STR_PAD_LEFT);

               $inscripciones[$x]["proveedor"] = $eventosInscripcione->getIdProveedor()->getNombre();
               $inscripciones[$x]["nit"] = $eventosInscripcione->getIdAsociado()->getNit();
               $inscripciones[$x]["asociado"] = $eventosInscripcione->getIdAsociado()->getNombreAsociado();
               $inscripciones[$x]["codigo"] = $eventosInscripcione->getIdAsociado()->getCodigo();
               $inscripciones[$x]["centro"] = $eventosInscripcione->getIdAsociado()->getIdCentro()->getCentro();
               $inscripciones[$x]["nombreDrogueria"] = $eventosInscripcione->getIdAsociado()->getNombreDrogueria();

               $inscripciones[$x]["ciudad"] = $eventosInscripcione->getIdAsociado()->getCiudad();
               $inscripciones[$x]["departamento"] = $eventosInscripcione->getIdAsociado()->getDepartamento();
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
            $useridObj = $this->get('security.token_storage')->getToken()->getUser('Article', 1);
            $userid=$useridObj->getIdProveedor()->getId();

            $encoders = array(new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());

             $x = 0;
            $ganadores = Array();

            $eventosGanadores = $this->getDoctrine()->getRepository('CpdgUsuarioBundle:EventosGanadores')
                        ->createQueryBuilder('e')
                        ->where('e.idEvento = :idEvento')->setParameter('idEvento', $data["idEvento"])
                        ->andWhere('e.idProveedor = :idProveedor')->setParameter('idProveedor', $userid)
                        ->orderBy('e.idSorteo','desc')
                        ->getQuery()->execute();

            foreach ($eventosGanadores as $eventosInscripcione) {
               $ganadores[$x]["id"] = $eventosInscripcione->getId();
               $ganadores[$x]["idProveedor"] = $eventosInscripcione->getIdProveedor();
               $ganadores[$x]["idSorteo"] =str_pad($eventosInscripcione->getIdSorteo()->getId(), 6, "0", STR_PAD_LEFT);

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

    public function inscribirDrogueriaAction(Request $request, $id)
    {
        if($request->isXmlHttpRequest())
        {
            $em = $this->getDoctrine()->getManager();
            $data = $request->request->all();
            $useridObj = $this->get('security.token_storage')->getToken()->getUser('Article', 1);
            $userid=$useridObj->getIdProveedor()->getId();
            $evento = $em->getRepository('CpdgUsuarioBundle:Eventos')->find($data["idEvento"]);

            $encoders = array(new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());
            

            if($evento->getEstado() == 0){

                $serializer = new Serializer($normalizers, $encoders);            
                $response = new JsonResponse();
                $response->setStatusCode(200);
                $response->setData(array(
                    'response' => 'eventocerrado',
                    'idEvento' => $data["idEvento"],
                ));
                return $response; 
            }
            //------------------------------------------------

            $countAsociados = $this->getDoctrine()->getRepository('CpdgUsuarioBundle:Asociados')
                        ->createQueryBuilder('e')
                        ->select('COUNT(e.id)')
                        ->where('e.codigo = :codigo')->setParameter('codigo', $data["codigoAsociado"])      
                        ->getQuery()
                        ->getSingleScalarResult();
            if($countAsociados == 0){ 
                $serializer = new Serializer($normalizers, $encoders);
            
                $response = new JsonResponse();
                $response->setStatusCode(200);
                $response->setData(array(
                    'response' => 'desconocido',
                    'idEvento' => $data["idEvento"],
                ));
                return $response;
            }

            //------------------------------------------------
            $asociados = $this->getDoctrine()->getRepository('CpdgUsuarioBundle:Asociados')
                        ->createQueryBuilder('e')
                        ->where('e.codigo = :codigo')->setParameter('codigo', $data["codigoAsociado"])      
                        ->getQuery()->execute();
            foreach ($asociados as $asociado) {
                $idAsociado = $asociado->getId();
                $nitAsociado = $asociado->getNit();
                $emailAsociado = $asociado->getEmail();
                $idAsociadoCentro = $asociado->getIdCentro()->getId();
            }
            
            if($idAsociadoCentro != $evento->getIdCentro()->getId()){

               $serializer = new Serializer($normalizers, $encoders);
            
                $response = new JsonResponse();
                $response->setStatusCode(200);
                $response->setData(array(
                    'response' => 'nocentro',
                    'idEvento' => $data["idEvento"],
                ));
                return $response; 
            }
            //------------------------------------------------


            $sorteos = $this->getDoctrine()->getRepository('CpdgUsuarioBundle:Sorteos')
                    ->createQueryBuilder('e')
                    ->where('e.idEvento = :idEvento')->setParameter('idEvento', $data["idEvento"])
                    ->andWhere('e.tipo = :tipo')->setParameter('tipo', '1')
                    ->orderBy("e.fechaCreacion","desc")      
                    ->getQuery()->execute();
            foreach ($sorteos as $value) {
                $idSorteo = $value->getId();
                break;
            }

            /*$verEventosInscripciones = $this->getDoctrine()->getRepository('CpdgUsuarioBundle:EventosInscripciones')
                        ->createQueryBuilder('e')
                        ->select('COUNT(e.id)')
                        ->where('e.idEvento = :idEvento')->setParameter('idEvento', $data["idEvento"])
                        ->andWhere('e.idProveedor = :idProveedor')->setParameter('idProveedor', $userid)
                        ->andWhere('e.idAsociado = :idAsociado')->setParameter('idAsociado', $idAsociado)  
                        ->andWhere('e.idSorteo = :idSorteo')->setParameter('idSorteo', $idSorteo)    
                        ->getQuery()
                        ->getSingleScalarResult();*/

            $verEventosInscripciones = $this->getDoctrine()->getRepository('CpdgUsuarioBundle:EventosInscripciones')
                        ->createQueryBuilder('e')
                        ->select('COUNT(e.id)')
                        ->where('e.idEvento = :idEvento')->setParameter('idEvento', $data["idEvento"])
                        ->andWhere('e.idProveedor = :idProveedor')->setParameter('idProveedor', $userid)
                        ->andWhere('e.idAsociado = :idAsociado')->setParameter('idAsociado', $idAsociado)     
                        ->getQuery()
                        ->getSingleScalarResult();

            if($verEventosInscripciones != 0){ 
                $serializer = new Serializer($normalizers, $encoders);
            
                $response = new JsonResponse();
                $response->setStatusCode(200);
                $response->setData(array(
                    'response' => 'inscrito',
                    'idEvento' => $data["idEvento"],
                ));
                return $response;
            }else{
                $idEventoObj = $em->getReference('CpdgUsuarioBundle:Eventos', $data["idEvento"]);
                $idProveedorObj = $em->getReference('CpdgUsuarioBundle:Proveedores', $userid); 
                $idAsociadoObj = $em->getReference('CpdgUsuarioBundle:Asociados', $idAsociado);


                //Consulta repetida en la linea 313
                /*$sorteos = $this->getDoctrine()->getRepository('CpdgUsuarioBundle:Sorteos')
                        ->createQueryBuilder('e')
                        ->where('e.idEvento = :idEvento')->setParameter('idEvento', $data["idEvento"])
                        ->andWhere('e.tipo = :tipo')->setParameter('tipo', '1')
                        ->orderBy("e.fechaCreacion","desc")      
                        ->getQuery()->execute();
                        
                foreach ($sorteos as $value) {
                    $idSorteo = $value->getId();
                    break;
                }*/
                $idSorteoObj = $em->getReference('CpdgUsuarioBundle:Sorteos', $idSorteo);

                $newEventosInscripciones = new EventosInscripciones();
                $newEventosInscripciones->setIdEvento($idEventoObj);
                $newEventosInscripciones->setIdSorteo($idSorteoObj);
                $newEventosInscripciones->setIdProveedor($idProveedorObj);
                $newEventosInscripciones->setIdAsociado($idAsociadoObj);                
                $newEventosInscripciones->setFecha(new \DateTime(date("Y-m-d"))); 
                $newEventosInscripciones->setHora(new \DateTime(date("H:i:s")));           
                $em->persist($newEventosInscripciones);
                $em->flush();

                $message = \Swift_Message::newInstance()
                ->setSubject('Ha sido inscrito en PREMIATÓN')
                ->setFrom('noreply@premiaton.com')
                ->setTo($emailAsociado)
                ->setBody(
                    $this->renderView(
                        'CpdgUsuarioBundle:eventos:emailSubmit.html.twig',
                        array(
                            'proveedor' => $newEventosInscripciones->getIdProveedor()->getNombre(),
                            'evento' => $evento->getNombre(),
                            )
                    ),
                    'text/html'
                );
                //$this->get('mailer')->send($message);
                // Se captura los errores en el envio
                try{
                    $this->get('mailer')->send($message);
                }catch(\Exception $e){
                    $noCorreo = $e->getMessage();
                }

                $this->processLogAction(1,$userid, "Proveedor Inscribe Droguería, Evento: ".$evento->getId()." - ".$evento->getNombre().", Proveedor: ".$newEventosInscripciones->getIdProveedor()->getId()." - ".$newEventosInscripciones->getIdProveedor()->getNombre().", Drogueria: ".$idAsociado." - ".$nitAsociado);

            }

            $serializer = new Serializer($normalizers, $encoders);
            
            $response = new JsonResponse();
            $response->setStatusCode(200);
            $response->setData(array(
                'response' => 'success',
                'idEvento' => $data["idEvento"],
                'temp' => 'ok',
            ));
            return $response;
        }
    }
    public function eliminarDrogueriaAction(Request $request, $id)
    {
        if($request->isXmlHttpRequest())
        {
            $em = $this->getDoctrine()->getEntityManager();
            $data = $request->request->all();
            $useridObj = $this->get('security.token_storage')->getToken()->getUser('Article', 1);
            $userid=$useridObj->getIdProveedor()->getId();
            $usernombre=$useridObj->getIdProveedor()->getNombre();

            $encoders = array(new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());


            
            $eventosInscripciones = $em->getRepository('CpdgUsuarioBundle:EventosInscripciones')->find($data["idvar"]);
            $evento = $em->getRepository('CpdgUsuarioBundle:Eventos')->find($data["idEvento"]);


            $verEventosGanadores = $this->getDoctrine()->getRepository('CpdgUsuarioBundle:EventosGanadores')
                        ->createQueryBuilder('e')
                        ->select('COUNT(e.id)')
                        ->where('e.idEvento = :idEvento')->setParameter('idEvento', $data["idEvento"])
                        ->andWhere('e.idProveedor = :idProveedor')->setParameter('idProveedor', $userid)
                        ->andWhere('e.idAsociado = :idAsociado')->setParameter('idAsociado', $eventosInscripciones->getIdAsociado()->getId())      
                        ->getQuery()
                        ->getSingleScalarResult();

            if($verEventosGanadores > 0){ 
                $serializer = new Serializer($normalizers, $encoders);
            
                $response = new JsonResponse();
                $response->setStatusCode(200);
                $response->setData(array(
                    'response' => 'ganador',
                    'idEvento' => $data["idEvento"],
                ));
                return $response;
            }
            
            $this->processLogAction(1,$userid, "Proveedor Elimina Droguería, Evento: ".$evento->getId()." - ".$evento->getNombre().", Proveedor: ".$userid." - ".$usernombre.", Drogueria: ".$eventosInscripciones->getIdAsociado()->getId()." - ".$eventosInscripciones->getIdAsociado()->getNit());  

            $em->remove($eventosInscripciones);
            $em->flush();  

                    
 
            $serializer = new Serializer($normalizers, $encoders);
            
            $response = new JsonResponse();
            $response->setStatusCode(200);
            $response->setData(array(
                'response' => 'success',
            ));
            return $response;
        }
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
