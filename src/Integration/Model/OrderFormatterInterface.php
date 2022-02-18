<?php

namespace Accord\Integration\Model;

interface OrderFormatterInterface
{
    public function toCsv(\Accord\Integration\Api\Response\Orders $order): string;

}