<?php
namespace Client\Controller;

use Application\Controller\CommonController;
use Client\Service\PortalService;

class PortalController extends CommonController
{

    protected $portalService;

    public function __construct(PortalService $portalService)
    {
        $this->portalService = $portalService;
    }

    public function indexAction()
    {
        $portals = $this->portalService->userPortalsView($this->currentUser());
        return array('data' =>  $portals);
    }

    public function createAction()
    {
        if ($this->posted()) {
            $data = $this->requirePostedJson();
            $result = $this->portalService->create($data, $this->currentUser());
            return $this->createServiceApiResponse($result);
        }

        return array();
    }

    public function viewAction()
    {
        $portal = $this->currentPortal();
        $portal = $this->portalService->view($portal);
        return array('data' => $portal);
    }

    public function updateAction()
    {
        $portal = $this->currentPortal();
        $data = $this->requirePostedJson();
        $result = $this->portalService->update($data, $portal);
        return $this->createServiceApiResponse($result);
    }
}
