<?php
namespace Accord\Integration\Api\Request;

class UpdateSettings extends Request
{
    /**
     * @var string
     */
    protected $currentId;

    /**
     * @param array $data
     * @return array
     */
    protected function convert($data)
    {
        if (!$data) {
            throw $this->error('Data is empty');
        }

        $data = array_map(function ($item) {
            return [
                'setting' => $this->getSetting($item),
                'label' => $this->getLabel($item),
                'category' => $this->getCategory($item),
                'value' => $this->getValue($item),
            ];
        }, $data);

        $data = (array)$data;
        return $data;
    }

    /**
     * @param array $item
     * @return string
     */
    protected function getSetting(array $item)
    {
        $this->currentId = '';
        if (!isset($item['setting'])) {
            throw $this->error('Invalid param item');
        }
        $id = $item['setting'];

        return $this->currentId = $id;
    }

    /**
     * @param array $item
     * @return string
     */
    protected function getLabel(array $item)
    {
        if (!isset($item['label'])) {
            throw $this->error('Invalid item label');
        }

        return $item['label'];
    }

    /**
     * @param array $item
     * @return string
     */
    protected function getCategory(array $item)
    {
        if (!isset($item['category'])) {
            throw $this->error('Invalid item category');
        }

        return $item['category'];
    }

    /**
     * @param array $item
     * @return string
     */
    protected function getValue(array $item)
    {
        return $item['value'];
    }

    /**
     * @param $message
     * @param int $code
     * @return RequestException
     */
    protected function error($message, $code = 0)
    {
        return new RequestException($message . ' ' . $this->currentId, $code, $this);
    }

}