<?php

namespace Cpdg\AdministradorBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Cpdg\AdministradorBundle\Entity\Proveedores;
use Cpdg\AdministradorBundle\Entity\Archivos;
use Cpdg\UsuarioBundle\Entity\Logs;
use Cpdg\AdministradorBundle\Form\ProveedoresType;
use Cpdg\AdministradorBundle\Form\ArchivosType;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use Cpdg\AdministradorBundle\Entity\ProveedoresUsuarios;
use Cpdg\AdministradorBundle\Form\ProveedoresUsuariosType;

/**
 * Proveedores controller.
 *
 */
class ProveedoresController extends Controller
{
   public $baseBundle = "CpdgAdministradorBundle";
   public $entityMain = "Proveedores";
   public $titulo = "Proveedores";
   public $campos;

   public function __construct(){
        //----------------------------------
        $this->campos["etiquetas"][] = "Nombre";
        $this->campos["etiquetas"][] = "NIT";
        $this->campos["etiquetas"][] = "Representante";
        $this->campos["etiquetas"][] = "Teléfono";
        $this->campos["etiquetas"][] = "Email";

        $this->campos["tipos"][] = "text";
        $this->campos["tipos"][] = "text";
        $this->campos["tipos"][] = "text";
        $this->campos["tipos"][] = "text";
        $this->campos["tipos"][] = "text";

        $this->campos["campos"][] = "e.nombre";
        $this->campos["campos"][] = "e.nit";
        $this->campos["campos"][] = "e.representante";
        $this->campos["campos"][] = "e.telefono";
        $this->campos["campos"][] = "e.email";
    }
    /**
     * Lists all entities.
     *
     */
   public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $page =  $request->query->get('page');
        if(!isset($page)){ $page = 1; }
        //----------------------------------
        $edit = "true";
        $delete = "false";
        $new = "true";
        $excel = "true";
        $pdf = "false";
        $pdfone = "false";
        $advancedsearch = "false"; 
        $completeSearch = "true";         
        $cantidadPorPagina = 20;
        $startPagination = ($page - 1) * $cantidadPorPagina;
        //----------------------------------
        $etiquetas[] = "#";
        $etiquetas[] = "Nombre";
        $etiquetas[] = "NIT";
        $etiquetas[] = "Representante";
        $etiquetas[] = "Teléfono";
        $etiquetas[] = "Email";
        $etiquetas[] = "Logo";

        $etiquetas[] = "Editar";
        
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
            $page = 1;
        }

        //----------------------------------
        $paginator  = $this->get('knp_paginator');
        $resultset = $paginator->paginate(
            $consulta,
            $page,$cantidadPorPagina
        );
        $archivo = new Archivos();
        $form = $this->createForm("Cpdg\AdministradorBundle\Form\ArchivosType", $archivo);
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
            'loadFile' => 'true',
            'form' => $form->createView(), 
        ));
    }
    /**
     * Creates a new entity.
     *
     */
    public function newAction(Request $request)
        {
        $newf = new Proveedores();
        $form = $this->createForm("Cpdg\AdministradorBundle\Form\\".$this->entityMain."CompleteType", $newf);        
        $form->handleRequest($request);
/*
        
        $ProveedoresUsuariosform = $this->createForm("Cpdg\AdministradorBundle\Form\ProveedoresUsuariosType", $ProveedoresUsuarios);*/

        if ($form->isSubmitted() && $form->isValid()) {                    
            $data = $request->request->all();
            $em = $this->getDoctrine()->getManager();

            $archivo1 = $newf->getImagen();
            if($archivo1 != ""){
                $fileName1 = md5(uniqid()).'.jpg';
                $ArchivoDir = $this->container->getParameter('kernel.root_dir').'/../web/images/proveedores/';
                $archivo1->move($ArchivoDir, $fileName1);
                $newf->setImagen($fileName1);
            }else{
                $newf->setImagen("");
            }

            $newf->setPublic(1);
            $em->persist($newf);
            $em->flush();

            $this->addFlash('success', 'Proveedor creado correctamente');
            try{
                $proveedoresUsuarios = new ProveedoresUsuarios();
                $idProveedorObj = $em->getReference('CpdgAdministradorBundle:Proveedores', $newf->getId());  
                $proveedoresUsuarios->setIdProveedor($idProveedorObj);
                $proveedoresUsuarios->setNombre($newf->getNombre());
                $proveedoresUsuarios->setTelefono($newf->getTelefono());
                $proveedoresUsuarios->setEmail($newf->getEmail());
                $proveedoresUsuarios->setContrasena($data["proveedores_complete"]["usr_contrasena"]["first"]);
                $proveedoresUsuarios->setFechaActualizacionContrasena(new \DateTime(date("Y-m-d H:i:s")));
                $proveedoresUsuarios->setProgramarActualizacionContrasena(0);
                $proveedoresUsuarios->setSuperUsuario(1);
                $proveedoresUsuarios->setPublic(1);
                //proveedores_complete[usr_nombre]
                $em->persist($proveedoresUsuarios);
                $em->flush();
            }catch(\Doctrine\DBAL\DBALException $e){
                $this->addFlash('danger', 'El email del proveedor ya esta ingresado en el sistema, para crear un usuario a este proveedor deberá hacerlo en la opción Ver Usuarios de Proveedores y usar otro email');
            }


            
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
    public function editAction(Request $request, Proveedores $postvar)
    {
        $editForm = $this->createForm("Cpdg\AdministradorBundle\Form\\".$this->entityMain."Type", $postvar);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $data = $request->request->all();
            $em = $this->getDoctrine()->getManager();

            $archivo1 = $postvar->getImagen();
            if(is_object($archivo1) == 1){
               $fileName1 = md5(uniqid()).'.jpg';
               $ArchivoDir = $this->container->getParameter('kernel.root_dir').'/../web/images/proveedores/';
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
    public function deleteAction(Request $request, Proveedores $entityvar)
    {
        $data = $request->request->all();
        $this->addFlash('success', 'Eliminado correctamente');
        $em = $this->getDoctrine()->getManager();
        $em->remove($entityvar);
        $em->flush();        
        return $this->redirectToRoute(strtolower($this->entityMain).'_index');
    }
    public function exportarExcelAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager(); 
        $data = $request->request->all();
        $query = $em->getRepository($this->baseBundle.':'.$this->entityMain)
                ->createQueryBuilder('e');

        
        $result = $query->getQuery()->getResult();

        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()
            ->setCreator("Premiatón")
            ->setLastModifiedBy("Premiatón")
            ->setTitle("Proveedores")
            ->setSubject("Premiatón")
            ->setDescription("Lista de Proveedores")
            ->setKeywords("Premiatón");

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Premiatón - Lista de Proveedores');
        
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A2', 'Nombre')
            ->setCellValue('B2', 'Nit')
            ->setCellValue('C2', 'Representante')
            ->setCellValue('D2', 'Telefono')
            ->setCellValue('E2', 'Email')
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


        // recorremos los registros obtenidos de la consulta a base de datos escribiéndolos en las celdas correspondientes
        $row = 3;
        foreach ($result as $item) {
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$row, $item->getNombre())
                ->setCellValue('B'.$row, $item->getNit())
                ->setCellValue('C'.$row, $item->getRepresentante())
                ->setCellValue('D'.$row, $item->getTelefono())
                ->setCellValue('E'.$row, $item->getEmail())
                ;
            $row++;
        }

        // se crea el writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // se crea el response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // y por último se añaden las cabeceras
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT,'Proveedores.xls');
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    public function loadFileAction(Request $request){
        $useridObj = $this->get('security.token_storage')->getToken()->getUser('Article', 1);
        $userid=$useridObj->getId();
        $user=$useridObj->getUsuario();
        $archivo = new Archivos();
        $form = $this->createForm("Cpdg\AdministradorBundle\Form\ArchivosType", $archivo);        
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $file = $archivo->getNombre();

            $fileName = md5(uniqid()).'.xls';

            $ArchivoDir = $this->container->getParameter('kernel.root_dir').'/../web/uploads';
            $file->move($ArchivoDir, $fileName);

            $archivo->setNombre($fileName);
            $archivo->setFecha(new \DateTime(date("Y-m-d H:i:s")));
            $em->persist($archivo);
            $em->flush();

            $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
            $file = $this->get('kernel')->getRootDir()."/../web/uploads/".$fileName;
            if (!file_exists($file)) {
                $this->addFlash('error', 'Archivo no cargado contactese con el administrador del sistema'); 
            }
            $objPHPExcel = \PHPExcel_IOFactory::load($file);
            $retVar = "";
            $retVarError = "";
            $dataInsert = array();
            $boolAlguno = false;
            foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                    foreach ($worksheet->getRowIterator() as $row) {
                        if($row->getRowIndex() != 1){
                            $cellIterator = $row->getCellIterator();
                            $cellIterator->setIterateOnlyExistingCells(true);
                            unset($dataInsert);
                            $dataInsert = array();
                            $boolInsert = false;
                            foreach ($cellIterator as $cell) {
                               $dataInsert[] = $cell->getCalculatedValue();
                               if (!is_null($cell)) {
                                    if($cell->getCoordinate() == "B".$row->getRowIndex()){

                                       $proveedor = $this->getDoctrine()->getRepository('CpdgAdministradorBundle:Proveedores')->createQueryBuilder('e');
                                       $proveedor->where('e.nit = :nit')->setParameter('nit', $cell->getCalculatedValue()); 
                                       $inicount = $proveedor->getQuery()->execute();
                                       $total = -1;      
                                       foreach($inicount as $repoc){ $total ++; } 
                                            if($total == 0){
                                               $boolInsert = false;
                                               $idProveedor = $repoc->getId();
                                            }else{
                                                //no lo encontro y lo ingresa como nuevo
                                               $boolInsert = true;
                                            }
                                    }
                                }
                            }
                            if($boolInsert == true){
                                if($dataInsert[0] != "" && $dataInsert[1] != "" && $dataInsert[2] != "" && $dataInsert[3] != "" && $dataInsert[4] != "" && $dataInsert[5] != "" && $dataInsert[6] != ""){
                                    $retVar .= ' Insertado Proveedor NIT: '.$dataInsert[1];
                                    $insert = new Proveedores();
                                    if($dataInsert[0] == NULL) $dataInsert[0] = "-"; else $dataInsert[0];
                                    if($dataInsert[2] == NULL) $dataInsert[2] = "-"; else $dataInsert[2];
                                    if($dataInsert[3] == NULL) $dataInsert[3] = "-"; else $dataInsert[3];
                                    if($dataInsert[4] == NULL) $dataInsert[4] = "-"; else $dataInsert[4];
                                    if($dataInsert[5] == NULL) $dataInsert[5] = "-"; else $dataInsert[5];


                                    try{
                                        $em = $this->getDoctrine()->getManager();
                                        
                                        
                                        $insert->setNombre($dataInsert[0]);
                                        $insert->setNit($dataInsert[1]);
                                        $insert->setRepresentante($dataInsert[2]);
                                        $insert->setTelefono($dataInsert[3]);
                                        //$insert->setUsuario($dataInsert[4]);
                                        //$insert->setContrasena($dataInsert[5]);
                                        $insert->setEmail($dataInsert[6]);
                                        $insert->setImagen("");
                                        $insert->setPublic(1);

                                        $em->persist($insert);
                                        $em->flush();

                                        $proveedoresUsuarios = new ProveedoresUsuarios();
                                        $idProveedorObj = $em->getReference('CpdgAdministradorBundle:Proveedores', $insert->getId());  
                                        $proveedoresUsuarios->setIdProveedor($idProveedorObj);
                                        $proveedoresUsuarios->setNombre($insert->getNombre());
                                        $proveedoresUsuarios->setTelefono($insert->getTelefono());
                                        $proveedoresUsuarios->setEmail($insert->getEmail());
                                        $proveedoresUsuarios->setContrasena($dataInsert[5]);
                                        $proveedoresUsuarios->setFechaActualizacionContrasena(new \DateTime(date("Y-m-d H:i:s")));
                                        $proveedoresUsuarios->setProgramarActualizacionContrasena(0);
                                        $proveedoresUsuarios->setSuperUsuario(1);
                                        $proveedoresUsuarios->setPublic(1);
                                        //proveedores_complete[usr_nombre]
                                        $em->persist($proveedoresUsuarios);
                                        $em->flush();
                                        $boolAlguno = true;

                                    } catch(\Doctrine\DBAL\DBALException $e) {
                                        $retVar .= ' Proveedor con codigo: '.$dataInsert[0].' ya existe.';
                                    }
                                }
                            }else{

                                if($dataInsert[0] == NULL) $dataInsert[0] = "-"; else $dataInsert[0];
                                if($dataInsert[2] == NULL) $dataInsert[2] = "-"; else $dataInsert[2];
                                if($dataInsert[3] == NULL) $dataInsert[3] = "-"; else $dataInsert[3];
                                if($dataInsert[4] == NULL) $dataInsert[4] = "-"; else $dataInsert[4];
                                if($dataInsert[5] == NULL) $dataInsert[5] = "-"; else $dataInsert[5];

                                $proveedorEm = $em->getRepository('CpdgAdministradorBundle:Proveedores')->find($idProveedor);
                                $emailToFind = $proveedorEm->getEmail();                                

                                $usuarioproveedor = $this->getDoctrine()->getRepository('CpdgAdministradorBundle:ProveedoresUsuarios')->createQueryBuilder('e')
                                    ->where('e.email = :email')->setParameter('email', $emailToFind)
                                    ->getQuery()->execute();
                                $totalb = 0;      
                                foreach($usuarioproveedor as $value){ $totalb ++; $idProveedorUsuario = $value->getId(); } 

                                //$this->addFlash('success', $idProveedorUsuario." - ".$emailToFind);

                                $proveedorEmUs = $em->getRepository('CpdgAdministradorBundle:ProveedoresUsuarios')->find($idProveedorUsuario);
                                $proveedorEmUs->setNombre($dataInsert[0]);
                                $proveedorEmUs->setTelefono($dataInsert[3]);
                                $proveedorEmUs->setEmail($dataInsert[6]);
                                $proveedorEmUs->setContrasena($dataInsert[5]);
                                $proveedorEmUs->setFechaActualizacionContrasena(new \DateTime(date("Y-m-d H:i:s")));
                                $proveedorEmUs->setProgramarActualizacionContrasena(0);
                                $proveedorEmUs->setSuperUsuario(1);

                                $em->persist($proveedorEmUs);
                                $em->flush();

                                $proveedorEm->setNombre($dataInsert[0]);
                                $proveedorEm->setNit($dataInsert[1]);
                                $proveedorEm->setRepresentante($dataInsert[2]);
                                $proveedorEm->setTelefono($dataInsert[3]);
                                $proveedorEm->setEmail($dataInsert[6]);
                                $em->persist($proveedorEm);
                                $em->flush();  


                            }
                        }
                    }
            }
            if($boolAlguno == true){
                $this->addFlash('success', 'Proveedores cargados exitosamente , la informacion ingresada cargo de forma correcta.');
                $this->processLogAction(0,$user, "Carga Proveedores: ".$retVar);
            }else{
               $this->addFlash('success', 'Los Proveedores que intenta ingresar y ya se encuentran registrados en el sistema fueron actualizados.'); 
               $this->processLogAction(0,$user, "Carga Proveedores: Los Proveedores que intenta ingresar ya se encuentran registrados en el sistema");
            }
                        
            return $this->redirectToRoute(strtolower($this->entityMain).'_index');
        }

        return $this->redirectToRoute(strtolower($this->entityMain).'_index');
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
