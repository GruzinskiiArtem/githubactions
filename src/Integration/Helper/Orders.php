<?php

namespace Accord\Integration\Helper;

class Orders extends \Accord\Integration\Helper\Api
{
    /**
     * @param $orderId
     * @return \Accord\Integration\Api\Response\ResponseInterface
     */
    public function getOrders($orderId)
    {
        $request = $this->objectManager->get(\Accord\Integration\Api\Request\Orders::class);
        $response = $this->objectManager->get(\Accord\Integration\Api\Response\Orders::class);
        return (new \Accord\Integration\Api\Commands\GetOrders($this->client, $request, $response))->execute($orderId);
    }

}
