<?php
namespace Accord\Integration\Model\Config\Formatter;

class VatType implements FormatterInterface
{
    protected $types = [];

    public function __construct(array $addTypes)
    {
        $this->types = $addTypes;
    }


    /**
     * @param mixed $value
     * @return mixed
     */
    public function format($value)
    {
        if (!array_key_exists($value, $this->types)) {
            return $value;
        }

        return $this->types[$value];
    }
}
