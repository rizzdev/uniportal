<?php

namespace Application\Controller;

use Doctrine\ORM\EntityManager;
use Entity\PortalConnect;
use Zend\Session\Container;

class IndexController extends CommonController
{
    public function indexAction()
    {
        /** @var EntityManager $em */
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');

        $accessPoint['mac'] = $this->params()->fromQuery('id');
        $accessPoint['ap'] = $this->params()->fromQuery('ap');
        $accessPoint['t'] = $this->params()->fromQuery('t');
        $accessPoint['url'] = $this->params()->fromQuery('url');
        $accessPoint['ssid'] = $this->params()->fromQuery('ssid');

        /**
         * Only for testing to bypass
         */

        $accessPoint['mac'] = 'ac:cf:85:c9:26:33';
        $accessPoint['ap'] = '44:d9:e7:02:01:e4';
        $accessPoint['t'] = '1468511482';
        $accessPoint['url'] = 'http://connectivitycheck.gstatic.com/generate_204';
        $accessPoint['ssid'] = 'TestSSID';


        $ap = $em->getRepository('\Entity\UserControllerAp')->findOneBy(array('mac' => $accessPoint['ap']));

        if (!is_object($ap)) {
            return $this->redirect()->toUrl('/unknown');
        }

        $connection = new PortalConnect();
        $connection->setClientMac($accessPoint['mac']);
        $connection->setConnectionInitial($accessPoint['t']);
        $connection->setUserControllerSite($ap->getUserControllerSite());
        $connection->setSsid($accessPoint['ssid']);
        $em->persist($connection);
        $em->flush();

        $apContainer = new Container('ap_container');
        foreach ($accessPoint as $key => $value) {
            $apContainer->offsetSet($key, $value);
        }

        return $this->redirect()->toUrl('/portal');
    }

    public function testAction()
    {
        $accessPoint['mac'] = 'ac:cf:85:c9:26:33';
        $accessPoint['ap'] = '44:d9:e7:02:01:e4';
        $accessPoint['t'] = '1468511482';
        $accessPoint['url'] = 'http://connectivitycheck.gstatic.com/generate_204';
        $accessPoint['ssid'] = 'TestSSID';

        $apContainer = new Container('ap_container');
        foreach ($accessPoint as $key => $value) {
            $apContainer->offsetSet($key, $value);
        }

        $this->redirect()->toUrl('/portal');

    }

    public function resetAction()
    {
        $apContainer = new Container('ap_container');
        $apContainer->getManager()->destroy();

        return $this->redirect()->toRoute('/');
    }
}
