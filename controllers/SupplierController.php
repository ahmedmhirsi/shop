<?php
/**
 * Supplier Controller
 * Handles supplier management
 */

require_once __DIR__ . '/../models/Supplier.php';

class SupplierController {
    private $supplierModel;
    
    public function __construct() {
        $this->supplierModel = new Supplier();
    }
    
    public function index() {
        $suppliers = $this->supplierModel->getSupplierWithProductCount();
        
        $page_title = 'Fournisseurs';
        $content = __DIR__ . '/../views/suppliers/index.php';
        include __DIR__ . '/../views/layout.php';
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf_token = $_POST['csrf_token'] ?? '';
            
            if (!validate_csrf($csrf_token)) {
                redirect_with_flash('index.php?page=suppliers', 'error', 'Invalid request');
            }
            
            $data = [
                'name' => sanitize($_POST['name']),
                'contact_person' => sanitize($_POST['contact_person'] ?? ''),
                'phone' => sanitize($_POST['phone'] ?? ''),
                'email' => sanitize($_POST['email'] ?? ''),
                'address' => sanitize($_POST['address'] ?? ''),
                'status' => 'active'
            ];
            
            if ($this->supplierModel->nameExists($data['name'])) {
                redirect_with_flash('index.php?page=suppliers&action=create', 'error', 'Supplier name already exists');
            }
            
            if ($this->supplierModel->create($data)) {
                redirect_with_flash('index.php?page=suppliers', 'success', 'Supplier added successfully');
            } else {
                redirect_with_flash('index.php?page=suppliers&action=create', 'error', 'Failed to add supplier');
            }
        }
        
        $page_title = 'Ajouter un fournisseur';
        $content = __DIR__ . '/../views/suppliers/create.php';
        include __DIR__ . '/../views/layout.php';
    }
    
    public function edit() {
        $id = (int)$_GET['id'] ?? 0;
        $supplier = $this->supplierModel->getById($id);
        
        if (!$supplier) {
            redirect_with_flash('index.php?page=suppliers', 'error', 'Supplier not found');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf_token = $_POST['csrf_token'] ?? '';
            
            if (!validate_csrf($csrf_token)) {
                redirect_with_flash('index.php?page=suppliers', 'error', 'Invalid request');
            }
            
            $data = [
                'name' => sanitize($_POST['name']),
                'contact_person' => sanitize($_POST['contact_person'] ?? ''),
                'phone' => sanitize($_POST['phone'] ?? ''),
                'email' => sanitize($_POST['email'] ?? ''),
                'address' => sanitize($_POST['address'] ?? '')
            ];
            
            if ($this->supplierModel->nameExists($data['name'], $id)) {
                redirect_with_flash('index.php?page=suppliers&action=edit&id=' . $id, 'error', 'Supplier name already exists');
            }
            
            if ($this->supplierModel->update($id, $data)) {
                redirect_with_flash('index.php?page=suppliers', 'success', 'Supplier updated successfully');
            } else {
                redirect_with_flash('index.php?page=suppliers&action=edit&id=' . $id, 'error', 'Failed to update supplier');
            }
        }
        
        $page_title = 'Modifier le fournisseur';
        $content = __DIR__ . '/../views/suppliers/edit.php';
        include __DIR__ . '/../views/layout.php';
    }
    
    public function delete() {
        $id = (int)$_GET['id'] ?? 0;
        $supplier = $this->supplierModel->getById($id);
        
        if (!$supplier) {
            redirect_with_flash('index.php?page=suppliers', 'error', 'Supplier not found');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf_token = $_POST['csrf_token'] ?? '';
            
            if (!validate_csrf($csrf_token)) {
                redirect_with_flash('index.php?page=suppliers', 'error', 'Invalid request');
            }
            
            if ($this->supplierModel->delete($id)) {
                redirect_with_flash('index.php?page=suppliers', 'success', 'Supplier deleted successfully');
            } else {
                redirect_with_flash('index.php?page=suppliers', 'error', 'Failed to delete supplier');
            }
        }
        
        $page_title = 'Supprimer le fournisseur';
        $content = __DIR__ . '/../views/suppliers/delete.php';
        include __DIR__ . '/../views/layout.php';
    }
}
