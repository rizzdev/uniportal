<?php
namespace Client\Controller;

use Application\Controller\CommonController;
use Doctrine\ORM\EntityManager;
use Entity\Portal;

class PortalController extends CommonController
{

    public function indexAction()
    {
        return array('data' => $this->myPortals());
    }

    public function createAction()
    {

        if ($this->posted()) {

            /** @var EntityManager $em */
            $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');

            $data = $this->compilePostParams(array(
                'params' => array(
                    'portal_subdomain',
                    'portal_title',
                    'portal_auth_types',
                    'portal_header',
                )
            ));

            $portal = new Portal();
            $portal->setAuthTypes(json_encode($data['portal_auth_types']));
            $portal->setHeader($data['portal_header']);
            $portal->setSubdomain($data['portal_subdomain']);
            $portal->setTitle($data['portal_title']);
            $portal->setOwner($this->currentUser());
            $em->persist($portal);
            $em->flush();

            return $this->redirect()->toUrl('/client/portal/view/' . $portal->getId());

        }

        return array();
    }

    public function viewAction()
    {
        $portal = $this->currentPortal();

        $portalArray = array(
            'id' => $portal->getId(),
            'owner' => $portal->getOwner()->getUserId(),
            'subdomain' => $portal->getSubdomain(),
            'auth_types' => json_decode($portal->getAuthTypes(), true),
            'header' => $portal->getHeader(),
            'title' => $portal->getTitle(),
        );

        return array('data' => $portalArray);
    }
}
