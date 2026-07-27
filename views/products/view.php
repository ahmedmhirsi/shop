<?php
/**
 * View Product Details
 * Shows product information and stock history
 */

$currency = $settings['currency'] ?? 'TND ';
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Détails du produit</h3>
        <a href="index.php?page=products" class="btn btn-outline">Retour</a>
    </div>
    <div class="card-body">
        <div class="form-row">
            <div>
                <?php if ($product['image']): ?>
                    <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="" style="width: 200px; height: 200px; object-fit: cover; border-radius: 8px;">
                <?php else: ?>
                    <div style="width: 200px; height: 200px; background: #e2e8f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-image" style="font-size: 48px; color: #94a3b8;"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div style="flex: 1;">
                <h2 style="margin-bottom: 20px;"><?php echo htmlspecialchars($product['name']); ?></h2>
                
                <div class="form-row">
                    <div>
                        <p><strong>Code-barres :</strong> <?php echo htmlspecialchars($product['barcode']); ?></p>
                        <p><strong>Catégorie :</strong> <?php echo htmlspecialchars($product['category_name'] ?? '-'); ?></p>
                        <p><strong>Fournisseur :</strong> <?php echo htmlspecialchars($product['supplier_name'] ?? '-'); ?></p>
                    </div>
                    <div>
                        <p><strong>Prix d'achat :</strong> <?php echo format_currency($product['buying_price'], $currency); ?></p>
                        <p><strong>Prix de vente :</strong> <?php echo format_currency($product['selling_price'], $currency); ?></p>
                        <p><strong>Bénéfice par unité :</strong> <?php echo format_currency($product['selling_price'] - $product['buying_price'], $currency); ?></p>
                    </div>
                </div>
                
                <div style="margin-top: 20px;">
                    <?php if ($product['quantity'] == 0): ?>
                        <span class="badge badge-danger" style="font-size: 14px; padding: 8px 16px;">❌ En rupture de stock</span>
                    <?php elseif ($product['quantity'] <= $product['minimum_stock']): ?>
                        <span class="badge badge-warning" style="font-size: 14px; padding: 8px 16px;">⚠ Stock faible (<?php echo $product['quantity']; ?>)</span>
                    <?php else: ?>
                        <span class="badge badge-success" style="font-size: 14px; padding: 8px 16px;">En stock (<?php echo $product['quantity']; ?>)</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <?php if ($product['description']): ?>
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-color);">
                <h4>Description</h4>
                <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-color);">
            <a href="index.php?page=products&action=edit&id=<?php echo $product['id']; ?>" class="btn btn-primary">
                <i class="fas fa-edit"></i> Modifier le produit
            </a>
        </div>
    </div>
</div>

<!-- Stock History -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Historique des mouvements de stock</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Quantité</th>
                        <th>Utilisateur</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($stock_history)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">Aucun historique de stock pour le moment</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($stock_history as $history): ?>
                            <tr>
                                <td><?php echo format_date($history['created_at'], 'M d, Y H:i'); ?></td>
                                <td>
                                    <?php if ($history['type'] == 'in'): ?>
                                        <span class="badge badge-success">ENTRÉE</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">SORTIE</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $history['quantity']; ?></td>
                                <td><?php echo htmlspecialchars($history['user_name']); ?></td>
                                <td><?php echo htmlspecialchars($history['notes'] ?? '-'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
