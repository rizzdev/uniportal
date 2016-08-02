<?php
/**
 * Created by PhpStorm.
 * User: A
 * Date: 7/16/2016
 * Time: 10:52 PM
 */

namespace Client\Factory;


use Zend\Navigation\Service\DefaultNavigationFactory;

class ClientDashboardNavigationFactory extends DefaultNavigationFactory
{
    protected function getName()
    {
        return 'client-dashboard';
    }
}