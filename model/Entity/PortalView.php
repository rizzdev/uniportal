<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PortalView
 *
 * @ORM\Table(name="portal_view", indexes={@ORM\Index(name="user_controller_site", columns={"user_controller_site"})})
 * @ORM\Entity
 */
class PortalView
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
     * @ORM\Column(name="ip", type="string", length=255, nullable=false)
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="client_mac", type="string", length=255, nullable=false)
     */
    private $clientMac;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    private $timestamp;

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
     * Set ip
     *
     * @param string $ip
     *
     * @return PortalView
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set clientMac
     *
     * @param string $clientMac
     *
     * @return PortalView
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
     * Set timestamp
     *
     * @param \DateTime $timestamp
     *
     * @return PortalView
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get timestamp
     *
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Set userControllerSite
     *
     * @param \Entity\UserControllerSite $userControllerSite
     *
     * @return PortalView
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
