<?php

namespace Client\Factory;


use Client\Controller\AccountController;
use Client\Controller\ControllerController;
use Client\Controller\DashboardController;
use Client\Controller\SiteController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SiteControllerFactory implements FactoryInterface
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
        $siteService = $realServiceLocator->get('Client\Service\Site');
        $controller = new SiteController($siteService);

        return $controller;
    }
}