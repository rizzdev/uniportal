<?php

namespace Client\Service;


use Application\Service\CommonService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Entity\Portal;
use Entity\PortalAuthentication;
use Entity\User;

class StatService extends CommonService
{
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function portalDashboard(Portal $portal)
    {
        $stat['current_clients'] = 0;
        $stat['authorized_clients'] = 0;

        $sites = $this->em->getRepository('\Entity\UserControllerSite')->findBy(array('portal' => $portal));

        foreach ($sites as $site)
        {
            $unifi = new \UniFi($site->getUserController());
            if ($unifi->login()) {
                $unifi->setSite($site->getSiteName());
                $clients = $unifi->list_clients();
                $stat['current_clients'] = count($clients);
            }
        }

        $authorizedClients = $this->em->getRepository('\Entity\GuestDevice')->findBy(array('portal' => $portal));
        $authorizedClients = $this->renderEntity($authorizedClients, array('portal'));
        $stat['authorized_clients'] = count($authorizedClients);

        $return = array(
            array(
                'label' => 'Total authorized',
                'value' => $stat['authorized_clients']
            ),
            array(
                'label' => 'Connected Clients',
                'value' => $stat['current_clients']
            ),
        );

        $this->data('pie_chart', $return);

        return $this->returnData();
    }
}