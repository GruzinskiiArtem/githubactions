<?php

namespace Accord\Integration\Api\Response;

/**
 * @property-read array $canNotOrderProductSkus
 */
class ValidateProducts extends Response
{
    /**
     * @var array
     */
    protected $defaults = [
        'canNotOrderProductSkus' => [],
    ];

    /**
     * @return void
     */
    protected function validate()
    {
        if (!is_array($this->canNotOrderProductSkus)) {
            throw $this->error('canNotOrderProductSkus is invalid');
        }
    }
}
