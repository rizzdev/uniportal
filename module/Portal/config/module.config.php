<?php
return array(
    'router' => array(
        'routes' => array(
            'portal' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/portal',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Portal\Controller',
                        'controller'    => 'Guest',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
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
            'authorize' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/portal/authorize',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Portal\Controller',
                        'controller'    => 'Authorize',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '[/:action]',
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
    'service_manager' => array(
        'factories' => array(
            'Portal\Service\Guest' => Portal\Factory\GuestServiceFactory::class,
            'Portal\Service\Authorize' => Portal\Factory\AuthorizeServiceFactory::class,
        ),
    ),
    'controllers' => array(
        'factories' => array(
            'Portal\Controller\Guest' => Portal\Factory\GuestControllerFactory::class,
            'Portal\Controller\Authorize' => Portal\Factory\AuthorizeControllerFactory::class,
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);
