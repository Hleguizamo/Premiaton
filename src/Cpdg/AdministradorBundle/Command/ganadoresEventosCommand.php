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
use Cpdg\AdministradorBundle\Entity\Eventos;

class ganadoresEventosCommand extends ContainerAwareCommand
	{
	public $conn;
    protected function configure() {
        parent::configure();
        $this->setName('sorteoganadores:evento')->setDescription('Sorteo de Ganadores para Evento.');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

		$contadorGlobal = 0;

		$fechaGlobal = date("Y-m-d");
		$horaGlobal = date("H:i:s");
		$fechaCompletaGlobal = $fechaGlobal." ".$horaGlobal;
		$queryCerrar = "SELECT *
        		  FROM eventos
        		  WHERE estado IN(1,9)
        		  AND fecha_fin <= '".$fechaGlobal." ".$horaGlobal."'
        		  ;";
		$runCerrar = $this->getContainer()->get('doctrine')->getConnection()->executeQuery($queryCerrar, array(), array());
		foreach($runCerrar as $lineCerrar){
			$querycerrar = "UPDATE eventos SET estado = '0' WHERE id = '".$lineCerrar["id"]."';";
			$runupdate = $this->getContainer()->get('doctrine')->getConnection()->executeQuery($querycerrar, array(), array());
		}

        $logEvento = "Inicia el sorteo\n<br>";

        $query = "SELECT *
        		  FROM eventos
        		  WHERE estado IN(1,9)
        		  AND fecha_inicio <= '".$fechaGlobal." ".$horaGlobal."'
        		  AND fecha_fin >= '".$fechaGlobal." ".$horaGlobal."'
        		  ;";

		$run = $this->getContainer()->get('doctrine')->getConnection()->executeQuery($query, array(), array());
		$x = 0;
		$logEvento .= "---------------------------------------------------------------------\n<br>Eventos en el rango de ganadores:\n<br>\n<br>";
		$contadorEvento = 0;
		foreach($run as $line){
$output->writeln("se procesa el evento ".$line["id"]);
			$queryupdate = "UPDATE eventos SET estado = '9' WHERE id = '".$line["id"]."';";
			$runupdate = $this->getContainer()->get('doctrine')->getConnection()->executeQuery($queryupdate, array(), array());

			$sqlSorteo = "SELECT *
	        		  FROM
	        		  sorteos
	        		  WHERE
	        		  id_evento = '".$line["id"]."'
	        		  AND tipo = '1'
	        		  ORDER BY fecha_creacion DESC
	        		  LIMIT 0,1
	        		  ;";
	        $datosSorteo = $this->getContainer()->get('doctrine')->getConnection()->executeQuery($sqlSorteo, array(), array());
	        foreach($datosSorteo as $lines){ $idSorteo = $lines["id"]; }
$output->writeln("se procesa el sorteo ".$idSorteo);
			$x ++;

			$sqlGanadoresEvento = "SELECT
					  eventos_ganadores.id as idev,
					  eventos_ganadores.id_proveedor as idprov,
					  eventos_ganadores.fecha as fechav,
					  eventos_ganadores.hora as horav,
					  asociados.*
	        		  FROM
	        		  eventos_ganadores, asociados
	        		  WHERE eventos_ganadores.id_evento = '".$line["id"]."'
	        		  AND eventos_ganadores.id_asociado = asociados.id
	        		  ORDER BY eventos_ganadores.fecha,eventos_ganadores.hora asc
	        		  ;";
	        $ganadoresEvento = $this->getContainer()->get('doctrine')->getConnection()->executeQuery($sqlGanadoresEvento, array(), array());
	        $counterGanadores = 0;
	        $ganadoresExcluidost = "";
	        $nitGanadoresExcluidost = "";
	        $proveedoresExcluidost = "";
	        foreach($ganadoresEvento as $lineg){
$output->writeln("se excluye el ganador ".$lineg["codigo"]);
	        	$ganadores["idev"][] = $lineg["idev"];
	        	$ganadores["nombre"][] = $lineg["nombre_asociado"];
	        	$ganadores["codigo"][] = $lineg["codigo"];
	        	$ganadoresExcluidost .= $lineg["id"].",";
	        	$proveedoresExcluidost .= $lineg["idprov"].",";
	        	$nitGanadoresExcluidost .= "'".$lineg["nit"]."',";
	        	$fechaUltimoGanador = $lineg["fechav"]." ".$lineg["horav"];
	        	$counterGanadores++;
	        }
	          $ganadoresExcluidos = substr($ganadoresExcluidost, 0, -1);
	          $proveedoresExcluidos = substr($proveedoresExcluidost, 0, -1);
	          $nitGanadoresExcluidos = substr($nitGanadoresExcluidost, 0, -1);

			  $logEvento .= "Evento: ".$line["nombre"]."\n<br>";
			  $logEvento .= "Cada cuanto hay sorteo: ".$line["periodicidad"]." minutos"."\n<br>";
			  $logEvento .= "Cantidad de ganadores por sorteo: ".$line["numero_ganadores"]."\n<br>";
			  $logEvento .= "Cantidad Maxima de ganadores por sorteo: ".$line["numero_maximo_ganadores"]."\n<br>";
			  $logEvento .= "Ganadores Hasta el momento: ".$counterGanadores."\n<br>";

		//aca condicional de periodicidad
		//$horaGlobal;
		$booleanPeriodicidad = "false";
		if($counterGanadores == 0){
			$fechaInicio = $line["fecha_inicio"];
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
$output->writeln("generando ganadores ");
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
$output->writeln("buscando ganador ".$xz);
					$addsqlGanadorProveedor = "";
					if($xz != 1){
						if(!isset($idProveedorGanador)){ $idProveedorGanador = 0; }
						$addsqlGanadorProveedor = " AND eventos_inscripciones.id_proveedor = '".$idProveedorGanador."'  ";
					}
					if($ganadoresExcluidos == ""){
						$selectGanadorId = "
						SELECT eventos_inscripciones.*, asociados.nombre_asociado, asociados.codigo, asociados.nit
						FROM eventos_inscripciones, asociados
						WHERE
						id_evento = '".$line["id"]."'
						AND id_sorteo = '".$idSorteo."'
						$addsqlGanadorProveedor
						AND eventos_inscripciones.id_asociado = asociados.id
						ORDER BY RAND()
						LIMIT 1
					";
					}else{
						$addsql = "";
						if($line["repite_proveedor"] == 0){
							if($proveedoresExcluidos != ""){
								$addsql = " AND eventos_inscripciones.id_proveedor NOT IN ($proveedoresExcluidos) ";
							}
						}
						$selectGanadorId = "
						SELECT eventos_inscripciones.*, asociados.nombre_asociado, asociados.codigo, asociados.nit
						FROM eventos_inscripciones, asociados
						WHERE
						id_evento = '".$line["id"]."'
						AND id_sorteo = '".$idSorteo."'
						$addsqlGanadorProveedor
						AND eventos_inscripciones.id_asociado = asociados.id
						AND eventos_inscripciones.id_asociado NOT IN ($ganadoresExcluidos)
						AND asociados.nit NOT IN ($nitGanadoresExcluidos)
						$addsql
						ORDER BY RAND()
						LIMIT 1
					";
					}
$output->writeln($selectGanadorId);
					$runGanadorId = $this->getContainer()->get('doctrine')->getConnection()->executeQuery($selectGanadorId, array(), array());

					$counterInsert = 0;
					foreach ($runGanadorId as $valueg) {
						$idAsociadoGanador = $valueg["id_asociado"];
						$nitAsociadoGanador = $valueg["nit"];
						$nombreAsociadoGanador = $valueg["nombre_asociado"];
						$codigoAsociadoGanador = $valueg["codigo"];
						$idProveedorGanador = $valueg["id_proveedor"];
						$counterInsert ++;
					}
					if($counterInsert != 0){
						$contadorEvento ++;
						$logEvento .= "\tGanador $xz: ".$nombreAsociadoGanador." - Codigo: ".$codigoAsociadoGanador." - NIT: ".$nitAsociadoGanador."\n<br>";
$output->writeln("se inserta el ganador ".$codigoAsociadoGanador);
						$sqlInsertarGanador = "
						  INSERT INTO eventos_ganadores
		        		  SET
		        		  id_evento ='".$line["id"]."',
		        		  id_sorteo ='".$idSorteo."',
		        		  id_proveedor ='".$idProveedorGanador."',
		        		  id_asociado ='".$idAsociadoGanador."',
		        		  fecha ='".$fechaGlobal."',
		        		  hora ='".$horaGlobal."'
		        		  ;";
		        		$runInsertarGanador = $this->getContainer()->get('doctrine')->getConnection()->executeQuery($sqlInsertarGanador, array(), array());
		        		$contadorGlobal ++;
$output->writeln($sqlInsertarGanador);
		        		if($ganadoresExcluidos == ""){
		        			$ganadoresExcluidos .= $idAsociadoGanador;
		        			$nitGanadoresExcluidos .= "'".$nitAsociadoGanador."'";
		        		}else{
		        			$ganadoresExcluidos .= ",".$idAsociadoGanador;
		        			$nitGanadoresExcluidos .= ",'".$nitAsociadoGanador."'";
		        		}
						$privateCounterGanadores ++;
						if($privateCounterGanadores == $line["numero_maximo_ganadores"]){
							break;
						}
					}
				}

$output->writeln("se cierra el sorteo ".$idSorteo);
				$queryupdatesorteo = "UPDATE sorteos SET estado = '1', fecha_cierre ='".date("Y-m-d H:i:s")."', response ='".$logEvento."' WHERE id = '".$idSorteo."';";
				$runupdatesorteo = $this->getContainer()->get('doctrine')->getConnection()->executeQuery($queryupdatesorteo, array(), array());
$output->writeln($queryupdatesorteo);
				$sqlInsertarNuevoSorteo = "
					  INSERT INTO sorteos
	        		  SET
	        		  id_evento ='".$line["id"]."',
	        		  tipo ='1',
	        		  estado ='0',
	        		  fecha_creacion ='".date("Y-m-d H:i:s")."',
	        		  fecha_cierre ='".date("Y-m-d H:i:s")."',
	        		  response ='Inician las inscripciones'
	        		  ;";
	    		$runInsertarNuevoSorteo = $this->getContainer()->get('doctrine')->getConnection()->executeQuery($sqlInsertarNuevoSorteo, array(), array());
$output->writeln("se crea el nuevo sorteo ");
				//Cerrando evento cuando llega al límite de ganadores
				if($counterGanadores == $line["numero_maximo_ganadores"] || $privateCounterGanadores == $line["numero_maximo_ganadores"]){
					$logEvento .= "El evento ya ha llegado al limite de ganadores, El evento sera terminado.\n<br>";
					$queryupdateb = "UPDATE eventos SET estado = '0' WHERE id = '".$line["id"]."';";
					$runupdateb = $this->getContainer()->get('doctrine')->getConnection()->executeQuery($queryupdateb, array(), array());
				}

			}else{
				$logEvento .= "El evento No genera ganadores ya ha llegado al limite de ganadores, El evento será terminado.\n<br>";
				$queryupdateb = "UPDATE eventos SET estado = '0' WHERE id = '".$line["id"]."';";
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
