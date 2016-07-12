<?php
namespace Portal\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class SController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }
}
