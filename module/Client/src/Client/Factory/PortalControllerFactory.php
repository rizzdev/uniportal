<?php

namespace Client\Factory;


use Client\Controller\PortalController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PortalControllerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $realServiceLocator = $serviceLocator->getServiceLocator();
        $portalService      = $realServiceLocator->get('Client\Service\Portal');
        $portalController   = new PortalController($portalService);

        return $portalController;
    }
}