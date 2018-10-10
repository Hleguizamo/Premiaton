<?php
namespace Cpdg\AdministradorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Cpdg\AdministradorBundle\Entity\Asociados;
use Cpdg\AdministradorBundle\Entity\logs;

class cargarDrogueriasCommand extends ContainerAwareCommand
	{
	public $conn;
    protected function configure() {
        parent::configure();
        $this->setName('actualiza:droguerias')->setDescription('Actualizar Droguerias');		
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
		
        $output->writeln("Inicia la carga de Droguerias");    

        $maestro_drog = "/home/planos/maestro_drog.txt";
		$drog = fopen($maestro_drog,"rb");
		$y = 0;
		$z = 0;
		$codigos = "";
		while(($linea = fgets($drog)) !== false){
			$lineVar = explode(",", $linea);
			$output->writeln("Verificando Codigo: ".$lineVar[1]);

			$query = "SELECT nit FROM asociados WHERE codigo ='".intval($lineVar[1])."'; ";
			$run = $this->getContainer()->get('doctrine')->getConnection()->executeQuery($query, array(), array());
			$x = false; foreach($run as $line){ $x = true; }
			if($x == false){
				$z++;
				$codigos = $codigos.$lineVar[1].", ";

				$centrovar = substr($lineVar[15], 0,1);
				if($centrovar==4){
					$centrovar=5;
				}			
				$queryinsert = "INSERT INTO asociados VALUES(
								NULL, 
								'".$centrovar."', 
								'".$lineVar[6]."', 
								'".$lineVar[3]."', 
								'".$lineVar[1]."', 
								'".$lineVar[19]."', 
								'".$lineVar[4]."', 
								'".$lineVar[10]."', 
								'".$lineVar[13]."',
								'".$lineVar[20]."',
								'".$lineVar[8]."',
								'".$lineVar[14]."');";
				$runinsert = $this->getContainer()->get('doctrine')->getConnection()->executeQuery($queryinsert, array(), array());
				$output->writeln("Codigo: ".intval($lineVar[1])." NO encontrado, se ha realizado la insersion del registro");
			}else{
				$z++;
				$codigos = $codigos.$lineVar[1].", ";

				$centrovar = substr($lineVar[15], 0,1);
				if($centrovar==4){
					$centrovar=5;
				}			
				$queryinsert = "UPDATE 
								asociados 
								SET 
								id_centro = '".$centrovar."', 
								nombre_asociado = '".$lineVar[6]."', 
								nombre_drogueria = '".$lineVar[3]."', 
								codigo = '".$lineVar[1]."', 
								email = '".$lineVar[19]."', 
								nit = '".$lineVar[4]."', 
								ciudad = '".$lineVar[10]."', 
								departamento = '".$lineVar[13]."',
								email_drogueria = '".$lineVar[20]."',
								direccion = '".$lineVar[8]."',
								telefono = '".$lineVar[14]."'
								WHERE codigo = '".$lineVar[1]."'
								;";
				$runinsert = $this->getContainer()->get('doctrine')->getConnection()->executeQuery($queryinsert, array(), array());
				$output->writeln("Codigo: ".intval($lineVar[1])." encontrado, se han actualizado los datos");
			}
			$y ++;
			//if($y == 10){ break;} //Interrupción para pruebas
		}
		//$this->processLogAction("0","Cron", "Carga Droguerias el Cron Total: $z - Codigos: ($codigos)");	
        $output->writeln("Tarea Finalizada");
    }
    public function processLogAction($tipoUsuario, $usuario, $mensaje)
    {
        $queryinsert = "INSERT INTO logs VALUES(NULL, '".$tipoUsuario."', '".$usuario."', '".$mensaje."', '127.0.0.1', '".date("Y-m-d H:i:s")."');";
		$runinsert = $this->getContainer()->get('doctrine')->getConnection()->executeQuery($queryinsert, array(), array());
    }	
}
?>