<?php

namespace Accord\Integration\Api\Request;

use Accord\Customer\Helper\Current\User\Data\Depot;

class CustomerProductInfo extends Request
{
    const CUSTOMER_CODE_KEY = 'customerCode';
    const PRODUCT_SKUS_KEY  = 'productSkus';

    /**
     * @param mixed $data
     * @return array|mixed
     */
    protected function convert($data)
    {
        if (!$data) {
            throw new RequestException('Data is empty', 0, $this);
        }

        if (!isset($data[self::CUSTOMER_CODE_KEY])) {
            throw new RequestException('customerCode is empty', 0, $this);
        }

        if (!isset($data[self::PRODUCT_SKUS_KEY])) {
            throw new RequestException('productSkus is empty', 0, $this);
        }

        if (!is_array($data[self::PRODUCT_SKUS_KEY]) || !count($data[self::PRODUCT_SKUS_KEY])) {
            throw new RequestException('productSkus must be array', 0, $this);
        }

        if (isset($data[Depot::DEPOT_CODE]) && !is_string($data[Depot::DEPOT_CODE])) {
            throw new RequestException('depotCode isn\'t string', 0, $this);
        }

        // Force reset array keys to ensure that it will
        // be converted to JSON array instead of JSON object
        $data[self::PRODUCT_SKUS_KEY] = array_values($data[self::PRODUCT_SKUS_KEY]);

        return $data;
    }
}
