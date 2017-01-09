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
 * @deprecated this class is no longer used as of v1.7.0.
 */
class TIG_PostNL_Model_Admin_Logging_Handler_Postnl extends Enterprise_Logging_Model_Handler_Controllers
{
    /**
     * PostNL mass action postDispatch handler.
     *
     * @param Varien_Simplexml_Element       $config
     * @param Enterprise_Logging_Model_Event $eventModel
     *
     * @return boolean
     */
    public function postDispatchSaveMassAction(/** @noinspection PhpUnusedParameterInspection */
        $config, $eventModel)
    {
        $request = Mage::app()->getRequest();
        if ($request->getParam('shipment_ids')) {
            /** @noinspection PhpUndefinedMethodInspection */
            $eventModel->setInfo(
                Mage::helper('enterprise_logging')->implodeValues($request->getParam('shipment_ids'))
            );

            return true;
        }

        if ($request->getParam('order_ids')) {
            /** @noinspection PhpUndefinedMethodInspection */
            $eventModel->setInfo(
                Mage::helper('enterprise_logging')->implodeValues($request->getParam('order_ids'))
            );

            return true;
        }

        return true;
    }

    /**
     * PostNL mass action postDispatch handler.
     *
     * @param Varien_Simplexml_Element       $config
     * @param Enterprise_Logging_Model_Event $eventModel
     *
     * @return boolean
     */
    public function postDispatchAction(/** @noinspection PhpUnusedParameterInspection */
        $config, $eventModel)
    {
        return true;
    }
}
