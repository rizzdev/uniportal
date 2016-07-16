<?php
namespace Portal\Controller;

use Application\Controller\CommonController;
use Doctrine\ORM\EntityManager;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class IndexController extends CommonController
{
    public function indexAction()
    {
        $view = new ViewModel();

        /** @var EntityManager $em */
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');

        $apContainer = new Container('ap_container');
        $controller = $em->getRepository('\Entity\UserControllerAp')->findOneBy(array('mac' => $apContainer->ap));

        if(is_object($controller))
        {
            $view->setVariable('data', array(
                'template' => 'first',
                'mac' => $controller->getMac()
            ));
        }
        else
        {
            return $this->redirect()->toUrl('/unknown');
        }

        $view->setTerminal(1);
        return $view;
    }

    public function authorizeAction()
    {
        /** @var EntityManager $em */
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');

        $apContainer = new Container('ap_container');
        $ap = $em->getRepository('\Entity\UserControllerAp')->findOneBy(array('mac' => $apContainer->ap));

        if(is_object($ap))
        {
            $unifi = new \UniFi($ap->getUserControllerSite()->getUserController());

            if ($unifi->login()) {

                $authorized = $unifi->authorize_guest($apContainer->offsetGet('mac'), 60);

                if ($authorized) {

                    $connection = $em->getRepository('\Entity\PortalConnect')->findOneBy(array('connectionInitial' => $apContainer->offsetGet('t')));
                    $connection->setConnectionSuccesful(new \DateTime('now'));
                    $em->persist($connection);
                    $em->flush();

                    return new JsonModel(array(
                        'success' => true,
                        'message' => 'Connected to network'
                    ));
                }

                return new JsonModel(array(
                    'success' => false,
                    'message' => 'Failed to connect to network [FAILED_CLIENT_AUTHORIZE]'
                ));
            }

            return new JsonModel(array(
                'success' => false,
                'message' => 'Failed to connect to network [FAILED_CONTROLLER_LOGIN]'
            ));

        }

        return new JsonModel(array(
            'success' => false,
            'message' => 'Failed to connect to network [FAILED_CONTROLLER_COMPILE]'
        ));

    }
}
