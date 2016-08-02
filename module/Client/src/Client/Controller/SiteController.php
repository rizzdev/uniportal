<?php
namespace Client\Controller;

use Application\Controller\CommonController;
use Client\Service\AccountService;
use Client\Service\DashboardService;
use Client\Service\SiteService;

class SiteController extends CommonController
{
    protected $siteService;

    public function __construct(SiteService $siteService)
    {
        $this->siteService = $siteService;
    }

    public function updatePortalAction()
    {
        $data = $this->requirePostedJson();
        $site = $this->currentSite();
        $result = $this->siteService->updatePortal($data, $site);
        return $this->createServiceApiResponse($result);
    }

    public function validateAction()
    {
        $site = $this->currentSite();
        $result = $this->siteService->validateSite($site);
        return $this->createServiceApiResponse($result);
    }


}
