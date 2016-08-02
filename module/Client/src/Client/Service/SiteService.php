<?php

namespace Client\Service;


use Application\Service\CommonService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Entity\Portal;
use Entity\PortalAuthentication;
use Entity\User;
use Entity\UserControllerSite;
use UniFi;

class SiteService extends CommonService
{
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function updatePortal($data, UserControllerSite $site)
    {
        $site->setPortal($this->em->getRepository('\Entity\Portal')->find($data['portal']));
        $this->commit($site);
        return $this->success('Succesfully set site portal')->returnData();
    }

    public function validateSite(UserControllerSite $site)
    {
        $controller = $site->getUserController();
        $validation['errors'] = array();

        $unifi = new UniFi($controller);
        $siteSettings = array();

        if ($unifi->login()) {

            $unifi->setSite($site->getSiteName());
            $settings = $unifi->list_settings();
            $wirelessNetworks = $unifi->list_wlanconf();

            $guestNetworkEnabled = false;

            foreach ($wirelessNetworks as $network) {
                if ($network->is_guest = 1) {
                    $guestNetworkEnabled = true;
                }
            }

            if (!$guestNetworkEnabled) {
                $this->error('Failed to find any guest networks on this site');
            }

            foreach ($settings as $setting)
            {
                if (isset($setting->key) && $setting->key == 'guest_access') {

                    if(!isset($setting->portal_enabled) || !($setting->portal_enabled = 1)) {
                        $this->error('Guest Portal is not enabled');
                    }

                    if(!isset($setting->custom_ip) || !($setting->custom_ip = $_ENV['customPortalIp'])) {
                        $this->error('Custom Portal IP must be set to 74.91.120.57');
                    }

                    if(!isset($setting->auth) || !($setting->auth = 'custom')) {
                        $this->error('Authentication must be set to External Portal Server');
                    }

                }
            }

            $siteSettings[$site->getSiteName()] = $settings;

            //$this->data('sties', $siteSettings);
            return $this->returnData();
        }

        $this->error('Failed login to controller');
        return $this->returnData();    }
}