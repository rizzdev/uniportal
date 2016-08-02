<?php

namespace Client\Factory;


use Client\Controller\AccountController;
use Client\Controller\ControllerController;
use Client\Controller\DashboardController;
use Client\Controller\StatController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class StatControllerFactory implements FactoryInterface
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
        $statService = $realServiceLocator->get('Client\Service\Stat');
        $controller = new StatController($statService);

        return $controller;
    }
}