<?php

namespace Cpdg\AdministradorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EventosGanadoresAleatoriosProveedores
 *
 * @ORM\Table(name="eventos_ganadores_aleatorios_proveedores", indexes={@ORM\Index(name="id_evento", columns={"id_evento"}), @ORM\Index(name="id_proveedor", columns={"id_proveedor"})})
 * @ORM\Entity
 */
class EventosGanadoresAleatoriosProveedores
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
     * @var \Eventos
     *
     * @ORM\ManyToOne(targetEntity="Eventos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_evento", referencedColumnName="id")
     * })
     */
    private $idEvento;

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
     * @return EventosGanadoresAleatoriosProveedores
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
     * @return EventosGanadoresAleatoriosProveedores
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
     * Set idEvento
     *
     * @param \Cpdg\AdministradorBundle\Entity\Eventos $idEvento
     *
     * @return EventosGanadoresAleatoriosProveedores
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
     * Set idProveedor
     *
     * @param \Cpdg\AdministradorBundle\Entity\Proveedores $idProveedor
     *
     * @return EventosGanadoresAleatoriosProveedores
     */
    public function setIdProveedor(\Cpdg\AdministradorBundle\Entity\Proveedores $idProveedor = null)
    {
        $this->idProveedor = $idProveedor;

        return $this;
    }

    /**
     * Get idProveedor
     *
     * @return \Cpdg\AdministradorBundle\Entity\Proveedores
     */
    public function getIdProveedor()
    {
        return $this->idProveedor;
    }
}
