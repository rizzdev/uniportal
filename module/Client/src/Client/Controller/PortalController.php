<?php
namespace Client\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class PortalController extends AbstractActionController
{

    public function indexAction()
    {

        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toUrl('user/login');
        }

        return new ViewModel();
    }

    public function createAction()
    {

        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toUrl('user/login');
        }
        
        return new ViewModel();
    }
}
