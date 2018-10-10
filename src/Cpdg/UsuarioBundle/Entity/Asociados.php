<?php

namespace Cpdg\UsuarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Asociados
 *
 * @ORM\Table(name="asociados", uniqueConstraints={@ORM\UniqueConstraint(name="codigo", columns={"codigo"})}, indexes={@ORM\Index(name="id_centro", columns={"id_centro"})})
 * @ORM\Entity
 */
class Asociados
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
     * @ORM\Column(name="nombre_asociado", type="string", length=256, nullable=false)
     */
    private $nombreAsociado;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_drogueria", type="string", length=256, nullable=false)
     */
    private $nombreDrogueria;

    /**
     * @var integer
     *
     * @ORM\Column(name="codigo", type="integer", nullable=false)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=256, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="nit", type="string", length=256, nullable=false)
     */
    private $nit;

    /**
     * @var string
     *
     * @ORM\Column(name="ciudad", type="string", length=256, nullable=false)
     */
    private $ciudad;

    /**
     * @var string
     *
     * @ORM\Column(name="departamento", type="string", length=256, nullable=false)
     */
    private $departamento;

    /**
     * @var string
     *
     * @ORM\Column(name="email_drogueria", type="string", length=256, nullable=false)
     */
    private $emailDrogueria;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion", type="string", length=256, nullable=false)
     */
    private $direccion;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=128, nullable=false)
     */
    private $telefono;

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
     * Set nombreAsociado
     *
     * @param string $nombreAsociado
     *
     * @return Asociados
     */
    public function setNombreAsociado($nombreAsociado)
    {
        $this->nombreAsociado = $nombreAsociado;

        return $this;
    }

    /**
     * Get nombreAsociado
     *
     * @return string
     */
    public function getNombreAsociado()
    {
        return $this->nombreAsociado;
    }

    /**
     * Set nombreDrogueria
     *
     * @param string $nombreDrogueria
     *
     * @return Asociados
     */
    public function setNombreDrogueria($nombreDrogueria)
    {
        $this->nombreDrogueria = $nombreDrogueria;

        return $this;
    }

    /**
     * Get nombreDrogueria
     *
     * @return string
     */
    public function getNombreDrogueria()
    {
        return $this->nombreDrogueria;
    }

    /**
     * Set codigo
     *
     * @param integer $codigo
     *
     * @return Asociados
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return integer
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Asociados
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
     * Set nit
     *
     * @param string $nit
     *
     * @return Asociados
     */
    public function setNit($nit)
    {
        $this->nit = $nit;

        return $this;
    }

    /**
     * Get nit
     *
     * @return string
     */
    public function getNit()
    {
        return $this->nit;
    }

    /**
     * Set ciudad
     *
     * @param string $ciudad
     *
     * @return Asociados
     */
    public function setCiudad($ciudad)
    {
        $this->ciudad = $ciudad;

        return $this;
    }

    /**
     * Get ciudad
     *
     * @return string
     */
    public function getCiudad()
    {
        return $this->ciudad;
    }

    /**
     * Set departamento
     *
     * @param string $departamento
     *
     * @return Asociados
     */
    public function setDepartamento($departamento)
    {
        $this->departamento = $departamento;

        return $this;
    }

    /**
     * Get departamento
     *
     * @return string
     */
    public function getDepartamento()
    {
        return $this->departamento;
    }

    /**
     * Set emailDrogueria
     *
     * @param string $emailDrogueria
     *
     * @return Asociados
     */
    public function setEmailDrogueria($emailDrogueria)
    {
        $this->emailDrogueria = $emailDrogueria;

        return $this;
    }

    /**
     * Get emailDrogueria
     *
     * @return string
     */
    public function getEmailDrogueria()
    {
        return $this->emailDrogueria;
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     *
     * @return Asociados
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * Get direccion
     *
     * @return string
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set telefono
     *
     * @param string $telefono
     *
     * @return Asociados
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
     * Set idCentro
     *
     * @param \Cpdg\UsuarioBundle\Entity\Centros $idCentro
     *
     * @return Asociados
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
