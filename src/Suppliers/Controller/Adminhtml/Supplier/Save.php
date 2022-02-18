<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Accord\Suppliers\Controller\Adminhtml\Supplier;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{
    protected $dataPersistor;

    protected $supplier;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Accord\Suppliers\Model\SupplierFactory $supplier
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->supplier = $supplier;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('entity_id');
        
            $model = $this->supplier->create()->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Supplier no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            if (isset($data['logo'][0]['name']) && isset($data['logo'][0]['tmp_name'])) {
                $data['image'] =$data['logo'][0]['name'];
                $this->imageUploader = \Magento\Framework\App\ObjectManager::getInstance()->get(
                    'Accord\Suppliers\UiImageUpload'
                );
                $this->imageUploader->moveFileFromTmp($data['image']);
            } elseif (isset($data['logo'][0]['image']) && !isset($data['logo'][0]['tmp_name'])) {
                $data['image'] = $data['logo'][0]['image'];
            } else {
                $data['image'] = null;
            }
        
            $model->setData($data);
        
            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Supplier.'));
                $this->dataPersistor->clear('accord_suppliers_supplier');
        
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['entity_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Supplier.'));
            }
        
            $this->dataPersistor->set('accord_suppliers_supplier', $data);
            return $resultRedirect->setPath('*/*/edit', ['entity_id' => $this->getRequest()->getParam('entity_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}

