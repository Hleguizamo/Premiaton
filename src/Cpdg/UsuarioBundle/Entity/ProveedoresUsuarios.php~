<?php

namespace Cpdg\UsuarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * ProveedoresUsuarios
 *
 * @ORM\Table(name="proveedores_usuarios", uniqueConstraints={@ORM\UniqueConstraint(name="email", columns={"email"})}, indexes={@ORM\Index(name="id_proveedor", columns={"id_proveedor"})})
 * @ORM\Entity
 */
class ProveedoresUsuarios implements UserInterface
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
     * @ORM\Column(name="nombre", type="string", length=256, nullable=false)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=32, nullable=false)
     */
    private $telefono;

    /**
     * @var string
     *
     * @ORM\Column(name="contrasena", type="string", length=256, nullable=false)
     */
    private $contrasena;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=256, nullable=false)
     */
    private $email;

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
     * @var boolean
     *
     * @ORM\Column(name="super_usuario", type="boolean", nullable=false)
     */
    private $superUsuario;

    /**
     * @var boolean
     *
     * @ORM\Column(name="public", type="boolean", nullable=false)
     */
    private $public;

    /**
     * @var \Proveedores
     *
     * @ORM\ManyToOne(targetEntity="Proveedores")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_proveedor", referencedColumnName="id")
     * })
     */
    private $idProveedor;



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
     * @return ProveedoresUsuarios
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
     * Set telefono
     *
     * @param string $telefono
     *
     * @return ProveedoresUsuarios
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return string
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set contrasena
     *
     * @param string $contrasena
     *
     * @return ProveedoresUsuarios
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
     * Set email
     *
     * @param string $email
     *
     * @return ProveedoresUsuarios
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
     * Set fechaActualizacionContrasena
     *
     * @param \DateTime $fechaActualizacionContrasena
     *
     * @return ProveedoresUsuarios
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
     * @return ProveedoresUsuarios
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
     * Set superUsuario
     *
     * @param boolean $superUsuario
     *
     * @return ProveedoresUsuarios
     */
    public function setSuperUsuario($superUsuario)
    {
        $this->superUsuario = $superUsuario;

        return $this;
    }

    /**
     * Get superUsuario
     *
     * @return boolean
     */
    public function getSuperUsuario()
    {
        return $this->superUsuario;
    }

    /**
     * Set public
     *
     * @param boolean $public
     *
     * @return ProveedoresUsuarios
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
     * Set idProveedor
     *
     * @param \Cpdg\UsuarioBundle\Entity\Proveedores $idProveedor
     *
     * @return ProveedoresUsuarios
     */
    public function setIdProveedor(\Cpdg\UsuarioBundle\Entity\Proveedores $idProveedor = null)
    {
        $this->idProveedor = $idProveedor;

        return $this;
    }

    /**
     * Get idProveedor
     *
     * @return \Cpdg\UsuarioBundle\Entity\Proveedores
     */
    public function getIdProveedor()
    {
        return $this->idProveedor;
    }
    public function eraseCredentials() {
      $this->password = null;
    }

    public function getPassword() {
        return $this->getContrasena();
    }

    public function getRoles() {
        return array('ROLE_USER');
    }

    public function getSalt() {
        
    }

    public function getUsername() {
        return $this->getEmail();
    }
}
