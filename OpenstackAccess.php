<?php

namespace Jamespot\Misc;

use Exception;
use OpenStack\ObjectStore\v1\Models\Container;
use OpenStack\ObjectStore\v1\Service;
use OpenStack\OpenStack;

require_once 'vendor/autoload.php';

class OpenstackAccess
{
    /**
     * @var OpenStack
     */
    private $client;
    /**
     * @var Service
     */
    private $service;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var string
     */
    private $url;
    /**
     * @var string
     */
    private $region;
    /**
     * @var string
     */
    private $projecId;
    /**
     * @var string
     */
    private $projectName;
    /**
     * @var Object
     */
    private $config;

    public function __construct($config)
    {
        $this->client = null;
        $this->service = null;
        $this->container = null;
        $this->config = $config;
    }

    private function openstackConfig($url, $region, $username, $password, $projectId)
    {
        return [
            'authUrl' => $url,
            'region'  => $region,
            'user'    => [
                'name'     => $username,
                'password' => $password,
                'domain'    => ['name' => 'Default', 'id' => "default"]
            ],
            'scope'   => [
                'project' => [
                    'id' => $projectId,
                    'domain' => ['name' => 'Default', 'id' => "default"],
                ],
            ]
        ];
    }

    /**
     *
     * @return OpenStack
     */
    private function _client($url, $region, $username, $password, $projectId, $projectName, $cachedToken = null)
    {

        $this->url = $url;
        $this->region = $region;
        $this->projectId = $projectId;
        $this->projectName = $projectName;

        $openstackConfig = $this->openstackConfig($url, $region, $username, $password, $projectId);

        if (is_array($cachedToken)) {
            $openstackConfig['cachedToken'] = $cachedToken;
        }

        $openstack = new OpenStack($openstackConfig);
        return $openstack;
    }


    private function containerFullName($name)
    {
        return $this->region . '-' . $name;
    }


    public function initClient()
    {
        $config = $this->config;
        if (!is_null($config)) {

            // Get a new token 
            $lClient = $this->_client($config->url, $config->region, $config->username, $config->password, $config->projectId, $config->projectName, null);
            $identity = $lClient->identityV3();
            $generatedToken = $identity->generateToken($this->openstackConfig($config->url, $config->region, $config->username, $config->password, $config->projectId));

            $token = $generatedToken->export();

            $this->client = $this->_client($config->url, $config->region, $config->username, $config->password, $config->projectId, $config->projectName, $token);
            $this->service = $this->client->objectStoreV1();

            $fullName = $this->containerFullName($config->containerName);
            try {
                if ($this->service->containerExists($fullName)) {
                    $this->container = $this->service->getContainer($fullName);
                    return true;
                }
            } catch (Exception $e) {
                echo($e->getMessage());
            }
        }
        return null;
    }

    /** ------------------------------------ Container functions ---------------------------------------**/
    /**
     * return string
     */
    private function containerFilename($id)
    {
        return 'upload/' . $id;
    }

    public function getFileObject($id)
    {
        return $this->getObject($this->containerFilename($id));
    }

    public function getFileData($id)
    {
        return $this->_getData($this->containerFilename($id))->getContents();
    }

    public function getObject($name)
    {
        return $this->container->getObject($name);
    }

    public function _getData($name)
    {
        return $this->getObject($name)->download();
    }

    public function list($params = [])
    {
        return $this->container->listObjects($params);
    }
}
