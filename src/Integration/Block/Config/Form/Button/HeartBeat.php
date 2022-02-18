<?php
/**
 * Created by PhpStorm.
 * User: motaevns
 * Date: 08.05.16
 * Time: 8:39
 */

namespace Accord\Integration\Block\Config\Form\Button;

use \Magento\Config\Block\System\Config\Form\Field;
use \Magento\Framework\Data\Form\Element\AbstractElement;

class HeartBeat extends Field
{
    /**
     * Path to block template
     */
    const CHECK_TEMPLATE = 'system/config/button/heartBeat.phtml';
    /**
     * Set template to itself
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate(static::CHECK_TEMPLATE);
        }
        return $this;
    }
    /**
     * Render button
     *
     * @param  AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        // Remove scope label
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }
    /**
     * Get the button and scripts contents
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $this->addData(
            [
//                'api_domain_id' => $originalData['api_domain_id'],
                'intern_url' => $this->getUrl($originalData['button_url']),
                'button_label' => $originalData['button_label'],
                'html_id' => $element->getHtmlId(),
            ]
        );

        return $this->_toHtml();
    }
}
