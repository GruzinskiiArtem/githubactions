<?php

namespace Accord\Integration\Block\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Backend\Block\Template\Context;

class Version extends Field
{
    const PATH_COMPOSER_INSTALL_JSON = '/vendor/composer/installed.json';
    const COMPOSER_VERSION_NOT_AVAILABLE = 'N/A';
    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @var string
     */
    protected $package;

    /**
     * Version constructor.
     * @param DirectoryList $directoryList
     * @param Context $context
     * @param string $package
     * @param array $data
     */
    public function __construct(
        DirectoryList $directoryList,
        Context $context,
        string $package = 'accord/magento2',
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->directoryList = $directoryList;
        $this->package = $package;
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        return "<span style='position:relative;top:4px;'>" . $this->getPackageVersion($this->package) . "</span>";
    }

    /**
     * @param string $name
     * @return string
     */
    protected function getPackageVersion(string $name): string
    {
        $filepath = $this->directoryList->getRoot() . self::PATH_COMPOSER_INSTALL_JSON;

        if (!file_exists($filepath) || !is_readable($filepath)) {
            return self::COMPOSER_VERSION_NOT_AVAILABLE;
        }

        $packages = json_decode(file_get_contents($filepath));
        $packages = is_array($packages) ? $packages : $packages->packages;

        $packageData = array_values(
            array_filter($packages, function ($package) use ($name) { return $package->name === $name; })
        );
        $packageData = reset($packageData);

        return $packageData ? $packageData->version : self::COMPOSER_VERSION_NOT_AVAILABLE;
    }
}
