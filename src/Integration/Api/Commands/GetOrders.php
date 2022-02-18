<?php

declare(strict_types=1);

namespace Accord\Integration\Api\Commands;

use Accord\Integration\Api\Response\ResponseInterface;
use Accord\Integration\Api\Request\RequestException;

final class GetOrders extends Command
{
    /**
     * @param string $data
     * @return ResponseInterface
     */
    public function execute($data = null)
    {
        if (!isset($data)){
            throw new RequestException('Invalid OrderId', 0, $this);
        }

        $psrResponse = $this->getClient()->get('magento/orders/'. $data);

        return $this->getResponse()->setResponse($psrResponse);
    }
}
