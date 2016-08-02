<?php
namespace Client\Controller;

use Application\Controller\CommonController;
use Client\Service\DashboardService;

class DashboardController extends CommonController
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function indexAction()
    {
        
        if ($this->isApiRequest()) {
            $user = $this->currentUser();
            $result = $this->dashboardService->userDashboardView($user);
            return $this->createServiceApiResponse($result);
        }

        return array();

    }
}
