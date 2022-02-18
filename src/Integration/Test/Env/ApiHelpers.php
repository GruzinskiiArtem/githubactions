<?php
namespace Accord\Integration\Test\Env;

use Accord\Integration\Api\Client\Config;

trait ApiHelpers
{
    use ObjectManager;

    /**
     * @return $class
     */
    protected function getPhalcon_Api($class)
    {
        $config = new Config($this->getPhalconConfig());
        return $this->getObjectManager()->create($class, ['config' => $config]);
    }

    /**
     * @return $class
     */
    protected function getAccord_Api($class)
    {
        $config = new Config($this->getAccordConfig());
        return $this->getObjectManager()->create($class, ['config' => $config]);
    }

    /**
     * @return $class
     */
    protected function getAmazon_Api($class)
    {
        $config = new Config($this->getAmazonConfig());
        return $this->getObjectManager()->create($class, ['config' => $config]);
    }

    /**
     * @return array
     */
    protected function getPhalconConfig()
    {
        return include __DIR__ . '/config.php';
    }

    /**
     * @return array
     */
    private function getAccordConfig()
    {
        return [
            'apiEndpoint' => 'http://217.113.161.68:8810/am2/rest/api',
            'apiUsername' => 'restuser',
            'apiPassword' => 'password',
        ];
    }

    /**
     * @return array
     */
    private  function getAmazonConfig()
    {
        return [
            'apiEndpoint' => 'http://217.113.161.68:8830/am2/rest/api',
            'apiUsername' => 'restuser',
            'apiPassword' => 'password',
        ];
    }

}
