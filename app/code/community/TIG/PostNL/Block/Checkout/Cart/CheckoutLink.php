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
 * @method boolean                                     hasPublicWebshopId()
 * @method boolean                                     hasDoLoginCheck()
 * @method boolean                                     hasButtonTestBaseUrl()
 * @method boolean                                     hasButtonLiveBaseUrl()
 *
 * @method TIG_PostNL_Block_Checkout_Cart_CheckoutLink setPublicWebshopId(string $value)
 * @method TIG_PostNL_Block_Checkout_Cart_CheckoutLink setDoLoginCheck(boolean $value)
 * @method TIG_PostNL_Block_Checkout_Cart_CheckoutLink setButtonTestBaseUrl(string $value)
 * @method TIG_PostNL_Block_Checkout_Cart_CheckoutLink setButtonLiveBaseUrl(string $value)
 */
class TIG_PostNL_Block_Checkout_Cart_CheckoutLink extends TIG_PostNL_Block_Core_Template
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'postnl_checkout_cart_checkoutlink';

    /**
     * Base URLs of the checkout button.
     */
    const CHECKOUT_BUTTON_TEST_BASE_URL_XPATH = 'postnl/checkout/checkout_button_test_base_url';
    const CHECKOUT_BUTTON_LIVE_BASE_URL_XPATH = 'postnl/checkout/checkout_button_live_base_url';

    /**
     * Xpath to public webshop ID setting.
     */
    const XPATH_PUBLIC_WEBSHOP_ID = 'postnl/cif/public_webshop_id';

    /**
     * Xpath to the 'instruction_cms_page' setting.
     */
    const XPATH_INSTRUCTION_CMS_PAGE = 'postnl/checkout/instruction_cms_page';

    /**
     * Xpath to 'show exclusively for mijnpakket users' setting.
     */
    const XPATH_SHOW_EXCLUSIVELY_FOR_MIJNPAKKET_USERS = 'postnl/checkout/show_exclusively_for_mijnpakket_users';

    /**
     * Gets the checkout URL.
     *
     * @return string
     */
    public function getCheckoutUrl()
    {
        /** @var Mage_Checkout_Helper_Url $helper */
        $helper = Mage::helper('checkout/url');
        $url = $helper->getCheckoutUrl();

        return $url;
    }

    /**
     * @return string
     */
    public function getButtonTestBaseUrl()
    {
        if ($this->hasButtonTestBaseUrl()) {
            return $this->_getData('button_test_base_url');
        }

        $baseUrl = Mage::getStoreConfig(self::CHECKOUT_BUTTON_TEST_BASE_URL_XPATH);

        $this->setButtonTestBaseUrl($baseUrl);
        return $baseUrl;
    }

    /**
     * @return string
     */
    public function getButtonLiveBaseUrl()
    {
        if ($this->hasButtonLiveBaseUrl()) {
            return $this->_getData('button_live_base_url');
        }

        $baseUrl = Mage::getStoreConfig(self::CHECKOUT_BUTTON_LIVE_BASE_URL_XPATH);

        $this->setButtonLiveBaseUrl($baseUrl);
        return $baseUrl;
    }

    /**
     * Returns whether or not we need to check if the current customer is logged in with mijnpakket before showing
     * PostNL Checkout.
     *
     * @return bool
     */
    public function getDoLoginCheck()
    {
        if ($this->hasDoLoginCheck()) {
            return $this->_getData('do_login_check');
        }

        $storeId = Mage::app()->getStore()->getId();
        $doLoginCheck = Mage::getStoreConfigFlag(self::XPATH_SHOW_EXCLUSIVELY_FOR_MIJNPAKKET_USERS, $storeId);

        $this->setDoLoginCheck($doLoginCheck);
        return $doLoginCheck;
    }

    /**
     * Check if the button should be disabled.
     *
     * @return boolean
     */
    public function isDisabled()
    {
        if (!$this->canUsePostnlCheckout()) {
            return true;
        }

        return false;
    }

    /**
     * Check if the button should be displayed.
     *
     * @return boolean
     */
    public function canUsePostnlCheckout()
    {
        /** @var Mage_Checkout_Model_Session $session */
        $session = Mage::getSingleton('checkout/session');
        $quote = $session->getQuote();

        /** @var TIG_PostNL_Helper_Checkout $helper */
        $helper = Mage::helper('postnl/checkout');
        $canUseCheckout = $helper->canUsePostnlCheckout($quote);

        /**
         * If Checkout is not available, log the reason why for debugging purposes
         */
        if (!$canUseCheckout && Mage::registry('postnl_checkout_logged') === null) {
            $configErrors = Mage::registry('postnl_checkout_is_configured_errors');
            if (is_null($configErrors)) {
                $configErrors = Mage::registry('postnl_checkout_is_enabled_errors');
            }

            if (is_null($configErrors)) {
                return $canUseCheckout;
            }

            $errorMessage = $helper->__('PostNL Checkout is not available due to the following reasons:');
            foreach ($configErrors as $error) {
                $errorMessage .= PHP_EOL . $error['message'];
            }

            Mage::register('postnl_checkout_logged', true);
            $helper->log($errorMessage);
        }

        return $canUseCheckout;

    }

    /**
     * Gets this webshop's public ID.
     *
     * @return string
     */
    public function getPublicWebshopId()
    {
        if ($this->hasPublicWebshopId()) {
            return $this->getData('public_webshop_id');
        }

        $webshopId = Mage::getStoreConfig(self::XPATH_PUBLIC_WEBSHOP_ID, Mage::app()->getStore()->getId());

        $this->setPublicWebshopId($webshopId);
        return $webshopId;
    }

    /**
     * Gets the checkout button src attribute.
     *
     * @param boolean $forceDisabled
     *
     * @return string
     */
    public function getSrc($forceDisabled = false)
    {
        /** @var TIG_PostNL_Helper_Checkout $helper */
        $helper = Mage::helper('postnl/checkout');
        if ($helper->isTestMode()) {
            $baseUrl = $this->getButtonTestBaseUrl();
        } else {
            $baseUrl = $this->getButtonLiveBaseUrl();;
        }

        $webshopId = $this->getPublicWebshopId();

        $url =  $baseUrl
             . '?publicId=' . $webshopId
             . '&format=Large'
             . '&type=Orange';

        if ($forceDisabled === true || $this->isDisabled()) {
            $url .= '&disabled=true';
        }

        return $url;
    }

    /**
     * Gets the URL of a CMS page containing instructions on how to use PostNL Checkout.
     *
     * @return boolean|string
     */
    public function getInstructionUrl()
    {
        $instructionPage = Mage::getStoreConfig(self::XPATH_INSTRUCTION_CMS_PAGE, Mage::app()->getStore()->getId());
        if (!$instructionPage) {
            return false;
        }

        /** @var Mage_Cms_Helper_Page $helper */
        $helper = Mage::helper('cms/page');
        $pageUrl = $helper->getPageUrl($instructionPage);
        return $pageUrl;
    }

    /**
     * Returns the block's html. Checks if the 'use_postnl_checkout' param is set. Otherwise returns an empty string.
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var TIG_PostNL_Helper_Checkout $helper */
        $helper = Mage::helper('postnl/checkout');
        if (!$helper->isCheckoutActive() && Mage::registry('postnl_checkout_logged') === null) {
            /**
             * If Checkout is not available, log the reason why for debugging purposes
             */
            $configErrors = Mage::registry('postnl_checkout_is_enabled_errors');

            if (is_null($configErrors)) {
                return '';
            }

            $errorMessage = $helper->__('PostNL Checkout is not available due to the following reasons:');
            foreach ($configErrors as $error) {
                $errorMessage .= PHP_EOL . $error['message'];
            }

            Mage::register('postnl_checkout_logged', true);
            $helper->log($errorMessage);

            /**
             * Do not render the checkout button
             */
            return '';
        }

        return parent::_toHtml();
    }
}
