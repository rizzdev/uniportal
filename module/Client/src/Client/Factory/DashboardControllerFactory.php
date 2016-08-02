<?php

namespace Client\Factory;


use Client\Controller\ControllerController;
use Client\Controller\DashboardController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DashboardControllerFactory implements FactoryInterface
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
        $controllerService = $realServiceLocator->get('Client\Service\Dashboard');
        $controller = new DashboardController($controllerService);

        return $controller;
    }
}