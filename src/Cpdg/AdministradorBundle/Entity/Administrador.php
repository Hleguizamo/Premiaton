<?php

namespace Cpdg\AdministradorBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * Administrador
 *
 * @ORM\Table(name="administrador", uniqueConstraints={@ORM\UniqueConstraint(name="usuario", columns={"usuario"})}, indexes={@ORM\Index(name="id_centro", columns={"id_centro"})})
 * @ORM\Entity
 */
class Administrador implements UserInterface
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
     * @ORM\Column(name="usuario", type="string", length=30, nullable=false)
     */
    private $usuario;

    /**
     * @var string
     *
     * @ORM\Column(name="contrasena", type="string", length=30, nullable=false)
     */
    private $contrasena;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_actualizacion_contrasena", type="datetime", nullable=false)
     */
    private $fechaActualizacionContrasena;

    /**
     * @var boolean
     *
     * @ORM\Column(name="programar_actualizacion_contrasena", type="boolean", nullable=false)
     */
    private $programarActualizacionContrasena;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=50, nullable=false)
     */
    private $email;

    /**
     * @var integer
     *
     * @ORM\Column(name="telefono", type="integer", nullable=false)
     */
    private $telefono;

    /**
     * @var boolean
     *
     * @ORM\Column(name="superadmin", type="boolean", nullable=false)
     */
    private $superadmin;

    /**
     * @var boolean
     *
     * @ORM\Column(name="super_administrador", type="boolean", nullable=false)
     */
    private $superAdministrador;

    /**
     * @var boolean
     *
     * @ORM\Column(name="menu_eventos", type="boolean", nullable=false)
     */
    private $menuEventos;

    /**
     * @var boolean
     *
     * @ORM\Column(name="menu_proveedores", type="boolean", nullable=false)
     */
    private $menuProveedores;

    /**
     * @var boolean
     *
     * @ORM\Column(name="menu_asociados", type="boolean", nullable=false)
     */
    private $menuAsociados;

    /**
     * @var boolean
     *
     * @ORM\Column(name="menu_administradores", type="boolean", nullable=false)
     */
    private $menuAdministradores;

    /**
     * @var boolean
     *
     * @ORM\Column(name="menu_eventosinternos", type="boolean", nullable=false)
     */
    private $menuEventosinternos;

    /**
     * @var boolean
     *
     * @ORM\Column(name="menu_registro", type="boolean", nullable=false)
     */
    private $menuRegistro;

    /**
     * @var boolean
     *
     * @ORM\Column(name="public", type="boolean", nullable=false)
     */
    private $public;

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
     * Set usuario
     *
     * @param string $usuario
     *
     * @return Administrador
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return string
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set contrasena
     *
     * @param string $contrasena
     *
     * @return Administrador
     */
    public function setContrasena($contrasena)
    {
        $this->contrasena = $contrasena;

        return $this;
    }

    /**
     * Get contrasena
     *
     * @return string
     */
    public function getContrasena()
    {
        return $this->contrasena;
    }

    /**
     * Set fechaActualizacionContrasena
     *
     * @param \DateTime $fechaActualizacionContrasena
     *
     * @return Administrador
     */
    public function setFechaActualizacionContrasena($fechaActualizacionContrasena)
    {
        $this->fechaActualizacionContrasena = $fechaActualizacionContrasena;

        return $this;
    }

    /**
     * Get fechaActualizacionContrasena
     *
     * @return \DateTime
     */
    public function getFechaActualizacionContrasena()
    {
        return $this->fechaActualizacionContrasena;
    }

    /**
     * Set programarActualizacionContrasena
     *
     * @param boolean $programarActualizacionContrasena
     *
     * @return Administrador
     */
    public function setProgramarActualizacionContrasena($programarActualizacionContrasena)
    {
        $this->programarActualizacionContrasena = $programarActualizacionContrasena;

        return $this;
    }

    /**
     * Get programarActualizacionContrasena
     *
     * @return boolean
     */
    public function getProgramarActualizacionContrasena()
    {
        return $this->programarActualizacionContrasena;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Administrador
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
     * Set email
     *
     * @param string $email
     *
     * @return Administrador
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set telefono
     *
     * @param integer $telefono
     *
     * @return Administrador
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return integer
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set superadmin
     *
     * @param boolean $superadmin
     *
     * @return Administrador
     */
    public function setSuperadmin($superadmin)
    {
        $this->superadmin = $superadmin;

        return $this;
    }

    /**
     * Get superadmin
     *
     * @return boolean
     */
    public function getSuperadmin()
    {
        return $this->superadmin;
    }

    /**
     * Set superAdministrador
     *
     * @param boolean $superAdministrador
     *
     * @return Administrador
     */
    public function setSuperAdministrador($superAdministrador)
    {
        $this->superAdministrador = $superAdministrador;

        return $this;
    }

    /**
     * Get superAdministrador
     *
     * @return boolean
     */
    public function getSuperAdministrador()
    {
        return $this->superAdministrador;
    }

    /**
     * Set menuEventos
     *
     * @param boolean $menuEventos
     *
     * @return Administrador
     */
    public function setMenuEventos($menuEventos)
    {
        $this->menuEventos = $menuEventos;

        return $this;
    }

    /**
     * Get menuEventos
     *
     * @return boolean
     */
    public function getMenuEventos()
    {
        return $this->menuEventos;
    }

    /**
     * Set menuProveedores
     *
     * @param boolean $menuProveedores
     *
     * @return Administrador
     */
    public function setMenuProveedores($menuProveedores)
    {
        $this->menuProveedores = $menuProveedores;

        return $this;
    }

    /**
     * Get menuProveedores
     *
     * @return boolean
     */
    public function getMenuProveedores()
    {
        return $this->menuProveedores;
    }

    /**
     * Set menuAsociados
     *
     * @param boolean $menuAsociados
     *
     * @return Administrador
     */
    public function setMenuAsociados($menuAsociados)
    {
        $this->menuAsociados = $menuAsociados;

        return $this;
    }

    /**
     * Get menuAsociados
     *
     * @return boolean
     */
    public function getMenuAsociados()
    {
        return $this->menuAsociados;
    }

    /**
     * Set menuAdministradores
     *
     * @param boolean $menuAdministradores
     *
     * @return Administrador
     */
    public function setMenuAdministradores($menuAdministradores)
    {
        $this->menuAdministradores = $menuAdministradores;

        return $this;
    }

    /**
     * Get menuAdministradores
     *
     * @return boolean
     */
    public function getMenuAdministradores()
    {
        return $this->menuAdministradores;
    }

    /**
     * Set menuEventosinternos
     *
     * @param boolean $menuEventosinternos
     *
     * @return Administrador
     */
    public function setMenuEventosinternos($menuEventosinternos)
    {
        $this->menuEventosinternos = $menuEventosinternos;

        return $this;
    }

    /**
     * Get menuEventosinternos
     *
     * @return boolean
     */
    public function getMenuEventosinternos()
    {
        return $this->menuEventosinternos;
    }

    /**
     * Set menuRegistro
     *
     * @param boolean $menuRegistro
     *
     * @return Administrador
     */
    public function setMenuRegistro($menuRegistro)
    {
        $this->menuRegistro = $menuRegistro;

        return $this;
    }

    /**
     * Get menuRegistro
     *
     * @return boolean
     */
    public function getMenuRegistro()
    {
        return $this->menuRegistro;
    }

    /**
     * Set public
     *
     * @param boolean $public
     *
     * @return Administrador
     */
    public function setPublic($public)
    {
        $this->public = $public;

        return $this;
    }

    /**
     * Get public
     *
     * @return boolean
     */
    public function getPublic()
    {
        return $this->public;
    }

    /**
     * Set idCentro
     *
     * @param \Cpdg\AdministradorBundle\Entity\Centros $idCentro
     *
     * @return Administrador
     */
    public function setIdCentro(\Cpdg\AdministradorBundle\Entity\Centros $idCentro = null)
    {
        $this->idCentro = $idCentro;

        return $this;
    }

    /**
     * Get idCentro
     *
     * @return \Cpdg\AdministradorBundle\Entity\Centros
     */
    public function getIdCentro()
    {
        return $this->idCentro;
    }
    public function eraseCredentials() {
      $this->password = null;
    }

    public function getPassword() {
        return $this->getContrasena();
    }

    public function getRoles() {
        return array('ROLE_ADMINISTRATOR');
    }

    public function getSalt() {
        
    }

    public function getUsername() {
        return $this->getUsuario();
    }
}
