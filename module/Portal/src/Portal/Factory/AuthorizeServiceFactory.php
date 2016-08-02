<?php

namespace Portal\Factory;


use Client\Service\PortalService;
use Portal\Service\AuthorizeService;
use Portal\Service\GuestService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthorizeServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $em            = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $service = new AuthorizeService($em);

        return $service;
    }
}