<?php

namespace Client\Factory;


use Client\Service\PortalService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PortalServiceFactory implements FactoryInterface
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
        $portalService = new PortalService($em);

        return $portalService;
    }
}