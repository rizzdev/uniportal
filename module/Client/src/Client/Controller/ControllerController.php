<?php
namespace Client\Controller;

use Application\Controller\CommonController;
use Client\Service\ControllerService;
use Client\Service\PortalService;
use Client\Service\SiteService;
use Doctrine\ORM\EntityManager;
use Entity\UserController;
use Entity\UserControllerAp;
use Entity\UserControllerSite;
use UniFi;
use Zend\Crypt\BlockCipher;
use Zend\View\Model\ViewModel;

class ControllerController extends CommonController
{

    protected $controllerService;
    protected $portalService;
    protected $siteService;

    public function __construct(ControllerService $controllerService, PortalService $portalService, SiteService $siteService)
    {
        $this->controllerService = $controllerService;
        $this->portalService = $portalService;
        $this->siteService = $siteService;
    }

    public function indexAction()
    {
        $controllers = $this->controllerService->userPortalsView($this->currentUser());
        return array('data' => $controllers);
    }

    public function createAction()
    {
        if ($this->posted()) {
            $data = $this->requirePostedJson();
            $result = $this->controllerService->create($data, $this->currentUser());
            return $this->createServiceApiResponse($result);
        }

        return array();
    }

    public function updateAction()
    {
        $controller = $this->currentController();
        $data = $this->requirePostedJson();
        $result = $this->controllerService->update($data, $controller);
        return $this->createServiceApiResponse($result);
    }

    public function viewAction()
    {
        $controller = $this->currentController();

        $unifi = new UniFi($controller);

        if ($unifi->login()) {
            $this->controllerService->updateLoginCookies($unifi, $controller);
            $this->controllerService->gatherSites($unifi, $controller);
            $this->controllerService->gatherAps($unifi, $controller);
        }

        $portals = $this->portalService->userPortalsView($this->currentUser());

        if ($this->isApiRequest()) {
            $result = $this->controllerService->viewDetailed($controller);
            return $this->createServiceApiResponse($result);
        }

        return array(
            'controller' => $this->controllerService->view($controller),
            'portals' => $portals
        );
    }

    public function removeAction()
    {
        return array();
    }

    public function validateAction()
    {
        $controller = $this->currentController();
        $result = $this->controllerService->validateConfiguration($controller);
        return $this->createServiceApiResponse($result);
    }

    public function visitPortalAction()
    {
        //Simply here to require authentication to view the portal
        $controller = $this->currentController();
        return $this->visitSite();
    }



}
