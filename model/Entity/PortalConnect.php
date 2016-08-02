<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PortalConnect
 *
 * @ORM\Table(name="portal_connect", indexes={@ORM\Index(name="user_controller_ap", columns={"portal"})})
 * @ORM\Entity
 */
class PortalConnect
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
     * @ORM\Column(name="client_mac", type="string", length=255, nullable=false)
     */
    private $clientMac;

    /**
     * @var string
     *
     * @ORM\Column(name="ssid", type="string", length=255, nullable=false)
     */
    private $ssid;

    /**
     * @var integer
     *
     * @ORM\Column(name="connection_initial", type="integer", nullable=false)
     */
    private $connectionInitial;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="connection_succesful", type="datetime", nullable=true)
     */
    private $connectionSuccesful;

    /**
     * @var \Entity\Portal
     *
     * @ORM\ManyToOne(targetEntity="Entity\Portal")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="portal", referencedColumnName="id")
     * })
     */
    private $portal;



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
     * Set clientMac
     *
     * @param string $clientMac
     *
     * @return PortalConnect
     */
    public function setClientMac($clientMac)
    {
        $this->clientMac = $clientMac;

        return $this;
    }

    /**
     * Get clientMac
     *
     * @return string
     */
    public function getClientMac()
    {
        return $this->clientMac;
    }

    /**
     * Set ssid
     *
     * @param string $ssid
     *
     * @return PortalConnect
     */
    public function setSsid($ssid)
    {
        $this->ssid = $ssid;

        return $this;
    }

    /**
     * Get ssid
     *
     * @return string
     */
    public function getSsid()
    {
        return $this->ssid;
    }

    /**
     * Set connectionInitial
     *
     * @param integer $connectionInitial
     *
     * @return PortalConnect
     */
    public function setConnectionInitial($connectionInitial)
    {
        $this->connectionInitial = $connectionInitial;

        return $this;
    }

    /**
     * Get connectionInitial
     *
     * @return integer
     */
    public function getConnectionInitial()
    {
        return $this->connectionInitial;
    }

    /**
     * Set connectionSuccesful
     *
     * @param \DateTime $connectionSuccesful
     *
     * @return PortalConnect
     */
    public function setConnectionSuccesful($connectionSuccesful)
    {
        $this->connectionSuccesful = $connectionSuccesful;

        return $this;
    }

    /**
     * Get connectionSuccesful
     *
     * @return \DateTime
     */
    public function getConnectionSuccesful()
    {
        return $this->connectionSuccesful;
    }

    /**
     * Set portal
     *
     * @param \Entity\Portal $portal
     *
     * @return PortalConnect
     */
    public function setPortal(\Entity\Portal $portal = null)
    {
        $this->portal = $portal;

        return $this;
    }

    /**
     * Get portal
     *
     * @return \Entity\Portal
     */
    public function getPortal()
    {
        return $this->portal;
    }
}
