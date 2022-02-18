<?php
namespace Accord\Integration\Model\Config;

class FieldFormatter
{
    /**
     * @var Formatter\FormatterInterface[]
     */
    protected $formatters;

    /**
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    protected $string;

    public function __construct(
        \Magento\Framework\Stdlib\StringUtils $string,
        array $formatters = []
    )
    {
        $this->string = $string;
        foreach ($formatters as $name => $formatter) {
            if (!$formatter instanceof Formatter\FormatterInterface) {
                throw new \Exception("Invalid type of '{$name}' formatter item'");
            }
        }

        $this->formatters = $formatters;
    }


    /**
     * @param \Magento\Config\Model\Config\Structure\Element\Field $field
     * @param mixed $value
     * @return mixed
     */
    public function format($field, $value)
    {
        return [
            'setting' => $field->getId(),
            'label' => $field->getLabel(),
            'category' => $this->getCategory($field),
            'value' => $this->formatValue($field, $value),
        ];
    }

    /**
     * @param \Magento\Config\Model\Config\Structure\Element\Field $field
     * @return string
     */
    private function getCategory($field)
    {
        $path = $field->getPath();
        list(, $group) = explode('/', $path);

        return $this->string->upperCaseWords($group, '_', ' - ');
    }

    /**
     * @param \Magento\Config\Model\Config\Structure\Element\Field $field
     * @param $value
     * @return mixed
     */
    protected function formatValue($field, $value)
    {

        if (!$field->hasSourceModel()) {
            return $value;
        }

        if (is_array($value)) {
            return implode(', ', $value);
        }
        
        $fieldData = $field->getData();
        $type = $fieldData['source_model'];

        if (!key_exists($type, $this->formatters)) {
            return $value;
        }

        $formatter = $this->formatters[$type];
        return $formatter->format($value);
    }

}
