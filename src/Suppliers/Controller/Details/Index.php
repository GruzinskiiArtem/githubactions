<?php

declare(strict_types=1);

namespace Accord\Suppliers\Controller\Details;

use Accord\Suppliers\Model\SupplierFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 */
class Index extends Action
{
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var SupplierFactory
     */
    private $supplier;

    /**
     * @var ForwardFactory
     */
    private $resultForwardFactory;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param SupplierFactory $supplier
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        SupplierFactory $supplier,
        ForwardFactory $resultForwardFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->supplier = $supplier;
        $this->resultForwardFactory = $resultForwardFactory;

        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return ResponseInterface|Forward|Redirect|ResultInterface|Page
     */
    public function execute()
    {
        $supplierId = (int)$this->getRequest()->getParam('id');

        if (!$supplierId) {
            return $this->noSupplierRedirect();
        }

        $supplier = $this->supplier->create()->load($supplierId);

        if (!$supplier->getId()) {
            return $this->noSupplierRedirect();
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set($supplier->getSupplierName() ?: $supplier->getName());

        return $resultPage;
    }

    /**
     * No Supplier Redirect
     *
     * @return Forward|Redirect
     */
    protected function noSupplierRedirect()
    {
        $store = $this->getRequest()->getQuery('store');

        if (isset($store) && !$this->getResponse()->isRedirect()) {
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('');
        }

        $resultForward = $this->resultForwardFactory->create();
        $resultForward->forward('noroute');

        return $resultForward;
    }
}
