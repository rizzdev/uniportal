<?php

namespace Client\Factory;


use Client\Controller\ControllerController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ControllerControllerFactory implements FactoryInterface
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
        $controllerService = $realServiceLocator->get('Client\Service\Controller');
        $portalService = $realServiceLocator->get('Client\Service\Portal');
        $siteService = $realServiceLocator->get('Client\Service\Site');
        $controllerController   = new ControllerController($controllerService, $portalService, $siteService);

        return $controllerController;
    }
}