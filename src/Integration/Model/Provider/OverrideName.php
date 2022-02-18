<?php

declare(strict_types=1);

namespace Accord\Integration\Model\Provider;

use Accord\Integration\Model\Config\Source\OverrideName as Source;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\ScopeInterface;

/**
 * Class OverrideName
 */
class OverrideName
{
    /**
     * @var string
     */
    private const XML_PATH_ACCORD_OVERRIDE_NAME = 'integration_accord/submit_order/override_name';

    /**
     * @var Quote|null
     */
    private $quote = null;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * OverrideName constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param Quote $quote
     *
     * @return string
     */
    public function get(Quote $quote): string
    {
        $keys = $this->getKeys($quote->getStoreId());
        $result = $this->getOverrideName($quote, $keys);

        return (!$result && $quote->getCustomerIsGuest())
            ? $this->getDefaultOverrideName($quote)
            : $result;
    }

    /**
     * @param Quote $quote
     *
     * @return string
     */
    public function getDefaultOverrideName(Quote $quote): string
    {
        return $quote->getCustomerFirstname() . Source::SEPARATOR . $quote->getCustomerLastname();
    }

    /**
     * @param Quote $quote
     * @param array $keys
     *
     * @return string
     */
    private function getOverrideName(Quote $quote, array $keys): string
    {
        return implode(' ', array_filter(array_map(
            static function ($key) use ($quote) {
                return $quote->getShippingAddress()->getData($key);
            },
            $keys
        )));
    }

    /**
     * @return array
     */
    private function getKeys(int $storeId): array
    {
        return explode(
            Source::SEPARATOR,
            $this->scopeConfig->getValue(
                self::XML_PATH_ACCORD_OVERRIDE_NAME,
                ScopeInterface::SCOPE_STORE,
                $storeId
            )
        );
    }
}