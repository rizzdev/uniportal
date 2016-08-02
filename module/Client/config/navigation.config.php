<?php

return array(
    'client-dashboard' => array(
        array('label' => 'Dashboard', 'uri' => '/client', 'icon' => 'fa fa-home'),
        array(
            'label' => 'My Account', 'uri' => '#', 'icon' => 'fa fa-group', 'pages' =>
            array(
                array('label' => 'Settings', 'uri' => '/client/account/settings', 'icon' => 'fa fa-gears'),
                array('label' => 'Help', 'uri' => '/client/account/help', 'icon' => 'fa fa-question')
            ),
        ),
        array(
            'label' => 'Controllers', 'uri' => '#', 'icon' => 'fa fa-rss', 'pages' =>
            array(
                array('label' => 'Add new Controller', 'uri' => '/client/controller/create', 'icon' => 'fa fa-plus'),
                array('label' => 'My Controllers', 'uri' => '/client/controller', 'icon' => 'fa fa-list')
            ),
        ),
        array(
            'label' => 'Portals', 'uri' => '#', 'icon' => 'fa fa-external-link', 'pages' =>
            array(
                array('label' => 'Add new Portal', 'uri' => '/client/portal/create', 'icon' => 'fa fa-plus'),
                array('label' => 'My Portals', 'uri' => '/client/portal', 'icon' => 'fa fa-list')
            ),
        ),
    )
);