<?php
namespace Accord\Integration\Model\Config\Formatter;

class ToBooleanString implements FormatterInterface
{

    /**
     * @param mixed $value
     * @return mixed
     */
    public function format($value)
    {
        $result = (bool) $value;

        return $result ? 'true' : 'false';
    }
}
