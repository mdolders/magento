<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) 2016 Total Internet Group B.V. (http://www.tig.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 *
 * This entire class is extended from Magento's core class for backwards compatibility on Magento 1.6.
 *
 * @method Varien_Data_Form_Element_Abstract getElement()
 * @method TIG_PostNL_Block_Adminhtml_System_Config_Form_Fieldset setElement(Varien_Data_Form_Element_Abstract $element)
 */
class TIG_PostNL_Block_Adminhtml_System_Config_Form_Fieldset extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    /**
     * Return header comment part of html for fieldset
     *
     * @param Varien_Data_Form_Element_Abstract $element
     *
     * @return string
     */
    protected function _getHeaderCommentHtml($element)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $comment = $element->getComment();
        if (!$comment) {
            return '';
        }

        $commentHtml = '<div class="box">'
                     .     '<p>'
                     .         $comment
                     .     '</p>'
                     . '</div>';

        return $commentHtml;
    }

    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $html = $this->_getHeaderHtml($element);

        /** @noinspection PhpUndefinedMethodInspection */
        foreach ($element->getSortedElements() as $field) {
            /** @noinspection PhpUndefinedMethodInspection */
            $html.= $field->toHtml();
        }

        $html .= $this->_getFooterHtml($element);

        return $html;
    }

    /**
     * Return header html for fieldset
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getHeaderHtml($element)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        if ($element->getIsNested()) {
            $html = '<tr class="nested"><td colspan="4"><div class="' . $this->_getFrontendClass($element) . '">';
        } else {
            $html = '<div class="' . $this->_getFrontendClass($element) . '">';
        }

        $html .= $this->_getHeaderTitleHtml($element);

        $html .= '<input id="'.$element->getHtmlId() . '-state" name="config_state[' . $element->getId()
            . ']" type="hidden" value="' . (int)$this->_getCollapseState($element) . '" />';
        $html .= '<fieldset class="' . $this->_getFieldsetCss($element) . '" id="' . $element->getHtmlId() . '">';
        /** @noinspection PhpUndefinedMethodInspection */
        $html .= '<legend>' . $element->getLegend() . '</legend>';

        $html .= $this->_getHeaderCommentHtml($element);

        // field label column
        $html .= '<table cellspacing="0" class="form-list"><colgroup class="label" /><colgroup class="value" />';
        if ($this->getRequest()->getParam('website') || $this->getRequest()->getParam('store')) {
            $html .= '<colgroup class="use-default" />';
        }
        $html .= '<colgroup class="scope-label" /><colgroup class="" /><tbody>';

        return $html;
    }

    /**
     * Get frontend class
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getFrontendClass($element)
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $frontendClass = (string)$this->getGroup($element)->frontend_class;
        return 'section-config' . (empty($frontendClass) ? '' : (' ' . $frontendClass));
    }

    /**
     * Get group xml data of the element
     *
     * @param null|Varien_Data_Form_Element_Abstract $element
     * @return Mage_Core_Model_Config_Element
     */
    public function getGroup($element = null)
    {
        if (is_null($element)) {
            $element = $this->getElement();
        }
        /** @noinspection PhpUndefinedMethodInspection */
        if ($element && $element->getGroup() instanceof Mage_Core_Model_Config_Element) {
            /** @noinspection PhpUndefinedMethodInspection */
            return $element->getGroup();
        }

        return new Mage_Core_Model_Config_Element('<config/>');
    }

    /**
     * Return header title part of html for fieldset
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getHeaderTitleHtml($element)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return '<div class="entry-edit-head collapseable" ><a id="' . $element->getHtmlId()
        . '-head" rel="' . $element->getHtmlId() . '-head" href="#" onclick="Fieldset.toggleCollapse(\''
        . $element->getHtmlId() . '\', \'' . $this->getUrl('*/*/state') . '\'); return false;">'
        . $element->getLegend() . '</a></div>';
    }

    /**
     * Return full css class name for form fieldset
     *
     * @param null|Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getFieldsetCss($element = null)
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $configCss = (string)$this->getGroup($element)->fieldset_css;
        return 'config collapseable' . ($configCss ? ' ' . $configCss : '');
    }

    /**
     * Return footer html for fieldset
     * Add extra tooltip comments to elements
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getFooterHtml($element)
    {
        $tooltipsExist = false;
        $html = '</tbody></table>';
        $html .= '</fieldset>' . $this->_getExtraJs($element, $tooltipsExist);

        /** @noinspection PhpUndefinedMethodInspection */
        if ($element->getIsNested()) {
            $html .= '</div></td></tr>';
        } else {
            $html .= '</div>';
        }
        return $html;
    }

    /**
     * Return js code for fieldset:
     * - observe fieldset rows;
     * - apply collapse;
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @param bool $tooltipsExist Init tooltips observer or not
     * @return string
     */
    protected function _getExtraJs($element, $tooltipsExist = false)
    {
        $id = $element->getHtmlId();
        $js = "Fieldset.applyCollapse('{$id}');";

        /** @var Mage_Adminhtml_Helper_Js $helper */
        $helper = Mage::helper('adminhtml/js');
        return $helper->getScript($js);
    }

    /**
     * Collapsed or expanded fieldset when page loaded?
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return bool|int
     */
    protected function _getCollapseState($element)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        if ($element->getExpanded() !== null) {
            return 1;
        }

        /** @var Mage_Admin_Model_Session $session */
        $session = Mage::getSingleton('admin/session');
        /** @noinspection PhpUndefinedMethodInspection */
        $extra = $session->getUser()->getExtra();
        if (isset($extra['configState'][$element->getId()])) {
            return $extra['configState'][$element->getId()];
        }
        return false;
    }
}
