<?php
/**
 * Created by PhpStorm.
 * User: A
 * Date: 7/12/2016
 * Time: 2:30 PM
 */

namespace Client\Controller;


use Application\Controller\CommonController;
use Entity\UserController;
use UniFi;
use Zend\View\Model\JsonModel;

class ApiController extends CommonController
{
    public function testConnectionAction()
    {
        $controller = $this->currentController();

        if (is_object($controller)) {
            /** @var UserController $controller */
            $unifi = new UniFi($controller);

            if ($unifi->login()) {
                return new JsonModel(array(
                    'result' => 'success',
                    'message' => 'Successfully able to login to Access Point'
                ));
            } else {
                return new JsonModel(array(
                    'result' => 'failure',
                    'message' => 'Was unable to login to Access Point'
                ));
            }
        }

        return new JsonModel(array(
            'result' => 'failure',
            'message' => 'Invalid Controller Parameters'
        ));
    }

    public function sitesAction()
    {
        $controller = $this->currentController();

        if (is_object($controller)) {

            /** @var UserController $controller */
            $unifi = new UniFi($controller);

            if ($unifi->login()) {
                return new JsonModel(array(
                    'result' => 'success',
                    'message' => 'Successfully retrieved list of sites',
                    'data' => $unifi->list_sites()
                ));
            } else {
                return new JsonModel(array(
                    'result' => 'failure',
                    'message' => 'Was unable to login to Access Point'
                ));
            }
        }

        return new JsonModel(array(
            'result' => 'failure',
            'message' => 'Invalid Controller Parameters'
        ));
    }

    public function updateGuestSettingsAction()
    {
        $data = array(
            'auth' => $this->params()->fromPost('auth'),
            'portal_enabled' => $this->params()->fromPost('portal_enabled'),
            'custom_ip' => $this->params()->fromPost('custom_ip'),
            'portal_use_hostname' => $this->params()->fromPost('portal_use_hostname'),
            'portal_hostname' => $this->params()->fromPost('portal_hostname'),
        );


        $result = $this->unifi()->set_guestlogin_settings(
            json_encode($data, JSON_UNESCAPED_SLASHES));

        if($result)
            return $this->createApiCall(true, 'Successfully updated guest settings');
        else
            return $this->createApiCall(false, 'Failed to update guest settings');
    }
}