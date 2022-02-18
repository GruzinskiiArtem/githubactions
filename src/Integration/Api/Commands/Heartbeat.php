<?php

declare(strict_types=1);

namespace Accord\Integration\Api\Commands;

use Accord\Integration\Api\Response\ResponseInterface;

final class Heartbeat extends Command
{
    /**
     * @param null $data
     * @return ResponseInterface
     */
    public function execute($data = null)
    {
        $psrResponse = $this->getClient()->get(
            $this->getUrl(),
            [
                'headers' => $this->getRequest()->getHeaders(),
                'connect_timeout' => 10,
            ]
        );

        return $this->getResponse()->setResponse($psrResponse);
    }
}
