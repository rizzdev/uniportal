<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserControllerSite
 *
 * @ORM\Table(name="user_controller_site", uniqueConstraints={@ORM\UniqueConstraint(name="site_id", columns={"site_id"})}, indexes={@ORM\Index(name="user_controller", columns={"user_controller"}), @ORM\Index(name="portal", columns={"portal"})})
 * @ORM\Entity
 */
class UserControllerSite
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
     * @ORM\Column(name="site_name", type="string", length=255, nullable=false)
     */
    private $siteName;

    /**
     * @var string
     *
     * @ORM\Column(name="site_id", type="string", length=255, nullable=false)
     */
    private $siteId;

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
     * @var \Entity\UserController
     *
     * @ORM\ManyToOne(targetEntity="Entity\UserController")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_controller", referencedColumnName="id")
     * })
     */
    private $userController;



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
     * Set siteName
     *
     * @param string $siteName
     *
     * @return UserControllerSite
     */
    public function setSiteName($siteName)
    {
        $this->siteName = $siteName;

        return $this;
    }

    /**
     * Get siteName
     *
     * @return string
     */
    public function getSiteName()
    {
        return $this->siteName;
    }

    /**
     * Set siteId
     *
     * @param string $siteId
     *
     * @return UserControllerSite
     */
    public function setSiteId($siteId)
    {
        $this->siteId = $siteId;

        return $this;
    }

    /**
     * Get siteId
     *
     * @return string
     */
    public function getSiteId()
    {
        return $this->siteId;
    }

    /**
     * Set portal
     *
     * @param \Entity\Portal $portal
     *
     * @return UserControllerSite
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

    /**
     * Set userController
     *
     * @param \Entity\UserController $userController
     *
     * @return UserControllerSite
     */
    public function setUserController(\Entity\UserController $userController = null)
    {
        $this->userController = $userController;

        return $this;
    }

    /**
     * Get userController
     *
     * @return \Entity\UserController
     */
    public function getUserController()
    {
        return $this->userController;
    }
}
