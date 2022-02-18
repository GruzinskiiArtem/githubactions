<?php

namespace Accord\Integration\Api\Request;

class PaymentStatus extends Request
{
    protected function convert($data)
    {
        if (!$data) {
            throw new RequestException('Data is empty', 0, $this);
        }

        if (!isset($data['depot'])) {
            throw new RequestException('depot is empty', 0, $this);
        }

        if (!isset($data['orderNumber'])) {
            throw new RequestException('orderNumber is empty', 0, $this);
        }

        // @todo validation
        if (!isset($data['createdDate'])) {
            throw new RequestException('createdDate is empty', 0, $this);
        }

        if (!isset($data['status'])) {
            throw new RequestException('status is empty', 0, $this);
        }

        return $data;
    }
}
