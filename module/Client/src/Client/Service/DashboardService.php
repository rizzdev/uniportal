<?php

namespace Client\Service;


use Application\Service\CommonService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Entity\User;
use Entity\UserController;
use Entity\UserControllerAp;
use Entity\UserControllerSite;
use UniFi;

class DashboardService extends CommonService
{
    protected $controllerService;

    public function __construct(EntityManager $em,
                                ControllerService $controllerService)
    {
        $this->em = $em;
        $this->controllerService = $controllerService;
    }

    public function userDashboardView(User $user)
    {

        $controllers = $this->em->getRepository('\Entity\UserController')->findBy(array('user' => $user));

        $controllerData = array();

        foreach ($controllers as $controller) {
            array_push($controllerData, $this->controllerService->view($controller));
        }

        foreach ($controllerData as $controllerKey => $controllerValue)
        {
            $siteData = $controllerValue['sites'];

            $unifi = new UniFi(null, array(
                'user' => $controllerValue['username'],
                'password' => $controllerValue['password'],
                'baseurl' => $controllerValue['base_url'],
            ));

            $controller = $this->em->getRepository('\Entity\UserController')->find($controllerValue['id']);

            if ($unifi->login()) {
                foreach ($siteData as $siteKey => $siteValue)
                {
                    $guestDevices = $this->em->getRepository('\Entity\GuestDevice')->findBy(array(
                        'portal' => $controller->getPortal(),
                    ));
                    
                    $guestDevicesArray = array();

                    foreach ($guestDevices as $guestDevice) {
                        $single = array(
                            'id' => $guestDevice->getId(),
                            'mac' => $guestDevice->getMac()
                        );
                        array_push($guestDevicesArray, $single);
                    }
                    
                    $unifi->setSite($siteValue['site_name']);
                    $controllerData[$controllerKey]['sites'][$siteKey]['guests'] = $unifi->list_guests();
                    $controllerData[$controllerKey]['sites'][$siteKey]['clients'] = $unifi->list_clients();
                    $controllerData[$controllerKey]['sites'][$siteKey]['events'] = $unifi->list_events();
                    $controllerData[$controllerKey]['portal_guests'] = $guestDevicesArray;
                }
            }
        }

        $this->data('controllers', $controllerData);
        $this->data('counter', $this->count($controllerData));
        $this->success('Succesfully retrived user information');

        return $this->returnData();
    }


    public function count(Array $controllerData)
    {

        $counter['controllers'] = 0;
        $counter['sites'] = 0;
        $counter['aps'] = 0;
        $counter['guests'] = 0;
        $counter['clients'] = 0;
        $counter['portal_guests'] = 0;

        foreach ($controllerData as $controllerKey => $controllerValue)
        {
            $counter['controllers']++;

            foreach ($controllerValue['portal_guests'] as $guestDevice)
            {
                $counter['portal_guests']++;
            }

            foreach ($controllerValue['sites'] as $siteKey => $siteValue)
            {
                $counter['sites']++;

                foreach ($controllerData[$controllerKey]['sites'][$siteKey]['guests'] as $guest)
                {
                    $counter['guests']++;
                }

                foreach ($controllerData[$controllerKey]['sites'][$siteKey]['clients'] as $client)
                {
                    $counter['clients']++;
                }

                foreach ($siteValue['aps'] as $apKey => $apValue)
                {
                    $counter['aps']++;
                }

            }
        }

        return $counter;
    }
}