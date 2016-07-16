<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Portal
 *
 * @ORM\Table(name="portal", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})}, indexes={@ORM\Index(name="user_controller_site", columns={"user_controller_site"}), @ORM\Index(name="owner", columns={"owner"})})
 * @ORM\Entity
 */
class Portal
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
     * @ORM\Column(name="subdomain", type="string", length=255, nullable=true)
     */
    private $subdomain;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="auth_types", type="string", length=255, nullable=false)
     */
    private $authTypes;

    /**
     * @var string
     *
     * @ORM\Column(name="header", type="blob", length=65535, nullable=true)
     */
    private $header;

    /**
     * @var \Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="owner", referencedColumnName="user_id")
     * })
     */
    private $owner;

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
     * Set subdomain
     *
     * @param string $subdomain
     *
     * @return Portal
     */
    public function setSubdomain($subdomain)
    {
        $this->subdomain = $subdomain;

        return $this;
    }

    /**
     * Get subdomain
     *
     * @return string
     */
    public function getSubdomain()
    {
        return $this->subdomain;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Portal
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set authTypes
     *
     * @param string $authTypes
     *
     * @return Portal
     */
    public function setAuthTypes($authTypes)
    {
        $this->authTypes = $authTypes;

        return $this;
    }

    /**
     * Get authTypes
     *
     * @return string
     */
    public function getAuthTypes()
    {
        return $this->authTypes;
    }

    /**
     * Set header
     *
     * @param string $header
     *
     * @return Portal
     */
    public function setHeader($header)
    {
        $this->header = $header;

        return $this;
    }

    /**
     * Get header
     *
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * Set owner
     *
     * @param \Entity\User $owner
     *
     * @return Portal
     */
    public function setOwner(\Entity\User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \Entity\User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set userControllerSite
     *
     * @param \Entity\UserControllerSite $userControllerSite
     *
     * @return Portal
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
