<?php

namespace Cpdg\AdministradorBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use Cpdg\AdministradorBundle\Entity\Asociados;
use Cpdg\AdministradorBundle\Form\AsociadosType;


/**
 * Asociados controller.
 *
 */
class AsociadosController extends Controller
{
   public $baseBundle = "CpdgAdministradorBundle";
   public $entityMain = "Asociados";
   public $titulo = "Asociados";
   public $campos;

   public function __construct(){
        //----------------------------------
        $this->campos["etiquetas"][] = "Nombre Asociado";
        $this->campos["etiquetas"][] = "Nombre Drogueria";
        $this->campos["etiquetas"][] = "Código";
        $this->campos["etiquetas"][] = "Email";
        $this->campos["etiquetas"][] = "Nit";
        $this->campos["etiquetas"][] = "Ciudad";
        $this->campos["etiquetas"][] = "Departamento";

        $this->campos["tipos"][] = "text";
        $this->campos["tipos"][] = "text";
        $this->campos["tipos"][] = "text";
        $this->campos["tipos"][] = "text";
        $this->campos["tipos"][] = "text";
        $this->campos["tipos"][] = "text";
        $this->campos["tipos"][] = "text";

        $this->campos["campos"][] = "e.nombreAsociado";
        $this->campos["campos"][] = "e.nombreDrogueria";
        $this->campos["campos"][] = "e.codigo";
        $this->campos["campos"][] = "e.email";
        $this->campos["campos"][] = "e.nit";
        $this->campos["campos"][] = "e.ciudad";
        $this->campos["campos"][] = "e.departamento";
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
        $edit = "false";
        $delete = "false";
        $new = "false";
        $excel = "true";
        $pdf = "false";
        $pdfone = "false";
        $advancedsearch = "false"; 
        $completeSearch = "true";        
        $cantidadPorPagina = 20;
        $startPagination = ($page - 1) * $cantidadPorPagina;
        //----------------------------------
        $etiquetas[] = "#";
        $etiquetas[] = "Centro";
        $etiquetas[] = "Nit Asociado";
        $etiquetas[] = "Nombre Asociado";
        $etiquetas[] = "Email Asociado";
        $etiquetas[] = "Código Droguería";
        $etiquetas[] = "Nombre Droguería";
        $etiquetas[] = "Email Asociado";
        $etiquetas[] = "Ciudad";
        $etiquetas[] = "Departamento";
        $etiquetas[] = "Dirección";
        $etiquetas[] = "Teléfono";

        //$etiquetas[] = "Editar";
        
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
        $newf = new Asociados();
        $form = $this->createForm("Cpdg\AdministradorBundle\Form\\".$this->entityMain."Type", $newf);        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {                    
            $em = $this->getDoctrine()->getManager();
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
    public function editAction(Request $request, Asociados $postvar)
    {
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
    public function deleteAction(Request $request, Asociados $entityvar)
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
            ->setCellValue('A1', 'Premiatón - Lista de Asociados');
        
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A2', 'Nombre Asociado')
            ->setCellValue('B2', 'Nombre Droguería')
            ->setCellValue('C2', 'Código')
            ->setCellValue('D2', 'Email')
            ->setCellValue('E2', 'Nit')
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

        // recorremos los registros obtenidos de la consulta a base de datos escribiéndolos en las celdas correspondientes
        $row = 3;
        foreach ($result as $item) {
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$row, $item->getNombreAsociado())
                ->setCellValue('B'.$row, $item->getNombreDrogueria())
                ->setCellValue('C'.$row, $item->getCodigo())
                ->setCellValue('D'.$row, $item->getEmail())
                ->setCellValue('E'.$row, $item->getNit())
                ;
            $row++;
        }

        // se crea el writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // se crea el response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // y por último se añaden las cabeceras
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT,'Asociados.xls');
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}