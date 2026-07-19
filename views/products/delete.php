<?php
/**
 * Delete Product View
 * Confirmation to delete product
 */

$currency = $settings['currency'] ?? 'TND ';
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Supprimer le produit</h3>
        <a href="index.php?page=products" class="btn btn-outline">Retour</a>
    </div>
    <div class="card-body">
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            Êtes-vous sûr de vouloir supprimer ce produit ? Cette action ne peut pas être annulée.
        </div>
        
        <div style="margin-bottom: 20px;">
            <p><strong>Code-barres :</strong> <?php echo htmlspecialchars($product['barcode']); ?></p>
            <p><strong>Nom :</strong> <?php echo htmlspecialchars($product['name']); ?></p>
            <p><strong>Catégorie :</strong> <?php echo htmlspecialchars($product['category_name'] ?? '-'); ?></p>
            <p><strong>Prix d'achat :</strong> <?php echo format_currency($product['buying_price'], $currency); ?></p>
            <p><strong>Prix de vente :</strong> <?php echo format_currency($product['selling_price'], $currency); ?></p>
            <p><strong>Quantité :</strong> <?php echo $product['quantity']; ?></p>
        </div>
        
        <form method="POST" action="index.php?page=products&action=delete&id=<?php echo $product['id']; ?>">
            <?php echo csrf_field(); ?>
            
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Supprimer le produit
            </button>
            <a href="index.php?page=products" class="btn btn-outline">Annuler</a>
        </form>
    </div>
</div>
