<?php
namespace Accord\Integration\Observer;

use Accord\Integration\Helper\HeartbeatCheck;
use Accord\Integration\Helper\ClientConfig;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;

class CheckConnectionObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * CheckConnectionObserver constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        $config = $observer->getData('config_data');
        $fieldPath = $config->getData('path');
        if (!$this->isAccordIntegrationSetting($fieldPath) || !$this->isEndPointField($fieldPath)) {
            return;
        }

        $fieldSet = $config->getData('fieldset_data');

        /**
         * @var HeartbeatCheck $heartbeatCheck
         */
        $heartbeatCheck = $this->objectManager->get(\Accord\Integration\Helper\HeartbeatCheck::class);
        $heartbeatCheck->check($fieldSet);
    }

    /**
     * @param string $path
     * @return bool
     */
    protected function isAccordIntegrationSetting($path)
    {
        return filter_var(preg_match('/^integration_accord/', $path), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @param $path
     * @return bool
     */
    protected function isEndPointField($path)
    {
        return $path == ClientConfig::XML_PATH_ACCORD_REST_API_ENDPOINT ? true : false;
    }
}