<?php

namespace Client\Service;


use Application\Service\CommonService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Entity\Portal;
use Entity\PortalAuthentication;
use Entity\User;

class AccountService extends CommonService
{
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
}