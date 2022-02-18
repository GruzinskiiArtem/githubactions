<?php

namespace Accord\Integration\Helper;

use Accord\Integration\Api\Client\ConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;
use Accord\Integration\Api\Client\Config;
use GuzzleHttp\HandlerStack;

class ClientConfig extends AbstractHelper implements ConfigInterface
{
    const XML_PATH_ACCORD_REST_API_API_TYPE = 'integration_accord/rest_parameters/apiType';
    const XML_PATH_ACCORD_REST_API_ENDPOINT = 'integration_accord/rest_parameters/apiEndpoint';
    const XML_PATH_ACCORD_REST_API_USERNAME = 'integration_accord/rest_parameters/apiUsername';
    const XML_PATH_ACCORD_REST_API_PASSWORD = 'integration_accord/rest_parameters/apiPassword';

    protected $config;

    public function __construct(Context $context)
    {
        parent::__construct($context);

        $data = [
            'apiEndpoint' => $this->getConfigValue(self::XML_PATH_ACCORD_REST_API_ENDPOINT),
            'apiUsername' => $this->getConfigValue(self::XML_PATH_ACCORD_REST_API_USERNAME),
            'apiPassword' => $this->getConfigValue(self::XML_PATH_ACCORD_REST_API_PASSWORD),
            'apiType' => $this->getConfigValue(self::XML_PATH_ACCORD_REST_API_API_TYPE),
        ];

        $this->config = new Config($data);
    }

    protected function getConfigValue($configPath)
    {
        return $this->scopeConfig->getValue(
            $configPath,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @inheritDoc
     */
    public function getRestApiEndpoint(): ?string
    {
        return $this->config->getRestApiEndpoint();
    }

    /**
     * @inheritDoc
     */
    public function getRestApiUsername(): ?string
    {
        return $this->config->getRestApiUsername();
    }

    /**
     * @inheritDoc
     */
    public function getRestApiPassword(): ?string
    {
        return $this->config->getRestApiPassword();
    }


    /**
     * @return HandlerStack|null
     */
    public function getHandler(): ?HandlerStack
    {
        return $this->config->getHandler();
    }

    /**
     * @return string|null
     */
    public function getApiType(): ?string
    {
        return $this->config->getApiType();
    }
}
