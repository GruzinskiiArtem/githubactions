<?php
namespace Accord\Integration\Api\Request;

class Documents extends Request
{

    /**
     * @param array $data
     * @return array
     */
    protected function convert($data)
    {
        if (!$data['docType']) {
            throw new RequestException('docType is required', 0, $this);
        }

        if (!$data['docRef'] && !is_array($data['docRef'])) {
            throw new RequestException('docRef is invalid', 0, $this);
        }

        $data['docRef'] = \GuzzleHttp\json_encode($data['docRef']);

        return $data;
    }

}