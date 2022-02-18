<?php
namespace Accord\Integration\Test\Env;

use Magento\Framework\App\Bootstrap;

trait ObjectManager
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private static $objectManager;

    /**
     * @return \Magento\Framework\ObjectManagerInterface
     */
    protected function getObjectManager()
    {
        if (!self::$objectManager) {
            $bootstrap = Bootstrap::create(BP, []);
            $bootstrap->createApplication('Magento\Framework\App\Http');
            self::$objectManager = $bootstrap->getObjectManager();
        }
        return self::$objectManager;
    }

}
