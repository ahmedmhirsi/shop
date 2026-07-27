<?php
/**
 * Edit Supplier View
 * Form to edit existing supplier
 */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Modifier le fournisseur</h3>
        <a href="index.php?page=suppliers" class="btn btn-outline">Retour</a>
    </div>
    <div class="card-body">
        <form method="POST" action="index.php?page=suppliers&action=edit&id=<?php echo $supplier['id']; ?>">
            <?php echo csrf_field(); ?>
            
            <div class="form-group">
                <label for="name">Nom du fournisseur *</label>
                <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($supplier['name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="contact_person">Contact</label>
                <input type="text" id="contact_person" name="contact_person" class="form-control" value="<?php echo htmlspecialchars($supplier['contact_person'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="phone">Téléphone</label>
                <input type="text" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($supplier['phone'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($supplier['email'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="address">Adresse</label>
                <textarea id="address" name="address" class="form-control" rows="3"><?php echo htmlspecialchars($supplier['address'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Mettre à jour le fournisseur
                </button>
                <a href="index.php?page=suppliers" class="btn btn-outline">Annuler</a>
            </div>
        </form>
    </div>
</div>
