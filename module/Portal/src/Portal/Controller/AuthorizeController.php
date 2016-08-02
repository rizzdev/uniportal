<?php
namespace Portal\Controller;

use Application\Controller\CommonController;
use Client\Service\PortalService;
use Doctrine\ORM\EntityManager;
use Portal\Service\AuthorizeService;
use Portal\Service\GuestService;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class AuthorizeController extends CommonController
{
    protected $authorizeService;

    public function __construct(AuthorizeService $authorizeService)
    {
        $this->authorizeService = $authorizeService;
    }

    public function authorize()
    {
        $this->authorizeService->authorizeDatabase();
    }

    public function authorizedAction()
    {
        $portal = $this->currentGuestPortal();
        $authorized = $this->authorizeService->isAuthorized($portal);
        if($authorized)
            die('authorized_successful_action');
        else
            return $this->redirect()->toUrl('/portal');
    }

    public function automaticAction()
    {
        $portal = $this->currentGuestPortal();
        $result = $this->authorizeService->automaticAuthorization($portal);
        return $this->createServiceApiResponse($result);
    }

    public function buttonAction()
    {
        $portal = $this->currentGuestPortal();
        $result = $this->authorizeService->buttonAuthorization($portal);
        return $this->createServiceApiResponse($result);
    }

}
