<?php

namespace Portal\Service;


use Application\Service\CommonService;
use Doctrine\ORM\EntityManager;
use Entity\Portal;

class GuestService extends CommonService
{
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function renderGuestPortal(Portal $portal)
    {
        $data = array(
            'title' => $portal->getTitle(),
            'header' => $portal->getHeader(),
            'subdomain' => $portal->getSubdomain(),
            'auth_types' => $this->viewAuthTypes($portal),
        );

        return $data;
    }

    public function viewAuthTypes(Portal $portal)
    {
        $authTypes = $this->em->getRepository('\Entity\PortalAuthentication')->findBy(array('portal' => $portal, 'enabled' => 1));
        $data = array();

        if (is_array($authTypes)) {
            foreach ($authTypes as $authType)
            {
                $single = array(
                    'id' => $authType->getId(),
                    'auth_type' => $authType->getAuthenticationType(),
                );
                array_push($data, $single);
            }
        }

        return $data;
    }


}