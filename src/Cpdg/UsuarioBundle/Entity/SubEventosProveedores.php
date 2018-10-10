<?php

namespace Cpdg\UsuarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SubEventosProveedores
 *
 * @ORM\Table(name="sub_eventos_proveedores", indexes={@ORM\Index(name="id_evento", columns={"id_sub_evento"}), @ORM\Index(name="id_proveedor", columns={"id_proveedor"})})
 * @ORM\Entity
 */
class SubEventosProveedores
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
     * @ORM\Column(name="fecha", type="datetime", nullable=false)
     */
    private $fecha;

    /**
     * @var \SubEventos
     *
     * @ORM\ManyToOne(targetEntity="SubEventos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_sub_evento", referencedColumnName="id")
     * })
     */
    private $idSubEvento;

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
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return SubEventosProveedores
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
     * Set idSubEvento
     *
     * @param \Cpdg\UsuarioBundle\Entity\SubEventos $idSubEvento
     *
     * @return SubEventosProveedores
     */
    public function setIdSubEvento(\Cpdg\UsuarioBundle\Entity\SubEventos $idSubEvento = null)
    {
        $this->idSubEvento = $idSubEvento;

        return $this;
    }

    /**
     * Get idSubEvento
     *
     * @return \Cpdg\UsuarioBundle\Entity\SubEventos
     */
    public function getIdSubEvento()
    {
        return $this->idSubEvento;
    }

    /**
     * Set idProveedor
     *
     * @param \Cpdg\UsuarioBundle\Entity\Proveedores $idProveedor
     *
     * @return SubEventosProveedores
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
}
