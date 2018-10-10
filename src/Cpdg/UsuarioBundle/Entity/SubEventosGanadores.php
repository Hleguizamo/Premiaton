<?php

namespace Cpdg\UsuarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SubEventosGanadores
 *
 * @ORM\Table(name="sub_eventos_ganadores", indexes={@ORM\Index(name="id_proveedor", columns={"id_proveedor"}), @ORM\Index(name="id_asociado", columns={"id_asociado"}), @ORM\Index(name="id_sub_evento", columns={"id_sub_evento"})})
 * @ORM\Entity
 */
class SubEventosGanadores
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
     * @var \Proveedores
     *
     * @ORM\ManyToOne(targetEntity="Proveedores")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_proveedor", referencedColumnName="id")
     * })
     */
    private $idProveedor;

    /**
     * @var \Asociados
     *
     * @ORM\ManyToOne(targetEntity="Asociados")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_asociado", referencedColumnName="id")
     * })
     */
    private $idAsociado;

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
     * @return SubEventosGanadores
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
     * @return SubEventosGanadores
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
     * Set idProveedor
     *
     * @param \Cpdg\UsuarioBundle\Entity\Proveedores $idProveedor
     *
     * @return SubEventosGanadores
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

    /**
     * Set idAsociado
     *
     * @param \Cpdg\UsuarioBundle\Entity\Asociados $idAsociado
     *
     * @return SubEventosGanadores
     */
    public function setIdAsociado(\Cpdg\UsuarioBundle\Entity\Asociados $idAsociado = null)
    {
        $this->idAsociado = $idAsociado;

        return $this;
    }

    /**
     * Get idAsociado
     *
     * @return \Cpdg\UsuarioBundle\Entity\Asociados
     */
    public function getIdAsociado()
    {
        return $this->idAsociado;
    }

    /**
     * Set idSubEvento
     *
     * @param \Cpdg\UsuarioBundle\Entity\SubEventos $idSubEvento
     *
     * @return SubEventosGanadores
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
}
