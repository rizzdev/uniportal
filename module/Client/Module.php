<?php
namespace Client;

use Doctrine\ORM\EntityManager;
use Entity\UserLogin;
use Zend\Http\PhpEnvironment\RemoteAddress;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $sm = $e->getApplication()->getServiceManager();
        $zfcAuthEvents = $e->getApplication()->getServiceManager()->get('ZfcUser\Authentication\Adapter\AdapterChain')->getEventManager();

        $zfcAuthEvents->attach( 'authenticate', function( $authEvent ) use( $sm ){

            /** @var EntityManager $em */
            $em = $sm->get('doctrine.entitymanager.orm_default');
            $remote = new RemoteAddress;
            $remote->setUseProxy( true );

            $login = new UserLogin();
            $login->setUser($em->getRepository('\Entity\User')->find($authEvent->getIdentity()));
            $login->setIp($remote->getIpAddress());
            $login->setTimestamp(new \DateTime('now'));

            $em->persist($login);
            $em->flush();

        });


        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, function($e) {
            $result = $e->getResult();
            $result->setTerminal(TRUE);
        });

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
