<?php
/**
 * Created by PhpStorm.
 * User: A
 * Date: 7/12/2016
 * Time: 1:13 PM
 */

namespace Application\Controller;


use Doctrine\Entity;
use Doctrine\ORM\EntityManager;
use Entity\Portal;
use Entity\UserController;
use UniFi;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use ZfcUser\Controller\Plugin\ZfcUserAuthentication;
use Doctrine\ORM\Query;


class CommonController extends AbstractActionController
{

    const SUCCESS = 'true';
    const FAIL = 'false';

    private $unifi = null;
    /**
     * @return ZfcUserAuthentication
     */
    public function auth()
    {
        return $this->zfcUserAuthentication();
    }

    /**
     * @return mixed
     */
    public function posted()
    {
        return $this->getRequest()->isPost();
    }

    public function createApiCall($status, $message, $data = null)
    {
        $model = new JsonModel(array(
            'success' => $status,
            'message' => $message,
            'data' => $data
        ));

        return $model;
    }

    /**
     * @return \Entity\User
     */
    public function currentUser()
    {
        $identity = $this->auth()->getIdentity();

        if ($identity == null) {
            return $this->redirect()->toUrl('/user/login');
        }

        /** @var EntityManager $em */
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $user = $em->getRepository('\Entity\User')->find($identity);

        if (!is_object($user)) {
            return $this->redirect()->toUrl('/user/login');
        }

        return $user;
    }
    
    public function currentController()
    {
        $id = $this->params()->fromRoute('id');
        $user = $this->currentUser();

        /** @var EntityManager $em */
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        /** @var UserController $controller */
        $controller = $em->getRepository('\Entity\UserController')->find($id);

        if (is_object($controller)) {
            if($controller->getUser()->getUserId() != $user->getUserId())
            {
                throw new \Exception('Unauthorized');
            }
            return $controller;
        } else {
            throw new \Exception('Error while compiling controller');
        }
        
    }

    /**
     * @return UniFi
     * @throws \Exception
     */
    public function unifi()
    {
        if($this->unifi == null)
        {
            $controller = $this->currentController();
            $unifi = new UniFi($controller);
            if ($unifi->login()) {
                $this->unifi = $unifi;
                return $unifi;
            } else {
                throw new \Exception('Failed to login to unifi controller');
            }
        } else {
            return $this->unifi;
        }
    }

    public function compilePostParams($array)
    {
        $data = $array['params'];

        foreach ($data as $item) {

            $value = $this->params()->fromPost($item);

            if($value == null)
                $value = false;

            $return[$item] = $value;
        }

        return $return;
    }

    public function visitSite()
    {
        $controller = $this->currentController();

        /** @var EntityManager $em */
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');

        $site = $em->getRepository('\Entity\UserControllerSite')->findOneBy(array('userController' => $controller));
        $ap = $em->getRepository('\Entity\UserControllerAp')->findOneBy(array('userControllerSite' => $site));

        if (is_object($ap)) {
            return $this->redirect()->toUrl('/guest/s/' . $site->getSiteName() . '/?mac=' . $ap->getMac());
        }

        return $this->redirect()->toUrl('/client/controller');
    }

    public function currentPortal()
    {
        $id = $this->params()->fromRoute('id');
        $user = $this->currentUser();

        /** @var EntityManager $em */
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        /** @var UserController $controller */
        $portal = $em->getRepository('\Entity\Portal')->find($id);

        if (is_object($portal)) {
            if($portal->getOwner()->getUserId() != $user->getUserId())
            {
                throw new \Exception('Unauthorized');
            }
            return $portal;
        } else {
            throw new \Exception('Error while compiling portal');
        }


    }

    public function myPortals($object = false)
    {

        if ($object) {
            $mode = Query::HYDRATE_OBJECT;
        } else
        {
            $mode = Query::HYDRATE_ARRAY;
        }

        /** @var EntityManager $em */
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');

        $results = $em
            ->createQueryBuilder()
            ->select('p')
            ->from('\Entity\Portal', 'p')
            ->where('p.owner = :user')
            ->setParameter('user', $this->currentUser())
            ->getQuery()->getResult($mode);

        $data = array();

        /** @var Portal $result */
        foreach ($results as $result) {
            $single = array();
            if(is_object($result))
                $single = array(
                    'id' => $result->getId(),
                    'owner' => $result->getOwner()->getUserId(),
                    'subdomain' => $result->getSubdomain(),
                    'auth_types' => json_decode($result->getAuthTypes(), true),
                    'header' => $result->getHeader(),
                );
            array_push($data, $single);
        }

        return $data;

    }


}