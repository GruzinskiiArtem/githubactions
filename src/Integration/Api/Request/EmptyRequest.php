<?php
namespace Accord\Integration\Api\Request;

final class EmptyRequest extends Request
{

    protected function convert($data)
    {
        if ($data) {
            throw new RequestException('Request must be empty', 0, $this);
        }
    }

}