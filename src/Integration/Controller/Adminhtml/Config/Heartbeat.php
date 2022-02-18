<?php
/**
 * Created by PhpStorm.
 * User: motaevns
 * Date: 08.05.16
 * Time: 22:58
 */
namespace Accord\Integration\Controller\Adminhtml\Config;
use \Magento\Backend\App\Action;

abstract class Heartbeat extends Action
{

    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Accord_Integration::config_heartbeat');
    }

}