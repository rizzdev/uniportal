<?php
namespace Client\Controller;

use Application\Controller\CommonController;
use Client\Service\AccountService;
use Client\Service\DashboardService;
use Client\Service\StatService;

class StatController extends CommonController
{
    protected $statService;

    public function __construct(StatService $statService)
    {
        $this->statService = $statService;
    }

    public function portalDashboardAction()
    {
        $portal = $this->currentPortal();
        $result = $this->statService->portalDashboard($portal);
        return $this->createServiceApiResponse($result);
    }
}
