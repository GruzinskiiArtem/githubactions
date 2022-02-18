<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Accord\Suppliers\Ui\Component\Listing\Column;

class SupplierActions extends \Magento\Ui\Component\Listing\Columns\Column
{
    const URL_PATH_DELETE = 'accord_suppliers/supplier/delete';
    const URL_PATH_DETAILS = 'accord_suppliers/supplier/details';
    const URL_PATH_EDIT = 'accord_suppliers/supplier/edit';

    protected $urlBuilder;

    /**
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {
            if (!isset($item['entity_id'])) {
                continue;
            }
            $item[$this->getData('name')] = [
                'edit' => [
                    'href' => $this->urlBuilder->getUrl(
                        static::URL_PATH_EDIT,
                        [
                            'entity_id' => $item['entity_id']
                        ]
                    ),
                    'label' => __('Edit')
                ]
            ];
        }
        
        return $dataSource;
    }
}
