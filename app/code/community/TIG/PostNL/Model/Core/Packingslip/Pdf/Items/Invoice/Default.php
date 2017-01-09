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
 * @method array                                                       getItemColumns()
 * @method TIG_PostNL_Model_Core_Packingslip_Pdf_Items_Invoice_Default setItemColumns(array $value)
 */
class TIG_PostNL_Model_Core_Packingslip_Pdf_Items_Invoice_Default extends Mage_Sales_Model_Order_Pdf_Items_Abstract
{
    /**
     * Draw item line.
     */
    public function draw()
    {
        /**
         * @var Mage_Sales_Model_Order_Invoice_Item $item
         */
        $item    = $this->getItem();
        $pdf     = $this->getPdf();
        $page    = $this->getPage();
        $columns = $this->getItemColumns();

        $lines = array(
            array()
        );

        $i = 0;
        $feed = 20;
        $previousFeed = 0;
        $nameFeed = 30;
        foreach ($columns as $column) {
            if ($i > 1) {
                $align = 'right';
            } else {
                $align = 'left';
            }

            if($i == 1){
                $previousFeed -= 20;
            }else if ($i == 2){
                $previousFeed += 20;
            }

            $feed += $previousFeed;
            $previousFeed = $column['width'];

            /**
             * We need the feed of the name column later to add custom options.
             */
            if ($column['field'] == 'name') {
                $nameFeed = $feed;
            }

            $value = $this->_getValue($item, $column['field']);

            $lines[0][] = array(
                'text'      => $value,
                'feed'      => $feed,
                'align'     => $align,
                'font_size' => 8,
            );

            $i++;
        }

        // Custom options
        $options = $this->getItemOptions();
        if ($options) {
            /** @var Mage_Core_Helper_String $stringHelper */
            $stringHelper = Mage::helper('core/string');
            foreach ($options as $option) {
                $optionText = strip_tags($option['label']);

                if ($option['value']) {
                    $printValue = isset($option['print_value'])
                        ? $option['print_value']
                        : strip_tags($option['value']);
                    $value = str_replace(', ', ' - ', $printValue);
                    $optionText .= ' - ' . $value;
                }

                // draw options
                $lines[][] = array(
                    'text'      => $stringHelper->str_split(strip_tags($optionText), 120, true, true),
                    'font'      => 'italic',
                    'feed'      => $nameFeed,
                    'font_size' => 7,
                    'shift'     => -8,
                );
            }
        }

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 20
        );

        $page = $pdf->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $this->setPage($page);
    }

    /**
     * Gets the formatted value for a specified field.
     *
     * @param Mage_Sales_Model_Order_Invoice_Item $item
     * @param string                              $field
     *
     * @return string
     */
    protected function _getValue(Mage_Sales_Model_Order_Invoice_Item $item, $field)
    {
        switch ($field) {
            case 'name':
                /** @var Mage_Core_Helper_String $stringHelper */
                $stringHelper = Mage::helper('core/string');
                $value = $stringHelper->str_split($item->getName(), 60, true, true);
                break;
            case 'sku':
                /** @var Mage_Core_Helper_String $stringHelper */
                $stringHelper = Mage::helper('core/string');
                $value = $stringHelper->str_split($this->getSku($item), 20);
                break;
            case 'price':
                $value = $this->getOrder()->formatPriceTxt($item->getPrice());
                break;
            case 'qty':
                $value = $item->getQty() * 1;
                break;
            case 'tax':
                $value = $this->getOrder()->formatPriceTxt($item->getTaxAmount());
                break;
            case 'subtotal':
                $value = $this->getOrder()->formatPriceTxt($item->getRowTotalInclTax());
                break;
            default:
                $value = '';
                break;
        }

        return $value;
    }
}
