<?php
namespace Accord\Integration\Api\Request\Calculation;

use Accord\Integration\Api\Request\RequestException;

abstract class AbstractData extends \Accord\Integration\Api\Request\User
{
    /**
     * @param array $data
     * @return array
     */
    protected function convert($data)
    {
        if (!isset($data['rowsToReturn'])) {
            throw new RequestException('rowsToReturn must be set', 0, $this);
        }

        $data['rowsToReturn'] = (int)$data['rowsToReturn'];

        return parent::convert($data);
    }

}