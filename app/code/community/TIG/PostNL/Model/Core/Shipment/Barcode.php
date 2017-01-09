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
 * Class TIG_PostNL_Model_Core_Shipment_Barcode
 *
 * @method int    getBarcodeId()
 * @method int    getParentId()
 * @method string getBarcodeType()
 * @method int    getBarcodeNumber()
 * @method string getBarcode()
 *
 * @method TIG_PostNL_Model_Core_Shipment_Barcode setBarcodeId(int $value)
 * @method TIG_PostNL_Model_Core_Shipment_Barcode setParentId(int $value)
 * @method TIG_PostNL_Model_Core_Shipment_Barcode setBarcodeType(string $value)
 * @method TIG_PostNL_Model_Core_Shipment_Barcode setBarcodeNumber(int $value)
 * @method TIG_PostNL_Model_Core_Shipment_Barcode setBarcode(string $value)
 */
class TIG_PostNL_Model_Core_Shipment_Barcode extends Mage_Core_Model_Abstract
{
    const BARCODE_TYPE_SHIPMENT = 'shipment';
    const BARCODE_TYPE_RETURN   = 'return';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'postnl_shipment_barcode';

    public function _construct()
    {
        $this->_init('postnl_core/shipment_barcode');
    }

    /**
     * Load a barcode object based on a postnl shipment Id and a barcode number
     *
     * @param int            $parentId
     * @param int            $barcodeNumber
     * @param string|boolean $type
     *
     * @return $this
     */
    public function loadByParentAndBarcodeNumber($parentId, $barcodeNumber, $type = false)
    {
        if (!$type) {
            $type = self::BARCODE_TYPE_SHIPMENT;
        }

        /**
         * @var TIG_PostNL_Model_Core_Resource_Shipment_Barcode_Collection $collection
         */
        $collection = $this->getCollection();
        $collection->addFieldToSelect('*')
                   ->addFieldToFilter('parent_id', array('eq' => $parentId))
                   ->addFieldToFilter('barcode_type', array('eq' => $type))
                   ->addFieldToFilter('barcode_number', array('eq' => $barcodeNumber));

        $collection->getSelect()
                   ->limit(1);

        if ($collection->getSize()) {
            $barcode = $collection->getFirstItem();

            $this->setData($barcode->getData());
            $this->setOrigData();
            $this->_afterLoad();
        }

        return $this;
    }
}
