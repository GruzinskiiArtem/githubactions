<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Accord\Suppliers\Controller\Adminhtml\Supplier;

class Edit extends \Accord\Suppliers\Controller\Adminhtml\Supplier
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory 
     */
    protected $resultPageFactory;

    /**
     * @var \Accord\Suppliers\Model\SupplierFactory
     */
    protected $supplierModel;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Accord\Suppliers\Model\SupplierFactory $supplier
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->supplierModel = $supplier;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('entity_id');

        // 2. Initial checking
        if ($id) {
            $supplier = $this->supplierModel->create()->load($id);
            if (!$supplier->getId()) {
                $this->messageManager->addErrorMessage(__('This Supplier no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->coreRegistry->register('accord_suppliers_supplier', $this->supplierModel->create());
        
        // 3. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Supplier') : __('New Supplier'),
            $id ? __('Edit Supplier') : __('New Supplier')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Suppliers'));
        $resultPage->getConfig()->getTitle()->prepend($supplier->getId() ? __($supplier->getName()) : __('New Supplier'));
        return $resultPage;
    }
}

