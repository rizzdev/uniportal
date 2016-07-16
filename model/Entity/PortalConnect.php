<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PortalConnect
 *
 * @ORM\Table(name="portal_connect", indexes={@ORM\Index(name="user_controller_ap", columns={"user_controller_site"})})
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
     * @var \Entity\UserControllerSite
     *
     * @ORM\ManyToOne(targetEntity="Entity\UserControllerSite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_controller_site", referencedColumnName="id")
     * })
     */
    private $userControllerSite;



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
     * Set userControllerSite
     *
     * @param \Entity\UserControllerSite $userControllerSite
     *
     * @return PortalConnect
     */
    public function setUserControllerSite(\Entity\UserControllerSite $userControllerSite = null)
    {
        $this->userControllerSite = $userControllerSite;

        return $this;
    }

    /**
     * Get userControllerSite
     *
     * @return \Entity\UserControllerSite
     */
    public function getUserControllerSite()
    {
        return $this->userControllerSite;
    }
}
