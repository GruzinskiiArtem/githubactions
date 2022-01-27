<?php

namespace Accord\Shipping\Helper;

class Shipping
{
    const SEPARATOR = '::';
    const DESPATCH_DATE_FORMAT = 'Y-m-d\TH:i:s.uP';

    /**
     * @param \Accord\Integration\Api\Response\GetDeliveryDetails\DeliveryDateAvailable\ShippingOption $shippingOption
     * @return string
     */
    public function getShippingMethodCodeByShippingOption($shippingOption)
    {
        return implode(self::SEPARATOR, [
            $shippingOption->despatchDate->format(self::DESPATCH_DATE_FORMAT),
            $shippingOption->route,
            $shippingOption->carrierID,
            $shippingOption->maxOrderValue
        ]);
    }

    /**
     * @param string $methodCode
     * @return string|null
     */
    public function getDespatchDateByShippingMethodCode($methodCode)
    {
        $data = $this->extractShippingMethodDataFromCode($methodCode);
        return $data['despatchDate'];
    }

    /**
     * @param string $methodCode
     * @return string|null
     */
    public function getRouteByShippingMethodCode($methodCode)
    {
        $data = $this->extractShippingMethodDataFromCode($methodCode);
        return $data['route'];
    }

    /**
     * @param string $methodCode
     * @return int|null
     */
    public function getCarrierIdByShippingMethodCode($methodCode)
    {
        $data = $this->extractShippingMethodDataFromCode($methodCode);
        return $data['carrierId'];
    }

    /**
     * @param string $methodCode
     * @return number|null
     */
    public function getMaxOrderValueByShippingMethodCode($methodCode)
    {
        $data = $this->extractShippingMethodDataFromCode($methodCode);
        return $data['maxOrderValue'];
    }

    /**
     * @param string $methodCode
     * @return array
     */
    protected function extractShippingMethodDataFromCode($methodCode)
    {
        $data = [
            'despatchDate' => null,
            'route' => null,
            'carrierId' => null,
            'maxOrderValue' => null
        ];

        $methodCode = ltrim($methodCode, 'accordbase_');
        $parts = explode(self::SEPARATOR, $methodCode);

        if (count($parts) == 4) {
            $data['despatchDate'] = strval($parts[0]);
            $data['route'] = strval($parts[1]);
            $data['carrierId'] = intval($parts[2]);
            $data['maxOrderValue'] = floatval($parts[3]);
        }

        return $data;
    }
}
