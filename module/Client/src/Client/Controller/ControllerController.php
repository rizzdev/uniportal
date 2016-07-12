<?php
namespace Client\Controller;

use Application\Controller\CommonController;
use Doctrine\ORM\EntityManager;
use Entity\UserController;
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
            $controllerData['controller_base_url'] = $this->params()->fromPost('controller_base_url');
            $controllerData['controller_username']   = $this->params()->fromPost('controller_username');
            $controllerData['controller_password']   = $this->params()->fromPost('controller_password');
            $controllerData['controller_version']    = $this->params()->fromPost('controller_version');
            $controllerData['controller_site']       = $this->params()->fromPost('controller_site');

            $controller = new UserController();
            $controller->setBaseUrl($controllerData['controller_base_url']);
            $controller->setUsername($controllerData['controller_username']);
            $controller->setPassword($controllerData['controller_password']);
            $controller->setVersion($controllerData['controller_version']);
            $controller->setSite($controllerData['controller_site']);
            $controller->setTimestamp(new \DateTime('now'));
            $controller->setUser($this->currentUser());

            /** @var EntityManager $em */
            $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
            $em->persist($controller);
            $em->flush();

            $this->flashMessenger()->addSuccessMessage('Your controller has been added');
            return $this->redirect()->toUrl('/client/controller');

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

            $controllerInformation = array(
                'id' => $controller->getId(),
                'base_url' => $controller->getBaseUrl(),
                'username' => $controller->getUsername(),
                'password' => $controller->getPassword(),
                'version' => $controller->getVersion(),
                'site' => $controller->getSite(),
                'timestamp' => $controller->getTimestamp()
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
