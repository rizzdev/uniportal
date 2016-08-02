<?php

namespace Application\Factory;


use Application\Controller\CommonController;
use Zend\ServiceManager\ServiceLocatorInterface;

class CommonControllerAbstractFactory
{

    public function start(ServiceLocatorInterface $serviceLocator, CommonController $controller)
    {
        $config = $serviceLocator->getServiceLocator()->get('config');
        $controller->setConfig($config);
    }

}