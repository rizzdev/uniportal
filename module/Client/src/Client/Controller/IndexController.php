<?php
namespace Client\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {

        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toUrl('client/login');
        }
        
        return new ViewModel();
    }
}
