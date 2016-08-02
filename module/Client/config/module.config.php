<?php



return array(
    'navigation' => include 'navigation.config.php',
    'router' => array(
        'routes' => array(
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'client' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/client',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Client\Controller',
                        'controller'    => 'Dashboard',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action][/:id]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'factories' => array(
            'Client\Controller\Portal' => Client\Factory\PortalControllerFactory::class,
            'Client\Controller\Controller' => Client\Factory\ControllerControllerFactory::class,
            'Client\Controller\Dashboard' => Client\Factory\DashboardControllerFactory::class,
            'Client\Controller\Account' => Client\Factory\AccountControllerFactory::class,
            'Client\Controller\Site' => Client\Factory\SiteControllerFactory::class,
            'Client\Controller\Stat' => Client\Factory\StatControllerFactory::class,
        ),
        'invokables' => array(
            'Client\Controller\Api' => Client\Controller\ApiController::class,
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'Client\Service\Portal' => Client\Factory\PortalServiceFactory::class,
            'Client\Service\Controller' => Client\Factory\ControllerServiceFactory::class,
            'Client\Service\Dashboard' => Client\Factory\DashboardServiceFactory::class,
            'Client\Service\Account' => Client\Factory\AccountServiceFactory::class,
            'Client\Service\Site' => Client\Factory\SiteServiceFactory::class,
            'Client\Service\Stat' => Client\Factory\StatServiceFactory::class,
            'Client\Navigation\Dashboard' => Client\Factory\ClientDashboardNavigationFactory::class
        ),
    ),

    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            'zfc-user' => __DIR__ . '/../view',
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),


    ),
);
