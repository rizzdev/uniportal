<?php

namespace Client\Factory;


use Client\Controller\AccountController;
use Client\Controller\ControllerController;
use Client\Controller\DashboardController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AccountControllerFactory implements FactoryInterface
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
        $accountService = $realServiceLocator->get('Client\Service\Account');
        $controller = new AccountController($accountService);

        return $controller;
    }
}