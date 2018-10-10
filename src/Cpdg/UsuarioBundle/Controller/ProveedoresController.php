<?php

namespace Cpdg\UsuarioBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use Cpdg\UsuarioBundle\Entity\Proveedores;
use Cpdg\UsuarioBundle\Form\ProveedoresType;

/**
 * Proveedores controller.
 *
 */
class ProveedoresController extends Controller
{
    private $cantidadPorPagina = 20;
    /**
    * @Route("/", defaults={"page": 1}, name="proveedoresusr_index")
    * @Method("GET")
    * @Cache(smaxage="10")
    */    
    public function indexAction($page, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        if(isset($data['find'])){
            $consulta = $this->getDoctrine()->getRepository('CpdgUsuarioBundle:Proveedores')->createQueryBuilder('e');

            if($data['proveedores']['nit'] != "")
            $consulta->where('e.nit LIKE :nit')->setParameter('nit', '%'.$data['proveedores']['nit'].'%');

            if($data['proveedores']['nombre'] != "")
            $consulta->orWhere('e.nombre LIKE :nombre')->setParameter('nombre', '%'.$data['proveedores']['nombre'].'%');

            if($data['proveedores']['codigo'] != "")
            $consulta->orWhere('e.codigo LIKE :codigo')->setParameter('codigo', '%'.$data['proveedores']['codigo'].'%');

            if($data['proveedores']['representanteLegal'] != "")
            $consulta->orWhere('e.representanteLegal LIKE :representanteLegal')->setParameter('representanteLegal', '%'.$data['proveedores']['representanteLegal'].'%');

            if($data['proveedores']['emailRepresentanteLegal'] != "")
            $consulta->orWhere('e.emailRepresentanteLegal LIKE :emailRepresentanteLegal')->setParameter('emailRepresentanteLegal', '%'.$data['proveedores']['emailRepresentanteLegal'].'%');

            if($data['proveedores']['telefonoRepresentanteLegal'] != "")
            $consulta->orWhere('e.telefonoRepresentanteLegal LIKE :telefonoRepresentanteLegal')->setParameter('telefonoRepresentanteLegal', '%'.$data['proveedores']['telefonoRepresentanteLegal'].'%');
        }else{
            $consulta = $em->getRepository('CpdgUsuarioBundle:Proveedores')->findAll();
        }
        /* Modal crear nuevo */
        $proveeedor = new Proveedores();
        $formnew = $this->createForm(new ProveedoresType(), $proveeedor);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $consulta,
            $this->get('request')->query->get('page', $page),$this->cantidadPorPagina
        );
        $areas = $em->getRepository('CpdgUsuarioBundle:Areas')->findAll();
        return $this->render('CpdgUsuarioBundle:proveedores:index.html.twig', array(
            'resultset' => $pagination, 
            'page' => ($page - 1), 
            'cantidadPorPagina'=>$this->cantidadPorPagina,
            'areas'=>$areas,
            'formNew' => $formnew->createView(),
            ));
    }

    /**
     * Creates a new Proveedores entity.
     *
     */
    public function newAction(Request $request)
    {
        $insert = new Proveedores();
        $form = $this->createForm(new ProveedoresType(), $insert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$insert->setNit(4);
            try{
                $em = $this->getDoctrine()->getManager();
                $em->persist($insert);
                $em->flush();
                $this->addFlash('success', "Creado correctamente");
           } catch(\Doctrine\DBAL\DBALException $e) {
                $this->addFlash('danger', "Error: No se puede crear el registro, NIT ya ingresado en el sistema");
            }

            return $this->redirectToRoute('proveedoresusr_index', array('page' => '1'));
        }
        return $this->redirectToRoute('proveedoresusr_index', array('page' => '1'));
    }

    /**
     * Finds and displays a Proveedores entity.
     *
     */
    public function showAction(Proveedores $proveedore)
    {
        $deleteForm = $this->createDeleteForm($proveedore);

        return $this->render('proveedores/show.html.twig', array(
            'proveedore' => $proveedore,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Proveedores entity.
     *
     */
    public function editAction(Request $request, Proveedores $proveedore)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($proveedore);
        $editForm = $this->createForm(new ProveedoresType(), $proveedore);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($proveedore);
            $em->flush();

            return $this->redirectToRoute('proveedoresusr_index', array('page' => '1'));
        }
        $areas = $em->getRepository('CpdgUsuarioBundle:Areas')->findAll();
        return $this->render('CpdgUsuarioBundle:proveedores:edit.html.twig', array(
            'proveedore' => $proveedore,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'areas'=>$areas,
        ));
    }

    /**
     * Deletes a Proveedores entity.
     *
     */
    public function deleteAction(Request $request, Proveedores $proveedore)
    {
        $form = $this->createDeleteForm($proveedore);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($proveedore);
            $em->flush();
        }

        return $this->redirectToRoute('proveedoresusr_index',array('page'=>'1'));
    }

    /**
     * Creates a form to delete a Proveedores entity.
     *
     * @param Proveedores $proveedore The Proveedores entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Proveedores $proveedore)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('proveedoresusr_delete', array('id' => $proveedore->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * @Route("/exportar/excel", name="exportar_excel")
     */
    public function exportarExcelAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $useridObj = $this->get('security.token_storage')->getToken()->getUser('Article', 1);
        $userid=$useridObj->getId();
        $userIdArea=$useridObj->getIdArea();
        
       /* $query = $this->getDoctrine()->getRepository('CpdgUsuarioBundle:Contactos')->createQueryBuilder('e');
        $query->where('e.idArea = :idArea')->setParameter('idArea', $userIdArea);
        $query->leftJoin('CpdgUsuarioBundle:Proveedores', 'p', 'WITH', 'p.id = e.idProveedor');*/

        // recuperamos los elementos de base de datos que queremos exportar
        $query = $em->getRepository('CpdgUsuarioBundle:Proveedores')
            ->createQueryBuilder('e')
            ->getQuery();

        $result = $query->getResult();

        // solicitamos el servicio 'phpexcel' y creamos el objeto vacío...
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        // ...y le asignamos una serie de propiedades
        $phpExcelObject->getProperties()
            ->setCreator("Coopidrogas")
            ->setLastModifiedBy("Coopidrogas")
            ->setTitle("Contactos")
            ->setSubject("Coopidrogas")
            ->setDescription("Lista de contactos")
            ->setKeywords("Coopidrogas");

        // establecemos como hoja activa la primera, y le asignamos un título
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Coopidrogas Proveedores');
        
        // escribimos en distintas celdas del documento el título de los campos que vamos a exportar
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A2', 'Empresa / Razón Social')
            ->setCellValue('B2', 'Nit')
            ->setCellValue('C2', 'Código')
            ->setCellValue('D2', 'Representante Legal')
            ->setCellValue('E2', 'Representante Legal Email')
            ->setCellValue('F2', 'Representante Legal Teléfono')
            ;

        // fijamos un ancho a las distintas columnas
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('A')
            ->setWidth(50);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('B')
            ->setWidth(25);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('C')
            ->setWidth(15);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('D')
            ->setWidth(30);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('E')
            ->setWidth(30);
        $phpExcelObject->setActiveSheetIndex(0)
            ->getColumnDimension('F')
            ->setWidth(30);      


        // recorremos los registros obtenidos de la consulta a base de datos escribiéndolos en las celdas correspondientes
        $row = 3;
        foreach ($result as $item) {
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$row, $item->getNombre())
                ->setCellValue('B'.$row, $item->getNit())
                ->setCellValue('C'.$row, $item->getCodigo())
                ->setCellValue('D'.$row, $item->getRepresentanteLegal())
                ->setCellValue('E'.$row, $item->getEmailRepresentanteLegal())
                ->setCellValue('F'.$row, $item->getTelefonoRepresentanteLegal())
                ;
            $row++;
        }

        // se crea el writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // se crea el response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // y por último se añaden las cabeceras
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'proveedores.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
