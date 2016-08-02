<?php

namespace Portal\Service;


use Application\Service\CommonService;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManager;
use Entity\GuestDevice;
use Entity\Portal;
use Zend\Form\Element\Date;
use Zend\Session\Container;

class AuthorizeService extends CommonService
{
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function isAllowed($type, Portal $portal)
    {
        $authType = $this->em->getRepository('\Entity\PortalAuthentication')->findOneBy(array('portal' => $portal, 'enabled' => 1, 'authenticationType' => $type));

        if (is_object($authType)) {
            return true;
        }

        throw new \Exception('Authentication Type not allowed');
    }

    public function authorizeDatabase()
    {
        $expire = new DateTime('now');
        $expire->add(new DateInterval('PT' . 60 . 'M'));

        $apContainer = new Container('ap_container');
        $ap = $this->em->getRepository('\Entity\UserControllerAp')->findOneBy(array('mac' => $apContainer->ap));
        $portal = $ap->getUserControllerSite()->getUserController()->getPortal();

        $guestDevice = $this->em->getRepository('\Entity\GuestDevice')->findOneBy(array('portal' => $portal, 'authorized' => 0, 'mac' => $apContainer->mac));

        if (is_object($guestDevice)) {
            $guestDevice->setAuthorized(1);
            $guestDevice->setUpdatedTimestamp(new \DateTime('now'));
            $guestDevice->setExpireTimestamp($expire);
        } else {
            $guestDevice = new GuestDevice();
            $guestDevice->setAuthorized(1);
            $guestDevice->setPortal($portal);
            $guestDevice->setUpdatedTimestamp(new \DateTime('now'));
            $guestDevice->setMac($apContainer->mac);
            $guestDevice->setExpireTimestamp($expire);
        }

        $this->commit($guestDevice);
        return $this->success('Guest has been authorized')->returnData();
    }

    public function authorizeUnifiController()
    {
        $apContainer = new Container('ap_container');
        $ap = $this->em->getRepository('\Entity\UserControllerAp')->findOneBy(array('mac' => $apContainer->ap));
        $unifi = new \UniFi($ap->getUserControllerSite()->getUserController());
        $unifi->setSite($ap->getUserControllerSite()->getSiteName());

        $result = null;

        if ($unifi->login()) {
            $result = $unifi->authorize_guest($apContainer->mac, 60);
        }

        return $result ? $this->success('Guest has been authorized')->returnData() : $this->error('There was an error authorizing the client')->returnData();
}

    public function isAuthorized(Portal $portal)
    {
        $apContainer = new Container('ap_container');

        $guestDevice = $this->em->getRepository('\Entity\GuestDevice')->findOneBy(array(
            'portal' => $portal,
            'mac' => $apContainer->mac,
            'authorized' => 1
        ));

        if (is_object($guestDevice)) {
            $expireTime = $guestDevice->getExpireTimestamp();
            if ($expireTime > new DateTime('now')) {
                return true;
            } else {
                $guestDevice->setAuthorized(0);
                $this->commit($guestDevice);
                return false;
            }
        }

        return false;
    }

    public function automaticAuthorization(Portal $portal)
    {
        $type = 'automatic_authentication';
        $this->isAllowed($type, $portal);
        $this->authorizeDatabase();
        $this->authorizeUnifiController();
        return $this->success('Client Authorized')->returnData();
    }

    public function buttonAuthorization(Portal $portal)
    {
        $type = 'button_authentication';
        $this->isAllowed($type, $portal);
        $this->authorizeDatabase();
        $this->authorizeUnifiController();
        return $this->success('Client Authorized')->returnData();
    }

}