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
use Cpdg\AdministradorBundle\Entity\SubEventos;

class ganadoresEventosempleadosCommand extends ContainerAwareCommand
	{
	public $conn;
    protected function configure() {
        parent::configure();
        $this->setName('sorteoganadores:eventosempleados')->setDescription('Sorteo de Ganadores para Eventos Internos.');		
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
		
		$contadorGlobal = 0;
		$fechaGlobal = date("Y-m-d");
		$horaGlobal = date("H:i:s");
		$fechaCompletaGlobal = $fechaGlobal." ".$horaGlobal;
		$queryCerrar = "SELECT * 
        		  FROM eventos_empleados 
        		  WHERE estado IN(1,9)         		  
        		  AND fecha_fin < '".$fechaGlobal."'
        		  ;";
		$runCerrar = $this->getContainer()->get('doctrine')->getConnection()->executeQuery($queryCerrar, array(), array());
		foreach($runCerrar as $lineCerrar){
			$querycerrar = "UPDATE eventos_empleados SET estado = '0' WHERE id = '".$lineCerrar["id"]."';";
			$runupdate = $this->getContainer()->get('doctrine')->getConnection()->executeQuery($querycerrar, array(), array());
		}

        $logEvento = "Inicia el sorteo\n<br>";

        $query = "SELECT * 
        		  FROM eventos_empleados 
        		  WHERE estado IN(1,9) 
        		  AND fecha_inicio <= '".$fechaGlobal."' 
        		  AND fecha_fin >= '".$fechaGlobal."'
        		  AND hora_inicio <= '".$horaGlobal."' 
        		  AND hora_fin >= '".$horaGlobal."'
        		  ;";

		$run = $this->getContainer()->get('doctrine')->getConnection()->executeQuery($query, array(), array());
		$x = 0; 
		$logEvento .= "---------------------------------------------------------------------\n<br>Eventos Empleados en el rango de ganadores:\n<br>\n<br>";
		foreach($run as $line){ 
			$queryupdate = "UPDATE eventos_empleados SET estado = '9' WHERE id = '".$line["id"]."';";
			$runupdate = $this->getContainer()->get('doctrine')->getConnection()->executeQuery($queryupdate, array(), array());

			$x ++;
			
			$sqlGanadoresEvento = "SELECT eventos_empleados_ganadores.id as idev, eventos_empleados_ganadores.fecha as fechav, eventos_empleados_ganadores.hora as horav, empleados.* 
	        		  FROM 
	        		  eventos_empleados_ganadores, empleados
	        		  WHERE eventos_empleados_ganadores.id_eventos_empleados = '".$line["id"]."' 
	        		  AND eventos_empleados_ganadores.id_empleados = empleados.id
	        		  ORDER BY eventos_empleados_ganadores.fecha,eventos_empleados_ganadores.hora asc
	        		  ;";

	        $ganadoresEvento = $this->getContainer()->get('doctrine')->getConnection()->executeQuery($sqlGanadoresEvento, array(), array());
	        $counterGanadores = 0;
	        $ganadoresExcluidost = "";
	        foreach($ganadoresEvento as $lineg){
	        	$ganadores["idev"][] = $lineg["idev"];
	        	$ganadores["nombre"][] = $lineg["nombre"];
	        	$ganadores["codigo"][] = $lineg["cedula"];
	        	$ganadoresExcluidost .= $lineg["id"].",";
	        	$fechaUltimoGanador = $lineg["fechav"]." ".$lineg["horav"];
	        	$counterGanadores++;
	        }
	          $ganadoresExcluidos = substr($ganadoresExcluidost, 0, -1);
		  
			  $logEvento .= "Evento Empleados: ".$line["nombre"]."\n<br>";
			  $logEvento .= "Cada cuanto hay sorteo: ".$line["periodicidad"]." minutos"."\n<br>";
			  $logEvento .= "Cantidad de ganadores por sorteo: ".$line["numero_ganadores"]."\n<br>";
			  $logEvento .= "Cantidad Maxima de ganadores por sorteo: ".$line["numero_maximo_ganadores"]."\n<br>";
			  $logEvento .= "Ganadores Hasta el momento: ".$counterGanadores."\n<br>";

		//aca condicional de periodicidad
		//$horaGlobal;
		$booleanPeriodicidad = "false";
		if($counterGanadores == 0){
			$fechaInicio = $line["fecha_inicio"]." ". $line["hora_inicio"];
			$nuevafecha = strtotime ( '+'.$line["periodicidad"].' minutes' , strtotime ( $fechaInicio ) ) ;
			$nuevaHora = date ( 'H:i' , $nuevafecha );
			$nuevafecha = date ( 'Y-m-d H:i:s' , $nuevafecha );

			$horaInicio = date ('H:i' , strtotime ( $fechaInicio ));
			//$output->writeln("--->".$horaInicio . "->hora ganadora: ".$nuevaHora);
			if($nuevaHora <= $horaGlobal){
				$booleanPeriodicidad = "true";
			}
		}else{
			$fechaInicio = $fechaUltimoGanador;

			$nuevafecha = strtotime ( '+'.$line["periodicidad"].' minutes' , strtotime ( $fechaInicio ) ) ;
			$nuevaHora = date ( 'H:i' , $nuevafecha );
			$nuevafecha = date ( 'Y-m-d H:i:s' , $nuevafecha );

			$horaInicio = date ('H:i' , strtotime ( $fechaInicio ));
			if($nuevaHora <= $horaGlobal){
				$booleanPeriodicidad = "true";
			}
		}
		
       // $nuevafecha = strtotime ( '-1 day' , strtotime ( $fecha ) );
        //$nuevafecha = date ( 'Y-m-d' , $nuevafecha );

		
		if($booleanPeriodicidad == "true"){
			//inicia sorteo de ganadores
			if($counterGanadores <= $line["numero_maximo_ganadores"]){
				$logEvento .= "---------------------\n<br>";
				$logEvento .= "Generando Ganadores:\n<br>";
				$logEvento .= "---------------------\n<br>";
				$privateCounterGanadores = $counterGanadores; 
				if($counterGanadores > 0){
					$logEvento .= "Lista de ganadores excluidos:\n<br>";
					$xy = 0;
					foreach ($ganadores["idev"] as $value) {
						$logEvento .= "\tExcluido: ".$ganadores["nombre"][$xy]." - Codigo: ".$ganadores["codigo"][$xy]."\n<br>";
						$xy++;
					}
				}else{
					$logEvento .= "No ha ganado ningun asociado aun\n<br>";
				}
				$logEvento .= "\t---------------------\n<br>";
				//aca condicional de periodicidad
				//if(){}

				//Generando ganadores
				for($xz = 1; $xz <= intval($line["numero_ganadores"]); $xz++){
					//condicional de la sucursal
					$addQuery = "";					
					if($ganadoresExcluidos == ""){
						if($line["id_centro"] != 1){
							$addQuery = " id_centro = '".$line["id_centro"]."'";
						}
						$selectGanadorId = "
						SELECT * FROM empleados
						WHERE 
						$addQuery
						ORDER BY RAND()
						LIMIT 1
						";
        				$output->writeln($selectGanadorId);

					}else{
						if($line["id_centro"] != 1){
							$addQuery = " AND id_centro = '".$line["id_centro"]."'";
						}
						$selectGanadorId = "
						SELECT * FROM empleados
						WHERE 
						id NOT IN ($ganadoresExcluidos)
						$addQuery
						ORDER BY RAND()
						LIMIT 1";
					}					

					$runGanadorId = $this->getContainer()->get('doctrine')->getConnection()->executeQuery($selectGanadorId, array(), array());
					//AND id_empleados NOT IN ('1,2')
					foreach ($runGanadorId as $valueg) {
						$idAsociadoGanador = $valueg["id"];
						$nombreAsociadoGanador = $valueg["nombre"];
						$codigoAsociadoGanador = $valueg["cedula"];
						//$idProveedorGanador = $valueg["id_proveedor"];
					}
					$logEvento .= "\tGanador $xz: ".$nombreAsociadoGanador." - Cedula: ".$codigoAsociadoGanador."\n<br>";

					$sqlInsertarGanador = "
					  INSERT INTO eventos_empleados_ganadores
	        		  SET
	        		  id_eventos_empleados ='".$line["id"]."',
	        		  id_empleados ='".$idAsociadoGanador."',
	        		  fecha ='".$fechaGlobal."',
	        		  hora ='".$horaGlobal."'
	        		  ;";
	        		$runInsertarGanador = $this->getContainer()->get('doctrine')->getConnection()->executeQuery($sqlInsertarGanador, array(), array());
	        		$contadorGlobal ++;

	        		if($ganadoresExcluidos == ""){
	        			$ganadoresExcluidos .= $idAsociadoGanador;
	        		}else{
	        			$ganadoresExcluidos .= ",".$idAsociadoGanador;

	        		}	        		

					$privateCounterGanadores ++;
					if($privateCounterGanadores == $line["numero_maximo_ganadores"]){
						break;
					}
				}

				//Cerrando evento cuando llega al límite de ganadores
				if($counterGanadores == $line["numero_maximo_ganadores"] || $privateCounterGanadores == $line["numero_maximo_ganadores"]){
					$logEvento .= "El evento ya ha llegado al limite de ganadores, El evento sera terminado.\n<br>";
					$queryupdateb = "UPDATE eventos_empleados SET estado = '0' WHERE id = '".$line["id"]."';";
					$runupdateb = $this->getContainer()->get('doctrine')->getConnection()->executeQuery($queryupdateb, array(), array());
				}				

			}else{
				$logEvento .= "El evento No genera ganadores ya ha llegado al limite de ganadores, El evento será terminado.\n<br>";
				$queryupdateb = "UPDATE eventos_empleados SET estado = '0' WHERE id = '".$line["id"]."';";
				$runupdateb = $this->getContainer()->get('doctrine')->getConnection()->executeQuery($queryupdateb, array(), array());
			}
		}
			$logEvento .= "---------------------------------------------------------------------"."\n<br>";
			$logEvento .= "---------------------------------------------------------------------"."\n<br>";
		} 
		$output->writeln($logEvento);
		if($contadorGlobal > 0){
			$this->processLogAction("0","CronEventos", $logEvento);	
		}
        $output->writeln("Finaliza el Sorteo");
    }
    public function processLogAction($tipoUsuario, $usuario, $mensaje)
    {
        $queryinsert = "INSERT INTO logs VALUES(NULL, '".$tipoUsuario."', '".$usuario."', '".$mensaje."', '127.0.0.1', '".date("Y-m-d H:i:s")."');";
		$runinsert = $this->getContainer()->get('doctrine')->getConnection()->executeQuery($queryinsert, array(), array());
    }	
}
?>