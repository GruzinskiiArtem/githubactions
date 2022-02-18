<?php
namespace Accord\Integration\Api\Request;

class ValidateProducts extends User
{

    /**
     * @param array $data
     * @return array
     */
    protected function convert($data)
    {
        if (!isset($data['customerCode'])) {
            throw new RequestException('customerCode is empty', 0, $this);
        }

        if (isset($data['depot']) && !is_string($data['depot'])){
            throw new RequestException('depotCode is not valid', 0, $this);
        }

        return $data;
    }

}
