<?php
/**
 * Delete Supplier View
 * Confirmation to delete supplier
 */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Supprimer le fournisseur</h3>
        <a href="index.php?page=suppliers" class="btn btn-outline">Retour</a>
    </div>
    <div class="card-body">
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            Êtes-vous sûr de vouloir supprimer ce fournisseur ? Cette action ne peut pas être annulée.
        </div>
        
        <div style="margin-bottom: 20px;">
            <p><strong>Nom :</strong> <?php echo htmlspecialchars($supplier['name']); ?></p>
            <p><strong>Contact :</strong> <?php echo htmlspecialchars($supplier['contact_person'] ?? '-'); ?></p>
            <p><strong>Téléphone :</strong> <?php echo htmlspecialchars($supplier['phone'] ?? '-'); ?></p>
            <p><strong>Email :</strong> <?php echo htmlspecialchars($supplier['email'] ?? '-'); ?></p>
            <p><strong>Produits :</strong> <?php echo $supplier['product_count']; ?></p>
        </div>
        
        <form method="POST" action="index.php?page=suppliers&action=delete&id=<?php echo $supplier['id']; ?>">
            <?php echo csrf_field(); ?>
            
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Supprimer le fournisseur
            </button>
            <a href="index.php?page=suppliers" class="btn btn-outline">Annuler</a>
        </form>
    </div>
</div>
