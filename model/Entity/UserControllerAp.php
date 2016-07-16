<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserControllerAp
 *
 * @ORM\Table(name="user_controller_ap", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})}, indexes={@ORM\Index(name="user_controller_site", columns={"user_controller_site"})})
 * @ORM\Entity
 */
class UserControllerAp
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
     * @ORM\Column(name="mac", type="string", length=255, nullable=true)
     */
    private $mac;

    /**
     * @var string
     *
     * @ORM\Column(name="ssid", type="string", length=255, nullable=true)
     */
    private $ssid;

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
     * Set mac
     *
     * @param string $mac
     *
     * @return UserControllerAp
     */
    public function setMac($mac)
    {
        $this->mac = $mac;

        return $this;
    }

    /**
     * Get mac
     *
     * @return string
     */
    public function getMac()
    {
        return $this->mac;
    }

    /**
     * Set ssid
     *
     * @param string $ssid
     *
     * @return UserControllerAp
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
     * Set userControllerSite
     *
     * @param \Entity\UserControllerSite $userControllerSite
     *
     * @return UserControllerAp
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
