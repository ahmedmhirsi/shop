<?php
/**
 * Create Category View
 * Form to add new category
 */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Ajouter une nouvelle catégorie</h3>
        <a href="index.php?page=categories" class="btn btn-outline">Retour</a>
    </div>
    <div class="card-body">
        <form method="POST" action="index.php?page=categories&action=create">
            <?php echo csrf_field(); ?>
            
            <div class="form-group">
                <label for="name">Nom de la catégorie *</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="3"></textarea>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer la catégorie
                </button>
                <a href="index.php?page=categories" class="btn btn-outline">Annuler</a>
            </div>
        </form>
    </div>
</div>
