<?php
namespace Accord\Integration\Helper;

class Heartbeat extends \Accord\Integration\Helper\Api
{

    /**
     * @return \Accord\Integration\Api\Response\EmptyResponse | \Accord\Integration\Api\Response\ResponseInterface
     */
    public function heartbeat()
    {
        $request = $this->objectManager->get(\Accord\Integration\Api\Request\EmptyRequest::class);
        $response = $this->objectManager->get(\Accord\Integration\Api\Response\EmptyResponse::class);
        return (new \Accord\Integration\Api\Commands\Heartbeat($this->client, $request, $response))->execute();
    }

}
