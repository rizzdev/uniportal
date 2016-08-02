<?php

namespace Portal;

use Doctrine\ORM\EntityManager;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Zend\Session\Container;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'checkSession'), -100);
        $moduleRouteListener->attach($eventManager);
    }

    public function checkSession(MvcEvent $e)
    {
        $sm = $e->getApplication()->getServiceManager();

        $match = $e->getRouteMatch();
        $list = array('portal');

        if (!$match instanceof RouteMatch) {
            return true;
        }

        $name = $match->getMatchedRouteName();
        if (in_array($name, $list)) {

            $apContainer = new Container('ap_container');

            /** @var EntityManager $em */
            $em = $sm->get('doctrine.entitymanager.orm_default');
            $ap = $em->getRepository('\Entity\UserControllerAp')->findOneBy(array('mac' => $apContainer->ap));

            if (is_object($ap)) {

                $authorized = $em->getRepository('\Entity\GuestDevice')->findOneBy(array(
                    'mac' => $apContainer->offsetGet('mac'),
                    'portal' => $ap->getUserControllerSite()->getUserController()->getPortal()
                ));

                if (is_object($authorized)) {

                    if ($authorized->getAuthorized()) {
                        return header('Location: /portal/authorize/authorized');
                    } else {
                        
                    }

                }

            }
        }

        return true;

    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
