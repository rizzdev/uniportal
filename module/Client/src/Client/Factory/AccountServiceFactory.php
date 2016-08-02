<?php

namespace Client\Factory;


use Client\Service\AccountService;
use Client\Service\ControllerService;
use Client\Service\CryptService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AccountServiceFactory implements FactoryInterface
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
        $accountService = new AccountService($em);

        return $accountService;
    }
}