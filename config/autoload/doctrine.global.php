<?php
return array(
    'doctrine' => array(
        'driver' => array(
            'Entity_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => array(__DIR__ . '/../../model/Entity'),
            ),
            'orm_default' => array(
                'drivers' => array(
                    'Entity' => 'Entity_driver',
                ),
            ),
        ),
        'connection' => array(
            // default connection name
            'orm_default' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params' => array(
                    'host'     => 'localhost',
                    'port'     => '3306',
                    'user'     => 'root',
                    'password' => 'rizz935123',
                    'dbname'   => 'portal',
                )
            )
        )
    ),
);