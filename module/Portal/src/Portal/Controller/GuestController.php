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

class GuestController extends CommonController
{
    protected $guestService;
    protected $authorizeService;

    public function __construct(GuestService $portalService, AuthorizeService $authorizeService)
    {
        $this->authorizeService = $authorizeService;
        $this->guestService = $portalService;
    }

    public function indexAction()
    {
        $portal = $this->currentGuestPortal();

        if ($this->authorizeService->isAuthorized($portal)) {
            $this->redirect()->toUrl('/portal/authorize/authorized');
        }

        $view = new ViewModel();
        $view->setTerminal(1);

        /** @var EntityManager $em */
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');

        $apContainer = new Container('ap_container');

        $ap = $em->getRepository('\Entity\UserControllerAp')->findOneBy(array('mac' => $apContainer->ap));

        if(is_object($ap)) {

            $portal = $ap->getUserControllerSite()->getUserController()->getPortal();

            if (is_object($portal)) {
                return $view->setVariable('data', array(
                    'template' => 'first',
                    'guest' => $this->guestService->renderGuestPortal($portal)
                ));
            }
        }

        return $view->setVariable('data', array(
            'template' => 'maintenance',
        ));
    }

}
