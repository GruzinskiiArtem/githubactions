<?php
namespace Accord\Integration\Helper;

use Accord\Integration\Api\Client\ClientInterface;
use Accord\Integration\Api\Client\Config;
use Accord\Integration\Api\Commands\Heartbeat;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class HeartbeatCheck extends AbstractHelper
{
    /**
     * @var ClientInterface
     */
    private $client;

    public function __construct(Context $context, ClientInterface $client)
    {
        parent::__construct($context);
        $this->client = $client;
    }

    public function check(array $fieldSetData)
    {
        $config = new Config($fieldSetData);
        $this->client->init($config);
        $request = new \Accord\Integration\Api\Request\EmptyRequest();
        $response = new \Accord\Integration\Api\Response\EmptyResponse();
        $heartbeat = new Heartbeat($this->client, $request, $response);
        return $heartbeat->execute();
    }

}