<?php
/**
 * Settings Model
 * Handles application settings
 */

require_once __DIR__ . '/Model.php';

class Settings extends Model {
    protected $table = 'settings';
    protected $primaryKey = 'id';
    
    /**
     * Get settings
     * @return array
     */
    public function getSettings() {
        $sql = "SELECT * FROM {$this->table} LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Update settings
     * @param array $data
     * @return bool
     */
    public function updateSettings($data) {
        $settings = $this->getSettings();
        if ($settings) {
            return $this->update($settings['id'], $data);
        }
        return $this->create($data);
    }
    
    /**
     * Get store name
     * @return string
     */
    public function getStoreName() {
        $settings = $this->getSettings();
        return $settings['store_name'] ?? 'My Store';
    }
    
    /**
     * Get currency
     * @return string
     */
    public function getCurrency() {
        $settings = $this->getSettings();
        $currency = $settings['currency'] ?? 'TND ';
        return $currency === '$' ? 'TND ' : $currency;
    }
    
    /**
     * Get tax percentage
     * @return float
     */
    public function getTaxPercentage() {
        $settings = $this->getSettings();
        return (float) ($settings['tax_percentage'] ?? 0);
    }
    
    /**
     * Get invoice prefix
     * @return string
     */
    public function getInvoicePrefix() {
        $settings = $this->getSettings();
        return $settings['invoice_prefix'] ?? 'INV-';
    }
    
    /**
     * Get low stock alert
     * @return int
     */
    public function getLowStockAlert() {
        $settings = $this->getSettings();
        return (int) ($settings['low_stock_alert'] ?? 10);
    }
}
