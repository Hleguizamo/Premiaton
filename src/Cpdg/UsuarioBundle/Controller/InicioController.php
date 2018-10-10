<?php

namespace Cpdg\UsuarioBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Cpdg\UsuarioBundle\Entity\Logs;
use Symfony\Component\HttpFoundation\RedirectResponse;

class InicioController extends Controller
{    
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $useridObj = $this->get('security.token_storage')->getToken()->getUser('Article', 1);
        $userid=$useridObj->getId();
        $fechaActualizacion=$useridObj->getFechaActualizacionContrasena();

        $fecha = $fechaActualizacion->format("Y-m-d");
        $nuevafecha = strtotime ( '+90 day' , strtotime ( $fecha ) ) ;
        $nuevafecha = date ( 'Y-m-d' , $nuevafecha );
        
        if($fecha >= $nuevafecha || $fecha < "2016-01-01"){
            $this->addFlash('danger', "Su contraseña ha caducado.");
            $uri = $this->container->get('router')->generate('proveedoresusuariosusr_updateContrasena', array('id'=>$userid));
            return new RedirectResponse($uri);
        }
        
        return $this->render('CpdgUsuarioBundle:inicio:index.html.twig', array(
            'titulo' => "Inicio",
            'namevar' => "inicio",
                ));
    }
    public function loginAction(Request $request)
    {
        $data = $request->request->all();
        $sendPwdvar = "0";

        if(isset($data["sendPwdForm"])){
            $consulta = $this->getDoctrine()->getRepository("CpdgUsuarioBundle:ProveedoresUsuarios")->createQueryBuilder('e')
            ->andWhere('e.email LIKE :email')->setParameter('email', $data["email"])
            ->getQuery()->execute();

            $x = 0; foreach ($consulta as $value) {
                $x ++;
                $email = $value->getEmail();
                $nombre = $value->getNombre();
                $usuario = $value->getEmail();
                $contrasena = $value->getContrasena();
            }
            if($x == 0){
                $sendPwdvar = "NO";
            }else{

                $sendPwdvar = "1";
                $message = \Swift_Message::newInstance()
                ->setSubject('Su Contraseña en PREMIATÓN')
                ->setFrom('noreply@premiaton.com')
                ->setTo($email)
                ->setBody(
                    $this->renderView(
                        'CpdgUsuarioBundle:inicio:emailSubmit.html.twig',
                        array(
                            'nombre' => $nombre,
                            'usuario' => $usuario,
                            'email' => $email,
                            'contrasena' => $contrasena,                            
                            )
                    ),
                    'text/html'
                );
                $this->get('mailer')->send($message);
            }
        }

        return $this->render('CpdgUsuarioBundle:inicio:login.html.twig', array(
            'sendPwd' => $sendPwdvar,
                ));
    }
	public function logoutAction()
    {
    
    }
    public function logoutBridgeAction()
    {
        $useridObj = $this->get('security.token_storage')->getToken()->getUser('Article', 1);
        $userid=$useridObj->getId();
        $this->processLogAction(1,$userid, "Finaliza la Sesión");
        $uri = $this->container->get('router')->generate('usuario_inicio_logout');
        return new RedirectResponse($uri);
    }
    public function loginErrorAction()
    {       
        if(isset($_GET['u'])){
            $user = $_GET['u'];
        }else{
            $user = "Anónimo";
        }        
        $this->processLogAction(1,$user, "Inicio de Sesión Fallido");
        $this->addFlash('danger', 'Usuario y/o Contraseña Invalidos, por favor intente nuevamente el ingreso.');
        return $this->render('CpdgUsuarioBundle:inicio:login.html.twig');
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
