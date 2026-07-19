<?php
/**
 * Settings Controller
 * Handles application settings management
 */

require_once __DIR__ . '/../models/Settings.php';

class SettingsController {
    private $settingsModel;
    
    public function __construct() {
        $this->settingsModel = new Settings();
    }
    
    public function index() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf_token = $_POST['csrf_token'] ?? '';
            
            if (!validate_csrf($csrf_token)) {
                redirect_with_flash('index.php?page=settings', 'error', 'Invalid request');
            }
            
            $data = [
                'store_name' => sanitize($_POST['store_name']),
                'address' => sanitize($_POST['address'] ?? ''),
                'phone' => sanitize($_POST['phone'] ?? ''),
                'email' => sanitize($_POST['email'] ?? ''),
                'currency' => sanitize($_POST['currency']),
                'tax_percentage' => (float)$_POST['tax_percentage'],
                'invoice_prefix' => sanitize($_POST['invoice_prefix']),
                'low_stock_alert' => (int)$_POST['low_stock_alert']
            ];
            
            // Handle logo upload
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $filename = upload_file($_FILES['logo'], UPLOAD_PATH);
                if ($filename) {
                    $data['logo'] = $filename;
                }
            }
            
            if ($this->settingsModel->updateSettings($data)) {
                redirect_with_flash('index.php?page=settings', 'success', 'Settings updated successfully');
            } else {
                redirect_with_flash('index.php?page=settings', 'error', 'Failed to update settings');
            }
        }
        
        $settings = $this->settingsModel->getSettings();
        
        $page_title = 'Paramètres';
        $content = __DIR__ . '/../views/settings/index.php';
        include __DIR__ . '/../views/layout.php';
    }
}
