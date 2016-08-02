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
use Zend\Http\Header\ContentType;
use Zend\Http\Request;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
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

    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function isApiRequest()
    {
        /** @var Request $request */
        $request = $this->getRequest();

        /** @var ContentType $contentType */
        $contentType = $request->getHeaders()->get('Content-Type');

        if (is_object($contentType)) {

            if ($contentType->toString() == 'Content-Type: application/json') {
                return true;
            }

        }

        return false;
    }


    public function requirePostedJson()
    {
        $json = $this->getRequest()->getContent();
        $array = json_decode($json, true);
        $fields = array();

        foreach ($array as $field)
        {
            if(@is_array($fields[$field['name']]))
            {
                array_push($fields[$field['name']], $field['value']);
            }
            else if (isset($fields[$field['name']]))
            {
                $previous = $field['name'];
                $fields[$field['name']] = array($previous, $field['value']);
            }
            else
            {
                if( strpos($field['name'], '[]') !== false ) {
                    $fields[$field['name']] = array($field['value']);
                } else {
                    $fields[$field['name']] = $field['value'];
                }
            }

        }

        return $fields;
    }


    /**
     * @return mixed
     */
    public function posted()
    {
        return $this->getRequest()->isPost();
    }

    public function createServiceApiResponse($serviceResponse)
    {
        if ($serviceResponse['success']) {
            $this->getResponse()->setStatusCode(200);
        } else {
            $this->getResponse()->setStatusCode(500);
        }

        $model = new JsonModel($serviceResponse);

        return $model;
    }

    public function createApiCall($status, $message, $data = null)
    {
        if (is_array($message)) {
            if(isset($message['service-success'])){
                $status = $message['service-success'];
            }
        }

        if (is_array($data)) {
            if(isset($data['service-data'])){
                $data = $data['service-success'];
            }
        }

        if ($status) {
            $this->getResponse()->setStatusCode(200);
        } else {
            $this->getResponse()->setStatusCode(500);
        }


        $model = new JsonModel(array(
            'success' => $status,
            'messages' => $message,
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

    public function currentSite()
    {
        $id = $this->params()->fromRoute('id');
        $user = $this->currentUser();

        /** @var EntityManager $em */
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        /** @var UserController $controller */
        $site = $em->getRepository('\Entity\UserControllerSite')->find($id);

        if (is_object($site)) {
            if($site->getUserController()->getUser()->getUserId() != $user->getUserId())
            {
                throw new \Exception('Unauthorized');
            }
            return $site;
        } else {
            throw new \Exception('Error while compiling site');
        }
    }

    public function currentGuestPortal()
    {
        $apContainer = new Container('ap_container');

        /** @var EntityManager $em */
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $ap = $em->getRepository('\Entity\UserControllerAp')->findOneBy(array('mac' => $apContainer->ap));
        if (is_object($ap)) {
            return $ap->getUserControllerSite()->getPortal();
        }

        throw new \Exception('Error while compiling guest portal');
    }

}