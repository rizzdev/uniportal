<?php

namespace Client\Service;


use Application\Service\CommonService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Entity\Portal;
use Entity\PortalAuthentication;
use Entity\User;

class PortalService extends CommonService
{
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param $data
     * @param User $user
     * @return Portal
     */
    public function create($data, User $user)
    {
        $portal = new Portal();
        $portal->setHeader($data['header']);
        $portal->setSubdomain($data['subdomain']);
        $portal->setTitle($data['title']);
        $portal->setOwner($user);
        $this->database()->persist($portal);
        $this->database()->flush();

        if(isset($data['auth_types[]']))
        {
            foreach ($data['auth_types[]'] as $authType)
            {
                $portalAuthentication = $this->em->getRepository('\Entity\PortalAuthentication')->findOneBy(array('authenticationType' => $authType, 'portal' => $portal));
                if(!is_object($portalAuthentication))
                    $portalAuthentication = new PortalAuthentication();
                $portalAuthentication->setPortal($portal);
                $portalAuthentication->setAuthenticationType($authType);
                $this->commit($portalAuthentication);
            }
        }


        $this->success('Succesfully updated portal');
        return $this->returnData();

    }

    public function update($data, Portal $portal)
    {
        $portal->setHeader($data['header']);
        $portal->setSubdomain($data['subdomain']);
        $portal->setTitle($data['title']);
        $this->database()->persist($portal);
        $this->database()->flush();

        $currentAuthTypes = $this->em->getRepository('\Entity\PortalAuthentication')->findBy(array('portal' => $portal, 'enabled' => 1));
        /** @var \Entity\PortalAuthentication $currentAuthType */
        foreach ($currentAuthTypes as $currentAuthType){
            $currentAuthType->setEnabled(0);
            $this->commit($currentAuthType);
        }

        if(isset($data['auth_types[]'])) {
            foreach ($data['auth_types[]'] as $authType) {
                $portalAuthentication = $this->em->getRepository('\Entity\PortalAuthentication')->findOneBy(array('authenticationType' => $authType, 'portal' => $portal));
                if(!is_object($portalAuthentication)){
                    $portalAuthentication = new PortalAuthentication();
                    $portalAuthentication->setPortal($portal);
                    $portalAuthentication->setAuthenticationType($authType);
                    $this->commit($portalAuthentication);
                } else {
                    $portalAuthentication->setEnabled(1);
                    $this->commit($portalAuthentication);
                }
            }
        }

        $this->success('Succesfully updated portal');
        return $this->returnData();

    }

    public function userPortalsView(User $user)
    {

        /** @var EntityManager $em */
        $em = $this->em;

        $results = $em
            ->createQueryBuilder()
            ->select('p')
            ->from('\Entity\Portal', 'p')
            ->where('p.owner = :user')
            ->setParameter('user', $user)
            ->getQuery()->getResult();

        $data = array();

        /** @var Portal $result */
        foreach ($results as $result) {
            if(is_object($result)) {
                $single = array(
                    'id' => $result->getId(),
                    'owner' => $result->getOwner()->getUserId(),
                    'title' => $result->getTitle(),
                    'subdomain' => $result->getSubdomain(),
                    'header' => $result->getHeader(),
                    'auth_types' => $this->viewAuthTypes($result),
                );
                array_push($data, $single);
            }
        }

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

    public function view(Portal $portal)
    {
        $portalArray = array(
            'id' => $portal->getId(),
            'owner' => $portal->getOwner()->getUserId(),
            'subdomain' => $portal->getSubdomain(),
            'header' => $portal->getHeader(),
            'title' => $portal->getTitle(),
            'auth_types' => $this->viewAuthTypes($portal),
        );

        return $portalArray;
    }

}