<?php
/**
 * POS Invoice View
 * Printable invoice for completed sale
 */

$currency = $settings['currency'] ?? 'TND ';
?>

<div class="invoice" id="printableInvoice">
    <div class="invoice-header">
        <div class="invoice-logo">
            <?php if ($settings['logo']): ?>
                <img src="uploads/<?php echo htmlspecialchars($settings['logo']); ?>" alt="" style="width: 100%; height: 100%; object-fit: contain;">
            <?php else: ?>
                <i class="fas fa-store" style="font-size: 48px; color: var(--primary-color);"></i>
            <?php endif; ?>
        </div>
        <div class="invoice-info">
            <h2><?php echo htmlspecialchars($settings['store_name'] ?? 'My Store'); ?></h2>
            <p><?php echo htmlspecialchars($settings['address'] ?? ''); ?></p>
            <p>Téléphone : <?php echo htmlspecialchars($settings['phone'] ?? ''); ?></p>
            <p>Email : <?php echo htmlspecialchars($settings['email'] ?? ''); ?></p>
        </div>
        <div style="text-align: right;">
            <h2>FACTURE</h2>
            <p><strong>Facture # :</strong> <?php echo htmlspecialchars($sale['invoice_number']); ?></p>
            <p><strong>Date :</strong> <?php echo format_date($sale['created_at'], 'M d, Y H:i'); ?></p>
            <p><strong>Caissier :</strong> <?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
        </div>
    </div>
    
    <div class="invoice-details">
        <div class="invoice-details-left">
            <h3>Facturé à :</h3>
            <p><?php echo htmlspecialchars($sale['customer_name'] ?? 'Client anonyme'); ?></p>
        </div>
        <div class="invoice-details-right">
            <h3>Détails du paiement :</h3>
            <p><strong>Méthode :</strong> <?php echo ucfirst($sale['payment_method']); ?></p>
            <p><strong>Montant reçu :</strong> <?php echo format_currency($sale['amount_received'], $currency); ?></p>
            <p><strong>Rendu :</strong> <?php echo format_currency($sale['change'], $currency); ?></p>
    </div>
    
    <div class="invoice-table">
        <table style="width: 100%;">
            <thead>
                <tr>
                    <th style="text-align: left;">Article</th>
                    <th style="text-align: center;">Qté</th>
                    <th style="text-align: right;">Prix</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sale_items as $item): ?>
                    <tr>
                            <td><?php echo htmlspecialchars($item['product_name']); ?><?php if (!empty($item['unit_type']) && $item['unit_type'] === 'cigarette') { echo ' (Cigarette)'; } elseif (!empty($item['unit_type']) && $item['unit_type'] === 'pack') { echo ' (Paquet)'; } ?></td>
                            <td style="text-align: center;">
                                <?php echo $item['quantity']; ?> <?php if (!empty($item['unit_type']) && $item['unit_type'] === 'cigarette') { echo 'cig.'; } elseif (!empty($item['unit_type']) && $item['unit_type'] === 'pack') { echo 'pkg'; } ?>
                            </td>
                            <td style="text-align: right;"><?php echo format_currency($item['selling_price'], $currency); ?></td>
                            <td style="text-align: right;"><?php echo format_currency($item['subtotal'], $currency); ?></td>
                        </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="invoice-totals">
        <div class="row">
            <span>Sous-total</span>
            <span><?php echo format_currency($sale['subtotal'], $currency); ?></span>
        </div>
        <?php if ($sale['discount'] > 0): ?>
            <div class="row">
                <span>Remise</span>
                <span>-<?php echo format_currency($sale['discount'], $currency); ?></span>
            </div>
        <?php endif; ?>
        <?php if ($sale['tax'] > 0): ?>
            <div class="row">
                <span>Taxe (<?php echo $settings['tax_percentage'] ?? 0; ?>%)</span>
                <span><?php echo format_currency($sale['tax'], $currency); ?></span>
            </div>
        <?php endif; ?>
        <div class="row total">
            <span>Total</span>
            <span><?php echo format_currency($sale['total'], $currency); ?></span>
        </div>
    </div>
    
    <?php if ($sale['notes']): ?>
        <div style="margin-top: 20px; padding: 15px; background: var(--light-bg); border-radius: var(--radius);">
            <strong>Notes :</strong>
            <p><?php echo nl2br(htmlspecialchars($sale['notes'])); ?></p>
        </div>
    <?php endif; ?>
    
    <div class="invoice-footer">
        <p>Merci pour votre confiance !</p>
        <p><?php echo htmlspecialchars($settings['store_name'] ?? 'My Store'); ?></p>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #printableInvoice, #printableInvoice * {
        visibility: visible;
    }
    #printableInvoice {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        margin: 0;
        padding: 20px;
    }
    .modal-header, .modal-footer {
        display: none !important;
    }
}
</style>
