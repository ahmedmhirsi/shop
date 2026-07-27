<?php
/**
 * Sale Details View
 * Shows complete sale information
 */

$currency = $settings['currency'] ?? 'TND ';
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Détails de la vente - <?php echo htmlspecialchars($sale['invoice_number']); ?></h3>
        <a href="index.php?page=sales" class="btn btn-outline">Retour</a>
    </div>
    <div class="card-body">
        <div class="form-row" style="margin-bottom: 30px;">
            <div>
                <h4>Informations de la facture</h4>
                <p><strong>Numéro de facture :</strong> <?php echo htmlspecialchars($sale['invoice_number']); ?></p>
                <p><strong>Date :</strong> <?php echo format_date($sale['created_at'], 'M d, Y H:i'); ?></p>
                <p><strong>Caissier :</strong> <?php echo htmlspecialchars($sale['cashier_name']); ?></p>
                <p><strong>Client :</strong> <?php echo htmlspecialchars($sale['customer_name'] ?? 'Client anonyme'); ?></p>
                <p><strong>Méthode de paiement :</strong> <?php echo ucfirst($sale['payment_method']); ?></p>
                <p><strong>Statut :</strong> 
                    <?php if ($sale['status'] == 'completed'): ?>
                        <span class="badge badge-success">Terminée</span>
                    <?php else: ?>
                        <span class="badge badge-danger">Annulée</span>
                    <?php endif; ?>
                </p>
            </div>
            <div>
                <h4>Détails du paiement</h4>
                <p><strong>Sous-total :</strong> <?php echo format_currency($sale['subtotal'], $currency); ?></p>
                <?php if ($sale['discount'] > 0): ?>
                    <p><strong>Remise :</strong> -<?php echo format_currency($sale['discount'], $currency); ?></p>
                <?php endif; ?>
                <?php if ($sale['tax'] > 0): ?>
                    <p><strong>Taxes :</strong> <?php echo format_currency($sale['tax'], $currency); ?></p>
                <?php endif; ?>
                <p><strong>Total :</strong> <?php echo format_currency($sale['total'], $currency); ?></p>
                <p><strong>Montant reçu :</strong> <?php echo format_currency($sale['amount_received'], $currency); ?></p>
                <p><strong>Rendu :</strong> <?php echo format_currency($sale['change'], $currency); ?></p>
            </div>
        </div>
        
        <?php if ($sale['notes']): ?>
            <div style="margin-bottom: 20px; padding: 15px; background: var(--light-bg); border-radius: var(--radius);">
                <strong>Notes:</strong>
                <p><?php echo nl2br(htmlspecialchars($sale['notes'])); ?></p>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Articles de la vente</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Code-barres</th>
                                <th>Catégorie</th>
                                <th>Quantité</th>
                                <th>Prix d'achat</th>
                                <th>Prix de vente</th>
                                <th>Sous-total</th>
                                <th>Bénéfice</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sale_items as $item): ?>
                                <tr>
                                    <td>
                                        <?php echo htmlspecialchars($item['product_name']); ?>
                                        <?php if (!empty($item['unit_type']) && $item['unit_type'] === 'cigarette'): ?>
                                            <span style="font-size: 0.85em; color: #666;">(Cigarette)</span>
                                        <?php elseif (!empty($item['unit_type']) && $item['unit_type'] === 'pack'): ?>
                                            <span style="font-size: 0.85em; color: #666;">(Paquet)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($item['barcode']); ?></td>
                                    <td><?php echo htmlspecialchars($item['category_name'] ?? '-'); ?></td>
                                    <td>
                                        <?php echo $item['quantity']; ?>
                                        <?php if (!empty($item['unit_type']) && $item['unit_type'] === 'cigarette'): ?>
                                            cig.
                                        <?php elseif (!empty($item['unit_type']) && $item['unit_type'] === 'pack'): ?>
                                            pkg
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo format_currency($item['buying_price'], $currency); ?></td>
                                    <td><?php echo format_currency($item['selling_price'], $currency); ?></td>
                                    <td><?php echo format_currency($item['subtotal'], $currency); ?></td>
                                    <td><?php echo format_currency($item['profit'], $currency); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6" style="text-align: right; font-weight: bold;">Total</td>
                                <td><?php echo format_currency($sale['subtotal'], $currency); ?></td>
                                <td><?php echo format_currency(array_sum(array_column($sale_items, 'profit')), $currency); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 20px;">
            <?php if ($sale['status'] == 'completed'): ?>
                <a href="index.php?page=sales&action=cancel&id=<?php echo $sale['id']; ?>" class="btn btn-danger">
                    <i class="fas fa-times"></i> Annuler la vente
                </a>
            <?php endif; ?>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>
</div>
