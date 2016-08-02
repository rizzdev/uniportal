<?php
namespace Client\Controller;

use Application\Controller\CommonController;
use Client\Service\AccountService;
use Client\Service\DashboardService;

class AccountController extends CommonController
{
    protected $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    public function settingsAction()
    {
        return array();
    }

    public function helpAction()
    {
        return array();
    }
}
