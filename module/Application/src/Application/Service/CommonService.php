<?php

namespace Application\Service;


use Doctrine\Entity;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class CommonService
{
    protected $em;
    protected $responseErrors = array();
    protected $responseSuccess = array();
    protected $success = true;
    protected $data = null;
    
    /**
     * @return EntityManager
     */
    public function database()
    {
        return $this->em;
    }

    public function commit($entity)
    {
        $this->database()->persist($entity);
        $this->database()->flush();
    }


    public function error($error)
    {
        $this->success = false;
        array_push($this->responseErrors, $error);
        return $this;
    }

    public function success($success)
    {
        array_push($this->responseSuccess, $success);
        return $this;
    }

    public function data($key, $data)
    {
        $this->data[$key] = $data;
        return $this;
    }

    public function returnData()
    {
        return array(
            'messages' => array(
                'errors' => $this->responseErrors,
                'success' => $this->responseSuccess,
            ),
            'success' => $this->success,
            'data' => $this->data
        );
    }

    public function renderEntity($entity, $ignore = array('user', 'owner') )
    {
        $normalizer = new GetSetMethodNormalizer();

        $normalizer->setCallbacks(
            array(
                'updatedTimestamp' => function ($dateTime) { return $dateTime instanceof \DateTime ? $dateTime->format(\DateTime::ISO8601) : '';},
                'expireTimestamp' => function ($dateTime) { return $dateTime instanceof \DateTime ? $dateTime->format(\DateTime::ISO8601) : '';})
        );

        $normalizer->setIgnoredAttributes($ignore);
        $encoder = new JsonEncoder();
        $serializer = new Serializer(array($normalizer), array($encoder));
        $data = $serializer->serialize($entity, 'json'); // Output: {"name":"foo"}
        return json_decode($data, true);
    }

}