<?php
namespace Accord\Integration\Api\Request;


class GetDeliveryDetails extends Request
{

    protected function convert($data)
    {
        if (!isset($data['customerCode']) || !$data['customerCode']) {
            throw new RequestException('Invalid customerCode', 0, $this);
        }

        if (isset($data['depot']) && !is_string($data['depot'])) {
            throw new RequestException('depotCode is not valid', 0, $this);
        }

        return $data;
    }

}
