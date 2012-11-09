<?php
/**
 * Overwrites Mage_Catalog_Model_Product_Type_Price
 * Lines that are change are indicated in the comment by
 * "THIS LINE WAS CHANGED".
 */

/**
 * Product type price model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Customweb_PriceRule_Model_Product_Type_Price extends Mage_Catalog_Model_Product_Type_Price
{
    /**
     * Calculate product price based on special price data and price rules
     *
     * @param   float $basePrice
     * @param   float $specialPrice
     * @param   string $specialPriceFrom
     * @param   string $specialPriceTo
     * @param   float|null|false $rulePrice
     * @param   mixed $wId
     * @param   mixed $gId
     * @param   null|int $productId
     * @return  float
     */
    public static function calculatePrice($basePrice, $specialPrice, $specialPriceFrom, $specialPriceTo, $rulePrice = false, $wId = null, $gId = null, $productId = null)
    {
        Varien_Profiler::start('__PRODUCT_CALCULATE_PRICE__');
        if ($wId instanceof Mage_Core_Model_Store) {
            $sId = $wId->getId();
            $wId = $wId->getWebsiteId();
        } else {
            $sId = Mage::app()->getWebsite($wId)->getDefaultGroup()->getDefaultStoreId();
        }

        $finalPrice = $basePrice;
        if ($gId instanceof Mage_Customer_Model_Group) {
            $gId = $gId->getId();
        }

        $finalPrice = self::calculateSpecialPrice($finalPrice, $specialPrice, $specialPriceFrom, $specialPriceTo, $sId);

        if ($rulePrice === false) {
            $storeTimestamp = Mage::app()->getLocale()->storeTimeStamp($sId);
            $rulePrice = Mage::getResourceModel('catalogrule/rule')
                ->getRulePrice($storeTimestamp, $wId, $gId, $productId);
        }

        if ($rulePrice !== null && $rulePrice !== false) {
			// THIS LINE WAS CHANGED
            $finalPrice = $rulePrice;
        }

        $finalPrice = max($finalPrice, 0);
        Varien_Profiler::stop('__PRODUCT_CALCULATE_PRICE__');
        return $finalPrice;
    }
}
