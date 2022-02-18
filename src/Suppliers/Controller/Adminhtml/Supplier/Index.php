<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Accord\Suppliers\Controller\Adminhtml\Supplier;

class Index extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
    protected $resourceConnection;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__("Suppliers"));
        return $resultPage;
    }
}

