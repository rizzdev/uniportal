<?php

namespace Client\Factory;


use Client\Service\ControllerService;
use Client\Service\DashboardService;
use Client\Service\PortalService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DashboardServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $em                    = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $controllerService     = $serviceLocator->get('Client\Service\Controller');
        $service               = new DashboardService($em, $controllerService);

        return $service;
    }
}