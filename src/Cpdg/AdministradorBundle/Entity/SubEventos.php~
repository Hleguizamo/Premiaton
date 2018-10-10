<?php

namespace Cpdg\AdministradorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SubEventos
 *
 * @ORM\Table(name="sub_eventos", indexes={@ORM\Index(name="id_evento", columns={"id_evento"}), @ORM\Index(name="id_administrador", columns={"id_administrador"})})
 * @ORM\Entity
 */
class SubEventos
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
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=1024, nullable=false)
     */
    private $nombre;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_creacion", type="date", nullable=false)
     */
    private $fechaCreacion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_inicio", type="date", nullable=false)
     */
    private $fechaInicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_fin", type="date", nullable=false)
     */
    private $fechaFin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="hora_inicio", type="time", nullable=false)
     */
    private $horaInicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="hora_fin", type="time", nullable=false)
     */
    private $horaFin;

    /**
     * @var integer
     *
     * @ORM\Column(name="periodicidad", type="integer", nullable=false)
     */
    private $periodicidad;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero_ganadores", type="integer", nullable=false)
     */
    private $numeroGanadores;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero_maximo_ganadores", type="integer", nullable=false)
     */
    private $numeroMaximoGanadores;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero_proveedores", type="integer", nullable=false)
     */
    private $numeroProveedores;

    /**
     * @var string
     *
     * @ORM\Column(name="imagen", type="string", length=256, nullable=false)
     */
    private $imagen;

    /**
     * @var string
     *
     * @ORM\Column(name="premio", type="string", length=1024, nullable=false)
     */
    private $premio;

    /**
     * @var string
     *
     * @ORM\Column(name="valor", type="string", length=256, nullable=false)
     */
    private $valor;

    /**
     * @var boolean
     *
     * @ORM\Column(name="mostrar_valor", type="boolean", nullable=false)
     */
    private $mostrarValor;

    /**
     * @var boolean
     *
     * @ORM\Column(name="repite_proveedor", type="boolean", nullable=false)
     */
    private $repiteProveedor;

    /**
     * @var integer
     *
     * @ORM\Column(name="estado", type="integer", nullable=false)
     */
    private $estado;

    /**
     * @var \Eventos
     *
     * @ORM\ManyToOne(targetEntity="Eventos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_evento", referencedColumnName="id")
     * })
     */
    private $idEvento;

    /**
     * @var \Administrador
     *
     * @ORM\ManyToOne(targetEntity="Administrador")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_administrador", referencedColumnName="id")
     * })
     */
    private $idAdministrador;



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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return SubEventos
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return SubEventos
     */
    public function setFechaCreacion($fechaCreacion)
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    /**
     * Get fechaCreacion
     *
     * @return \DateTime
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    /**
     * Set fechaInicio
     *
     * @param \DateTime $fechaInicio
     *
     * @return SubEventos
     */
    public function setFechaInicio($fechaInicio)
    {
        $this->fechaInicio = $fechaInicio;

        return $this;
    }

    /**
     * Get fechaInicio
     *
     * @return \DateTime
     */
    public function getFechaInicio()
    {
        return $this->fechaInicio;
    }

    /**
     * Set fechaFin
     *
     * @param \DateTime $fechaFin
     *
     * @return SubEventos
     */
    public function setFechaFin($fechaFin)
    {
        $this->fechaFin = $fechaFin;

        return $this;
    }

    /**
     * Get fechaFin
     *
     * @return \DateTime
     */
    public function getFechaFin()
    {
        return $this->fechaFin;
    }

    /**
     * Set horaInicio
     *
     * @param \DateTime $horaInicio
     *
     * @return SubEventos
     */
    public function setHoraInicio($horaInicio)
    {
        $this->horaInicio = $horaInicio;

        return $this;
    }

    /**
     * Get horaInicio
     *
     * @return \DateTime
     */
    public function getHoraInicio()
    {
        return $this->horaInicio;
    }

    /**
     * Set horaFin
     *
     * @param \DateTime $horaFin
     *
     * @return SubEventos
     */
    public function setHoraFin($horaFin)
    {
        $this->horaFin = $horaFin;

        return $this;
    }

    /**
     * Get horaFin
     *
     * @return \DateTime
     */
    public function getHoraFin()
    {
        return $this->horaFin;
    }

    /**
     * Set periodicidad
     *
     * @param integer $periodicidad
     *
     * @return SubEventos
     */
    public function setPeriodicidad($periodicidad)
    {
        $this->periodicidad = $periodicidad;

        return $this;
    }

    /**
     * Get periodicidad
     *
     * @return integer
     */
    public function getPeriodicidad()
    {
        return $this->periodicidad;
    }

    /**
     * Set numeroGanadores
     *
     * @param integer $numeroGanadores
     *
     * @return SubEventos
     */
    public function setNumeroGanadores($numeroGanadores)
    {
        $this->numeroGanadores = $numeroGanadores;

        return $this;
    }

    /**
     * Get numeroGanadores
     *
     * @return integer
     */
    public function getNumeroGanadores()
    {
        return $this->numeroGanadores;
    }

    /**
     * Set numeroMaximoGanadores
     *
     * @param integer $numeroMaximoGanadores
     *
     * @return SubEventos
     */
    public function setNumeroMaximoGanadores($numeroMaximoGanadores)
    {
        $this->numeroMaximoGanadores = $numeroMaximoGanadores;

        return $this;
    }

    /**
     * Get numeroMaximoGanadores
     *
     * @return integer
     */
    public function getNumeroMaximoGanadores()
    {
        return $this->numeroMaximoGanadores;
    }

    /**
     * Set numeroProveedores
     *
     * @param integer $numeroProveedores
     *
     * @return SubEventos
     */
    public function setNumeroProveedores($numeroProveedores)
    {
        $this->numeroProveedores = $numeroProveedores;

        return $this;
    }

    /**
     * Get numeroProveedores
     *
     * @return integer
     */
    public function getNumeroProveedores()
    {
        return $this->numeroProveedores;
    }

    /**
     * Set imagen
     *
     * @param string $imagen
     *
     * @return SubEventos
     */
    public function setImagen($imagen)
    {
        $this->imagen = $imagen;

        return $this;
    }

    /**
     * Get imagen
     *
     * @return string
     */
    public function getImagen()
    {
        return $this->imagen;
    }

    /**
     * Set premio
     *
     * @param string $premio
     *
     * @return SubEventos
     */
    public function setPremio($premio)
    {
        $this->premio = $premio;

        return $this;
    }

    /**
     * Get premio
     *
     * @return string
     */
    public function getPremio()
    {
        return $this->premio;
    }

    /**
     * Set valor
     *
     * @param string $valor
     *
     * @return SubEventos
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return string
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set mostrarValor
     *
     * @param boolean $mostrarValor
     *
     * @return SubEventos
     */
    public function setMostrarValor($mostrarValor)
    {
        $this->mostrarValor = $mostrarValor;

        return $this;
    }

    /**
     * Get mostrarValor
     *
     * @return boolean
     */
    public function getMostrarValor()
    {
        return $this->mostrarValor;
    }

    /**
     * Set repiteProveedor
     *
     * @param boolean $repiteProveedor
     *
     * @return SubEventos
     */
    public function setRepiteProveedor($repiteProveedor)
    {
        $this->repiteProveedor = $repiteProveedor;

        return $this;
    }

    /**
     * Get repiteProveedor
     *
     * @return boolean
     */
    public function getRepiteProveedor()
    {
        return $this->repiteProveedor;
    }

    /**
     * Set estado
     *
     * @param integer $estado
     *
     * @return SubEventos
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return integer
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set idEvento
     *
     * @param \Cpdg\AdministradorBundle\Entity\Eventos $idEvento
     *
     * @return SubEventos
     */
    public function setIdEvento(\Cpdg\AdministradorBundle\Entity\Eventos $idEvento = null)
    {
        $this->idEvento = $idEvento;

        return $this;
    }

    /**
     * Get idEvento
     *
     * @return \Cpdg\AdministradorBundle\Entity\Eventos
     */
    public function getIdEvento()
    {
        return $this->idEvento;
    }

    /**
     * Set idAdministrador
     *
     * @param \Cpdg\AdministradorBundle\Entity\Administrador $idAdministrador
     *
     * @return SubEventos
     */
    public function setIdAdministrador(\Cpdg\AdministradorBundle\Entity\Administrador $idAdministrador = null)
    {
        $this->idAdministrador = $idAdministrador;

        return $this;
    }

    /**
     * Get idAdministrador
     *
     * @return \Cpdg\AdministradorBundle\Entity\Administrador
     */
    public function getIdAdministrador()
    {
        return $this->idAdministrador;
    }
}
