<?php
/**
 * Overwrites Mage_CatalogRule_Model_Resource_Rule_Product_Price
 * Lines that are change are indicated in the comment by
 * "THIS LINE WAS CHANGED".
 */


/**
 * Catalog Rule Product Aggregated Price per date Resource Model
 *
 * @category    Mage
 * @package     Mage_CatalogRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Customweb_PriceRule_Model_Resource_Rule_Product_Price extends Mage_CatalogRule_Model_Resource_Rule_Product_Price
{
    /**
     * Apply price rule price to price index table
     *
     * @param Varien_Db_Select $select
     * @param array|string $indexTable
     * @param string $entityId
     * @param string $customerGroupId
     * @param string $websiteId
     * @param array $updateFields       the array of fields for compare with rule price and update
     * @param string $websiteDate
     * @return Mage_CatalogRule_Model_Resource_Rule_Product_Price
     */
    public function applyPriceRuleToIndexTable(Varien_Db_Select $select, $indexTable, $entityId, $customerGroupId, 
        $websiteId, $updateFields, $websiteDate)
    {
        if (empty($updateFields)) {
            return $this;
        }

        if (is_array($indexTable)) {
            foreach ($indexTable as $k => $v) {
                if (is_string($k)) {
                    $indexAlias = $k;
                } else {
                    $indexAlias = $v;
                }
                break;
            }
        } else {
            $indexAlias = $indexTable;
        }

        $select->join(array('rp' => $this->getMainTable()), "rp.rule_date = {$websiteDate}", array())
               ->where("rp.product_id = {$entityId} AND rp.website_id = {$websiteId} AND rp.customer_group_id = {$customerGroupId}");

        foreach ($updateFields as $priceField) {
            $priceCond = $this->_getWriteAdapter()->quoteIdentifier(array($indexAlias, $priceField));
            // THIS LINE WAS CHANGED
            $priceExpr = $this->_getWriteAdapter()->getCheckSql("rp.rule_price != {$priceCond}", 'rp.rule_price', $priceCond);
            $select->columns(array($priceField => $priceExpr));
        }

        $query = $select->crossUpdateFromSelect($indexTable);
        $this->_getWriteAdapter()->query($query);

        return $this;
    }
}
