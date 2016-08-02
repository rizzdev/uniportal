<?php

namespace Portal\Factory;


use Client\Controller\ControllerController;
use Portal\Controller\AuthorizeController;
use Portal\Controller\GuestController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthorizeControllerFactory implements FactoryInterface
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
        $service      = $realServiceLocator->get('Portal\Service\Authorize');
        $controller   = new AuthorizeController($service);

        return $controller;
    }
}