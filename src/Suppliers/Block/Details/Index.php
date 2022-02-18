<?php

declare(strict_types=1);

namespace Accord\Suppliers\Block\Details;

class Index extends \Magento\Framework\View\Element\Template
{
    protected $supplier;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Accord\Suppliers\Model\SupplierFactory $supplier,
        array $data = []
    ) {
        $this->supplier = $supplier;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        $supplierId = $this->getRequest()->getParams('supplier_id');
        if ($supplierId) {
            $supplier = $this->supplier->create()->load($supplierId);
            if ($supplier->getId()) {
                $this->supplier = $supplier;
            }
        }

        return parent::_prepareLayout();
    }

    public function getSupplier()
    {
        return $this->supplier;
    }
}

