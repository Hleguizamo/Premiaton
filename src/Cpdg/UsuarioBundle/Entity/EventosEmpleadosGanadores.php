<?php

namespace Cpdg\UsuarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EventosEmpleadosGanadores
 *
 * @ORM\Table(name="eventos_empleados_ganadores", indexes={@ORM\Index(name="id_evento", columns={"id_eventos_empleados"}), @ORM\Index(name="id_empleado", columns={"id_empleados"})})
 * @ORM\Entity
 */
class EventosEmpleadosGanadores
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date", nullable=false)
     */
    private $fecha;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="hora", type="time", nullable=false)
     */
    private $hora;

    /**
     * @var \EventosEmpleados
     *
     * @ORM\ManyToOne(targetEntity="EventosEmpleados")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_eventos_empleados", referencedColumnName="id")
     * })
     */
    private $idEventosEmpleados;

    /**
     * @var \Empleados
     *
     * @ORM\ManyToOne(targetEntity="Empleados")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_empleados", referencedColumnName="id")
     * })
     */
    private $idEmpleados;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return EventosEmpleadosGanadores
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set hora
     *
     * @param \DateTime $hora
     *
     * @return EventosEmpleadosGanadores
     */
    public function setHora($hora)
    {
        $this->hora = $hora;

        return $this;
    }

    /**
     * Get hora
     *
     * @return \DateTime
     */
    public function getHora()
    {
        return $this->hora;
    }

    /**
     * Set idEventosEmpleados
     *
     * @param \Cpdg\UsuarioBundle\Entity\EventosEmpleados $idEventosEmpleados
     *
     * @return EventosEmpleadosGanadores
     */
    public function setIdEventosEmpleados(\Cpdg\UsuarioBundle\Entity\EventosEmpleados $idEventosEmpleados = null)
    {
        $this->idEventosEmpleados = $idEventosEmpleados;

        return $this;
    }

    /**
     * Get idEventosEmpleados
     *
     * @return \Cpdg\UsuarioBundle\Entity\EventosEmpleados
     */
    public function getIdEventosEmpleados()
    {
        return $this->idEventosEmpleados;
    }

    /**
     * Set idEmpleados
     *
     * @param \Cpdg\UsuarioBundle\Entity\Empleados $idEmpleados
     *
     * @return EventosEmpleadosGanadores
     */
    public function setIdEmpleados(\Cpdg\UsuarioBundle\Entity\Empleados $idEmpleados = null)
    {
        $this->idEmpleados = $idEmpleados;

        return $this;
    }

    /**
     * Get idEmpleados
     *
     * @return \Cpdg\UsuarioBundle\Entity\Empleados
     */
    public function getIdEmpleados()
    {
        return $this->idEmpleados;
    }
}
