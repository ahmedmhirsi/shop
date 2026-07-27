<?php
/**
 * Delete Category View
 * Confirmation to delete category
 */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Supprimer la catégorie</h3>
        <a href="index.php?page=categories" class="btn btn-outline">Retour</a>
    </div>
    <div class="card-body">
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            Êtes-vous sûr de vouloir supprimer cette catégorie ? Cette action est irréversible. Tous les produits de cette catégorie seront également affectés.
        </div>
        
        <div style="margin-bottom: 20px;">
            <p><strong>Nom :</strong> <?php echo htmlspecialchars($category['name']); ?></p>
            <p><strong>Description :</strong> <?php echo htmlspecialchars($category['description'] ?? '-'); ?></p>
            <p><strong>Produits :</strong> <?php echo $category['product_count']; ?></p>
        </div>
        
        <form method="POST" action="index.php?page=categories&action=delete&id=<?php echo $category['id']; ?>">
            <?php echo csrf_field(); ?>
            
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Supprimer la catégorie
            </button>
            <a href="index.php?page=categories" class="btn btn-outline">Annuler</a>
        </form>
    </div>
</div>
