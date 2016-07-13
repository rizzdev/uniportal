<?php
namespace Client\Controller;

use Application\Controller\CommonController;
use Doctrine\ORM\EntityManager;
use Entity\UserController;
use UniFi;
use Zend\View\Model\ViewModel;

class ControllerController extends CommonController
{

    public function indexAction()
    {
        /** @var EntityManager $em */
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $controllers = $em->getRepository('\Entity\UserController')->findBy(array('user' => $this->currentUser()));
        $data = array();

        if(is_array($controllers))
        {
            /** @var UserController $controller */
            foreach ($controllers as $controller)
            {
                $it = array(
                    'id' => $controller->getId(),
                    'user' => $controller->getUser()->getUserId(),
                    'base_url' => $controller->getBaseUrl(),
                    'username' => $controller->getUsername(),
                    'password' => $controller->getPassword(),
                    'version' => $controller->getVersion(),
                    'site' => $controller->getSite(),
                    'timestamp' => $controller->getTimestamp()->getTimestamp()
                );

                array_push($data, $it);
            }

            return array('data' => $data);
        }


        return new ViewModel();
    }

    public function createAction()
    {
        if ($this->posted())
        {
            $controllerData['base_url']   = $this->params()->fromPost('base_url');
            $controllerData['username']   = $this->params()->fromPost('username');
            $controllerData['password']   = $this->params()->fromPost('password');
            $controllerData['version']    = $this->params()->fromPost('version');

            $controller = new UserController();
            $controller->setBaseUrl($controllerData['base_url']);
            $controller->setUsername($controllerData['username']);
            $controller->setPassword($controllerData['password']);
            $controller->setVersion($controllerData['version']);
            $controller->setSite('default');
            $controller->setTimestamp(new \DateTime('now'));
            $controller->setUser($this->currentUser());

            /** @var EntityManager $em */
            $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
            $em->persist($controller);
            $em->flush();

            $unifi = new UniFi($controller);

            if ($unifi->login()) {
                $this->flashMessenger()->addSuccessMessage('Successfully connected to panel');
            } else {
                $this->flashMessenger()->addErrorMessage('Unable to connect to panel, please verify the following information is correct');
            }

            return $this->redirect()->toUrl('/client/controller/view/' . $controller->getId());

        }

        return array();
    }

    public function viewAction()
    {
        /** @var EntityManager $em */
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $id = $this->params()->fromRoute('id');
        /** @var UserController $controller */
        $controller = $em->getRepository('\Entity\UserController')->find($id);


        if (is_object($controller)) {

            if ($this->currentUser()->getUserId() != $controller->getUser()->getUserId()) {
                $this->flashMessenger()->addErrorMessage('The controller you attempted to view does not belong to you');
                return $this->redirect()->toUrl('/client/controller');
            }

            if ($this->posted()) {

                $controllerData['base_url']   = $this->params()->fromPost('base_url');
                $controllerData['username']   = $this->params()->fromPost('username');
                $controllerData['password']   = $this->params()->fromPost('password');
                $controllerData['version']    = $this->params()->fromPost('version');
                $controllerData['site']       = $this->params()->fromPost('site');

                $controller->setBaseUrl($controllerData['base_url']);
                $controller->setUsername($controllerData['username']);
                $controller->setPassword($controllerData['password']);
                $controller->setVersion($controllerData['version']);
                $controller->setSite($controllerData['site']);
                $em->persist($controller);
                $em->flush();

            }

            $unifi = new UniFi($controller);

            if ($unifi->login()) {
                $this->flashMessenger()->addSuccessMessage('Successfully connected to panel');

                $sites = $unifi->list_sites();

                if($controller->getSite() != null || $controller->getSite() != "null")
                {
                    $settings = $unifi->list_settings();
                    $wlanConfig = $unifi->list_wlanconf();
                }

                $gather = array(
                    'sites' => $sites,
                    'settings' => $settings,
                    'wlanConfig' => $wlanConfig

                );

            } else {
                $this->flashMessenger()->addErrorMessage('Unable to connect to panel, please verify the following information is correct');
            }

            $controllerInformation = array(
                'id' => $controller->getId(),
                'base_url' => $controller->getBaseUrl(),
                'username' => $controller->getUsername(),
                'password' => $controller->getPassword(),
                'version' => $controller->getVersion(),
                'site' => $controller->getSite(),
                'timestamp' => $controller->getTimestamp(),
                'panel' => $gather,
            );

            return array('data' => $controllerInformation);
        }

        return $this->redirect()->toUrl('/client/controller');

    }

    public function removeAction()
    {
        return array();
    }

}
