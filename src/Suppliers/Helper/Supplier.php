<?php

namespace Accord\Suppliers\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Accord\Api\Helper\AttributeManager;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Helper\ImageFactory;

class Supplier extends AbstractHelper
{
    
    const PLACEHOLDER_IMAGE_XML_PATH = 'catalog/placeholder/image_placeholder';
    const PLACEHOLDER_MEDIA_PATH = 'catalog/product/placeholder/';

    protected $storeManager;

    protected $supplier;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        \Accord\Suppliers\Model\SupplierFactory $supplier
    ) {
        $this->storeManager = $storeManager;
        $this->supplier = $supplier;
        parent::__construct($context);
    }

    public function getSupplier($product)
    {
        if (empty($product->getData(AttributeManager::SUPPLIER))) {
            return '';
        }
        $supplier = $this->supplier->create()->load($product->getData(AttributeManager::SUPPLIER));
        return (($supplier->getId() && $supplier->getStatus()) ? $supplier : '');
    }

    public function getImageSupplier($supplier)
    {
        if ($supplier->getImage()){
            $url = $this->storeManager->getStore()->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) . 'suppliers/' . $supplier->getImage();
        } else {
            $mediaUrl = $this ->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
            $config = $this->storeManager->getStore()->getConfig(self::PLACEHOLDER_IMAGE_XML_PATH);
            $url = $mediaUrl . self::PLACEHOLDER_MEDIA_PATH . $config;
        }

        return $url;
    }
}
