<?php
namespace Accord\Integration\Api\Request;

use Accord\Customer\Helper\Customer\Customer;

class User extends Request
{

    protected function convert($data)
    {
        if (!isset($data['userCode']) || !$data['userCode']) {
            throw new RequestException('Invalid userCode', 0, $this);
        }

        if (!isset($data['userType']) || !$data['userType']) {
            throw new RequestException('Invalid userType', 0, $this);
        }

        if (!in_array($data['userType'], Customer::ACCORD_CUSTOMER_TYPES)) {
            throw new RequestException('Invalid userType', 0, $this);
        }

        $data['userType'] = str_replace(' ', '', $data['userType']);

        return (array)$data;
    }

}