<?php
/**
 * Created by PhpStorm.
 * User: A
 * Date: 7/12/2016
 * Time: 1:13 PM
 */

namespace Application\Controller;


use Doctrine\ORM\EntityManager;
use Entity\UserController;
use UniFi;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use ZfcUser\Controller\Plugin\ZfcUserAuthentication;

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
    
}