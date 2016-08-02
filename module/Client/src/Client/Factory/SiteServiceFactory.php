<?php

namespace Client\Factory;


use Client\Service\PortalService;
use Client\Service\SiteService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SiteServiceFactory implements FactoryInterface
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
        $siteService = new SiteService($em);

        return $siteService;
    }
}