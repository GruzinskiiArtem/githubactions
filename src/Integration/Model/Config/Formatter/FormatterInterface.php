<?php
namespace Accord\Integration\Model\Config\Formatter;

interface FormatterInterface
{
    /**
     * @param mixed $value
     * @return mixed
     */
    public function format($value);
}
