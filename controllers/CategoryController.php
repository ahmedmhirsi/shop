<?php
/**
 * Category Controller
 * Handles category management
 */

require_once __DIR__ . '/../models/Category.php';

class CategoryController {
    private $categoryModel;
    
    public function __construct() {
        $this->categoryModel = new Category();
    }
    
    public function index() {
        $categories = $this->categoryModel->getCategoryWithProductCount();
        
        $page_title = 'Catégories';
        $content = __DIR__ . '/../views/categories/index.php';
        include __DIR__ . '/../views/layout.php';
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf_token = $_POST['csrf_token'] ?? '';
            
            if (!validate_csrf($csrf_token)) {
                redirect_with_flash('index.php?page=categories', 'error', 'Requête invalide');
            }
            
            $data = [
                'name' => sanitize($_POST['name']),
                'description' => sanitize($_POST['description'] ?? ''),
                'status' => 'active'
            ];
            
            if ($this->categoryModel->nameExists($data['name'])) {
                redirect_with_flash('index.php?page=categories&action=create', 'error', 'Le nom de la catégorie existe déjà');
            }
            
            if ($this->categoryModel->create($data)) {
                redirect_with_flash('index.php?page=categories', 'success', 'Catégorie ajoutée avec succès');
            } else {
                redirect_with_flash('index.php?page=categories&action=create', 'error', 'Échec de l\'ajout de la catégorie');
            }
        }
        
        $page_title = 'Ajouter une catégorie';
        $content = __DIR__ . '/../views/categories/create.php';
        include __DIR__ . '/../views/layout.php';
    }
    
    public function edit() {
        $id = (int)$_GET['id'] ?? 0;
        $category = $this->categoryModel->getById($id);
        
        if (!$category) {
            redirect_with_flash('index.php?page=categories', 'error', 'Catégorie introuvable');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf_token = $_POST['csrf_token'] ?? '';
            
            if (!validate_csrf($csrf_token)) {
                redirect_with_flash('index.php?page=categories', 'error', 'Invalid request');
            }
            
            $data = [
                'name' => sanitize($_POST['name']),
                'description' => sanitize($_POST['description'] ?? '')
            ];
            
            if ($this->categoryModel->nameExists($data['name'], $id)) {
                redirect_with_flash('index.php?page=categories&action=edit&id=' . $id, 'error', 'Le nom de la catégorie existe déjà');
            }
            
            if ($this->categoryModel->update($id, $data)) {
                redirect_with_flash('index.php?page=categories', 'success', 'Catégorie mise à jour avec succès');
            } else {
                redirect_with_flash('index.php?page=categories&action=edit&id=' . $id, 'error', 'Échec de la mise à jour de la catégorie');
            }
        }
        
        $page_title = 'Modifier la catégorie';
        $content = __DIR__ . '/../views/categories/edit.php';
        include __DIR__ . '/../views/layout.php';
    }
    
    public function delete() {
        $id = (int)$_GET['id'] ?? 0;
        $category = $this->categoryModel->getById($id);
        
        if (!$category) {
            redirect_with_flash('index.php?page=categories', 'error', 'Catégorie introuvable');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf_token = $_POST['csrf_token'] ?? '';
            
            if (!validate_csrf($csrf_token)) {
                redirect_with_flash('index.php?page=categories', 'error', 'Invalid request');
            }
            
            if ($this->categoryModel->delete($id)) {
                redirect_with_flash('index.php?page=categories', 'success', 'Catégorie supprimée avec succès');
            } else {
                redirect_with_flash('index.php?page=categories', 'error', 'Échec de la suppression de la catégorie');
            }
        }
        
        $page_title = 'Supprimer la catégorie';
        $content = __DIR__ . '/../views/categories/delete.php';
        include __DIR__ . '/../views/layout.php';
    }
}
