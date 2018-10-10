<?php

namespace Cpdg\UsuarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Eventos
 *
 * @ORM\Table(name="eventos", indexes={@ORM\Index(name="id_administrador", columns={"id_administrador"}), @ORM\Index(name="id_ciudad", columns={"id_ciudad"}), @ORM\Index(name="id_centro", columns={"id_centro"})})
 * @ORM\Entity
 */
class Eventos
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
     * @var boolean
     *
     * @ORM\Column(name="repite_asociado_proveedor", type="boolean", nullable=false)
     */
    private $repiteAsociadoProveedor;

    /**
     * @var integer
     *
     * @ORM\Column(name="estado", type="integer", nullable=false)
     */
    private $estado;

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
     * @var \Ciudades
     *
     * @ORM\ManyToOne(targetEntity="Ciudades")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_ciudad", referencedColumnName="id")
     * })
     */
    private $idCiudad;

    /**
     * @var \Centros
     *
     * @ORM\ManyToOne(targetEntity="Centros")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_centro", referencedColumnName="id")
     * })
     */
    private $idCentro;



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
     * @return Eventos
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
     * @return Eventos
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
     * @return Eventos
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
     * @return Eventos
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
     * @return Eventos
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
     * @return Eventos
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
     * @return Eventos
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
     * @return Eventos
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
     * @return Eventos
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
     * @return Eventos
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
     * @return Eventos
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
     * @return Eventos
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
     * @return Eventos
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
     * @return Eventos
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
     * @return Eventos
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
     * Set repiteAsociadoProveedor
     *
     * @param boolean $repiteAsociadoProveedor
     *
     * @return Eventos
     */
    public function setRepiteAsociadoProveedor($repiteAsociadoProveedor)
    {
        $this->repiteAsociadoProveedor = $repiteAsociadoProveedor;

        return $this;
    }

    /**
     * Get repiteAsociadoProveedor
     *
     * @return boolean
     */
    public function getRepiteAsociadoProveedor()
    {
        return $this->repiteAsociadoProveedor;
    }

    /**
     * Set estado
     *
     * @param integer $estado
     *
     * @return Eventos
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
     * Set idAdministrador
     *
     * @param \Cpdg\UsuarioBundle\Entity\Administrador $idAdministrador
     *
     * @return Eventos
     */
    public function setIdAdministrador(\Cpdg\UsuarioBundle\Entity\Administrador $idAdministrador = null)
    {
        $this->idAdministrador = $idAdministrador;

        return $this;
    }

    /**
     * Get idAdministrador
     *
     * @return \Cpdg\UsuarioBundle\Entity\Administrador
     */
    public function getIdAdministrador()
    {
        return $this->idAdministrador;
    }

    /**
     * Set idCiudad
     *
     * @param \Cpdg\UsuarioBundle\Entity\Ciudades $idCiudad
     *
     * @return Eventos
     */
    public function setIdCiudad(\Cpdg\UsuarioBundle\Entity\Ciudades $idCiudad = null)
    {
        $this->idCiudad = $idCiudad;

        return $this;
    }

    /**
     * Get idCiudad
     *
     * @return \Cpdg\UsuarioBundle\Entity\Ciudades
     */
    public function getIdCiudad()
    {
        return $this->idCiudad;
    }

    /**
     * Set idCentro
     *
     * @param \Cpdg\UsuarioBundle\Entity\Centros $idCentro
     *
     * @return Eventos
     */
    public function setIdCentro(\Cpdg\UsuarioBundle\Entity\Centros $idCentro = null)
    {
        $this->idCentro = $idCentro;

        return $this;
    }

    /**
     * Get idCentro
     *
     * @return \Cpdg\UsuarioBundle\Entity\Centros
     */
    public function getIdCentro()
    {
        return $this->idCentro;
    }
}
