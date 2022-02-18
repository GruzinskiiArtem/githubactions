<?php

namespace Accord\Integration\Api\Request;

interface RequestInterface
{
    /**
     * @param mixed $data
     * @return $this
     */
    public function setData($data);

    /**
     * @return array
     */
    public function getData();

    /**
     * @return array
     */
    public function getHeaders();
}
