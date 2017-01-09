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
 */
class TIG_PostNL_Model_Core_System_Config_Source_StaticBlocks
{
    /**
     * @var array
     */
    protected $_options;

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function setOptions($options)
    {
        $this->_options = $options;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasOptions()
    {
        $options = $this->_options;
        if (is_array($options) && count($options) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Returns an option array for all static blocks.
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->hasOptions()) {
            return $this->getOptions();
        }

        /**
         * Get the basic empty option.
         */
        $options = array(
            array(
                'label' => Mage::helper('postnl')->__('-- None --'),
                'value' => '',
            ),
        );

        /**
         * Get all static blocks.
         */
        $blocksCollection = Mage::getResourceModel('cms/block_collection');

        /**
         * Add filters based on the current config scope.
         *
         * This prevents the merchant from accidentally selecting a static block that is not available in the current
         * scope.
         */
        $request = Mage::app()->getRequest();
        if ($request->getParam('store')) {
            $store = Mage::app()->getStore($request->getParam('store'));

            $blocksCollection->addStoreFilter($store);
        } elseif ($request->getParam('website')) {
            /** @var Mage_Core_Model_Website $website */
            $website = Mage::getModel('core/website')->load($request->getParam('website'));
            $stores = $website->getStoreIds();

            $blocksCollection->addStoreFilter($stores);
        }

        /**
         * Convert the collection to an option array where the block's ID is the value and the block's title is the
         * label.
         */
        $blocksOptions = $blocksCollection->toOptionArray();

        /**
         * Merge the empty option with the static blocks.
         */
        $options = array_merge($options, $blocksOptions);

        $this->setOptions($options);
        return $options;
    }
}
