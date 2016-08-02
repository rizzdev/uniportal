<?php

namespace Client\Service;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Application\Service\CommonService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Entity\User;
use Entity\UserController;
use Entity\UserControllerAp;
use Entity\UserControllerSite;
use UniFi;
use Zend\Crypt\BlockCipher;

class ControllerService extends CommonService
{
    public function __construct(EntityManager $em, CryptService $cryptService)
    {
        $this->em = $em;
    }

    public function validateConfiguration(UserController $controller)
    {
        $unifi = new UniFi($controller);
        $siteSettings = array();

        if ($unifi->login()) {

            $sites = $this->em->getRepository('\Entity\UserControllerSite')->findBy(array('userController' => $controller));

            foreach ($sites as $site)
            {
                $unifi->setSite($site->getSiteName());
                $settings = $unifi->list_settings();
                foreach ($settings as $setting)
                {
                    if (isset($setting->key) && $setting->key == 'guest_access') {

                        if(isset($setting->portal_enabled) && $setting->portal_enabled = 1){

                        } else {
                            $this->error('Failed Validation on site (' . $site->getSiteName() . ') ' . 'Guest Portal is not enabled');
                        }

                        if(isset($setting->custom_ip) && $setting->custom_ip = '74.91.120.57'){

                        } else {
                            $this->error('Failed Validation on site (' . $site->getSiteName() . ') ' . 'Custom Portal IP must be set to 74.91.120.57');
                        }

                        if(isset($setting->auth) && $setting->auth = 'custom'){

                        } else {
                            $this->error('Failed Validation on site (' . $site->getSiteName() . ') ' . 'Authentication must be set to External Portal Server');
                        }

                    }
                }
                $siteSettings[$site->getSiteName()] = $settings;
            }

            $this->data('sties', $siteSettings);
            return $this->returnData();
        }

        $this->error('Failed login to controller');
        return $this->returnData();
    }

    public function create($data, User $user)
    {

        $controller = new UserController();
        $controller->setBaseUrl($data['base_url']);
        $controller->setUsername($data['username']);
        $controller->setPassword($data['password']);
        $controller->setTimestamp(new \DateTime('now'));
        $controller->setUser($user);

        $unifi = new UniFi($controller);

        if ($unifi->login()) {
            $this->em->persist($controller);
            $this->em->flush();
            $this->gatherSites($unifi, $controller);
            $this->gatherAps($unifi, $controller);
            $this->data('controller', $this->view($controller));
            return $this->success('Your controller has been added')->returnData();
        }

        $this->error('Failed to connect to controller, please ensure your credentials are correct');

        return $this->returnData();
    }

    public function userPortalsView(User $user)
    {
        $em = $this->em;
        $controllers = $em->getRepository('\Entity\UserController')->findBy(array('user' => $user));
        $data = array();

        if(is_array($controllers))
        {
            /** @var UserController $controller */
            foreach ($controllers as $controller)
            {
                $it = array(
                    'id' => $controller->getId(),
                    'user' => $controller->getUser()->getUserId(),
                    'base_url' => $controller->getBaseUrl(),
                    'username' => $controller->getUsername(),
                    'password' => $controller->getPassword(),
                    'version' => $controller->getVersion(),
                    'timestamp' => $controller->getTimestamp()->getTimestamp()
                );

                array_push($data, $it);
            }

            return $data;
        }

        return false;
    }

    public function siteApsView(UserControllerSite $site)
    {
        $sites = $this->em->getRepository('\Entity\UserControllerAp')->findBy(array('userControllerSite' => $site));
        $data = array();

        if (is_array($sites)) {

            foreach ($sites as $site)
            {
                $single = array(
                    'id' => $site->getId(),
                    'ssid' => $site->getSsid(),
                    'mac' => $site->getMac(),
                );
                array_push($data, $single);
            }
        }

        return $data;
    }


    public function controllerSitesView(UserController $controller)
    {
        $sites = $this->em->getRepository('\Entity\UserControllerSite')->findBy(array('userController' => $controller));
        $data = array();

        if (is_array($sites)) {

            foreach ($sites as $site)
            {
                $portal = null;

                if (is_object($site->getPortal())) {
                    $portal = $site->getPortal()->getId();
                }
                $single = array(
                    'id' => $site->getId(),
                    'site_name' => $site->getSiteName(),
                    'site_id' => $site->getSiteId(),
                    'aps' => $this->siteApsView($site),
                    'portal_id' => $portal
                );
                array_push($data, $single);
            }
        }

        return $data;
    }

    public function view(UserController $controller)
    {
        $portalArray = array(
            'id' => $controller->getId(),
            'base_url' => $controller->getBaseUrl(),
            'username' => $controller->getUsername(),
            'password' => $controller->getPassword(),
            'sites' => $this->controllerSitesView($controller),
        );

        return $portalArray;
    }

    public function viewDetailed(UserController $controller)
    {
        $controllerArray = $this->view($controller);

        $siteData = $controllerArray['sites'];

        $unifi = new UniFi(null, array(
            'user' => $controllerArray['username'],
            'password' => $controllerArray['password'],
            'baseurl' => $controllerArray['base_url'],
        ));

        $controller = $this->em->getRepository('\Entity\UserController')->find($controllerArray['id']);

        if ($unifi->login()) {

            foreach ($siteData as $siteKey => $siteValue)
            {

                $unifi->setSite($siteValue['site_name']);

                $controllerArray['sites'][$siteKey]['clients'] = $unifi->list_clients();
                $controllerArray['sites'][$siteKey]['events'] = $unifi->list_events();
                $controllerArray['sites'][$siteKey]['aps'] = $unifi->list_aps();

            }
        }

        $portals = $this->em->getRepository('\Entity\Portal')->findBy(array('owner' => $controller->getUser()));
        $portals = $this->renderEntity($portals, array('owner'));

        $this->data('portals', $portals);
        $this->data('controller', $controllerArray);

        return $this->returnData();

    }


    public function gatherSites(UniFi $unifiInstance, UserController $controller)
    {
        $unifi = $unifiInstance;
        $sites = $unifi->list_sites();

        foreach ($sites as $site)
        {
            $userControllerSiteCurrent = $this->em->getRepository('\Entity\UserControllerSite')->findOneBy(array('siteId' => $site->_id));

            if($userControllerSiteCurrent instanceof UserControllerSite) {
                //Already in database
            } else {
                $userControllerSite = new UserControllerSite();
                $userControllerSite->setUserController($controller);
                $userControllerSite->setSiteId($site->_id);
                $userControllerSite->setSiteName($site->name);
                $this->em->persist($userControllerSite);
                $this->em->flush();
            }
        }

    }

    public function gatherAps(UniFi $unifiInstance, UserController $controller)
    {
        $userControllerSites = $this->em->getRepository('\Entity\UserControllerSite')->findBy(array('userController' => $controller));
        /** @var UserControllerSite $site */
        foreach ($userControllerSites as $site) {
            $unifiInstance->setSite($site->getSiteName());
            $aps = $unifiInstance->list_aps();
            foreach ($aps as $ap) {
                $userControllerApCurrent = $this->em->getRepository('\Entity\UserControllerAp')->findOneBy(array('mac' => $ap->mac));
                if ($userControllerApCurrent instanceof UserControllerAp) {
                    //Already in database
                } else {
                    $userControllerAp = new UserControllerAp();
                    $userControllerAp->setMac($ap->mac);
                    $userControllerAp->setUserControllerSite($site);
                    $this->em->persist($userControllerAp);
                    $this->em->flush();
                }
            }
        }


    }

    public function update($data, UserController $controller)
    {
        $controller->setUsername($data['username']);
        $controller->setPassword($data['password']);
        $controller->setBaseUrl($data['base_url']);
        $controller->setTimestamp(new \DateTime('now'));
        $this->em->persist($controller);
        $this->em->flush();

        return $this->success('Succesfully updated controller')->returnData();
    }

    public function updateLoginCookies(UniFi $unifi, UserController $controller)
    {
        $blockCipher = BlockCipher::factory('mcrypt', array('algo' => 'aes'));
        $blockCipher->setKey($_ENV['cipherKey']);
        $cookiesEncrypted = $blockCipher->encrypt($unifi->getCookies());
        $controller->setCookies($cookiesEncrypted);
        $this->commit($controller);
    }


}