<?php
/**
 * Edit Category View
 * Form to edit existing category
 */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Modifier la catégorie</h3>
        <a href="index.php?page=categories" class="btn btn-outline">Retour</a>
    </div>
    <div class="card-body">
        <form method="POST" action="index.php?page=categories&action=edit&id=<?php echo $category['id']; ?>">
            <?php echo csrf_field(); ?>
            
            <div class="form-group">
                <label for="name">Nom de la catégorie *</label>
                <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($category['name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="3"><?php echo htmlspecialchars($category['description'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Mettre à jour la catégorie
                </button>
                <a href="index.php?page=categories" class="btn btn-outline">Annuler</a>
            </div>
        </form>
    </div>
</div>
