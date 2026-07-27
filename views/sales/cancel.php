<?php
/**
 * Cancel Sale View
 * Confirmation to cancel a sale
 */

$currency = $settings['currency'] ?? 'TND ';
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Annuler la vente</h3>
        <a href="index.php?page=sales" class="btn btn-outline">Retour</a>
    </div>
    <div class="card-body">
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            Êtes-vous sûr de vouloir annuler cette vente ? Cette action est irréversible. Le stock des produits sera rétabli.
        </div>
        
        <div style="margin-bottom: 20px;">
            <p><strong>Facture :</strong> <?php echo htmlspecialchars($sale['invoice_number']); ?></p>
            <p><strong>Date :</strong> <?php echo format_date($sale['created_at'], 'M d, Y H:i'); ?></p>
            <p><strong>Total :</strong> <?php echo format_currency($sale['total'], $currency); ?></p>
        </div>
        
        <form method="POST" action="index.php?page=sales&action=cancel&id=<?php echo $sale['id']; ?>">
            <?php echo csrf_field(); ?>
            
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-times"></i> Annuler la vente
            </button>
            <a href="index.php?page=sales" class="btn btn-outline">Retour</a>
        </form>
    </div>
</div>
