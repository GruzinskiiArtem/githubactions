<?php

declare(strict_types=1);

namespace Accord\Integration\Observer;

use Accord\Integration\Helper\ClientConfig;
use Accord\Integration\Helper\UpdateSettings as Api;
use Accord\Integration\Model\Config\FieldFormatter;
use Accord\Integration\Model\Config\Source\ApiType;
use Magento\Config\Model\Config\Structure;
use Magento\Config\Model\Config\Structure\Element\Field;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;

class AccordConfigUpdateObserver implements ObserverInterface
{
    private Api $apiHelper;

    private Structure $configStructure;

    private ManagerInterface $messageManager;

    private FieldFormatter $fieldFormatter;

    private ClientConfig $clientConfig;

    /**
     * @param Structure $configStructure
     * @param ManagerInterface $messageManager
     * @param FieldFormatter $fieldFormatter
     * @param Api $apiHelper
     * @param ClientConfig $clientConfig
     */
    public function __construct(
        Structure        $configStructure,
        ManagerInterface $messageManager,
        FieldFormatter   $fieldFormatter,
        Api              $apiHelper,
        ClientConfig     $clientConfig
    ) {
        $this->configStructure = $configStructure;
        $this->messageManager = $messageManager;
        $this->fieldFormatter = $fieldFormatter;
        $this->apiHelper = $apiHelper;
        $this->clientConfig = $clientConfig;
    }

    /**
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer): void
    {
        if ($this->clientConfig->getApiType() !== ApiType::TYPE_ACCORD) {
            return;
        }

        $config = $observer->getData('config_data');
        $sectionPath = $config['section'];
        $result = [];

        foreach ($config['groups'] as $groupId => $group) {
            foreach ($group['fields'] as $fieldId => $data) {
                $field = $this->getFieldElement($sectionPath, $groupId, $fieldId);
                $result[] = $this->fieldFormatter->format($field, $data['value'] ?? $data['inherit']);
            }
        }

        try {
            $this->apiHelper->updateSettings($result);
        } catch (RuntimeException $e) {
            $this->messageManager->addWarningMessage($e->getMessage());
        }
    }

    /**
     * @param string $sectionPath
     * @param string $groupId
     * @param string $fieldId
     *
     * @return Field
     */
    protected function getFieldElement(string $sectionPath, string $groupId, string $fieldId): Field
    {
        $groupPath = $sectionPath . '/' . $groupId;
        $field = $this->configStructure->getElement($groupPath . '/' . $fieldId);

        return $field;
    }
}
