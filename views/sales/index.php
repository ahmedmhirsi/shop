<?php
/**
 * Sales Index View
 * Lists all sales with filters
 */

$currency = $settings['currency'] ?? 'TND ';
?>

<!-- Filters -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Filtres</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="index.php">
            <input type="hidden" name="page" value="sales">
            <div class="form-row">
                <div class="form-group">
                    <label>Numéro de facture</label>
                    <input type="text" name="invoice_number" class="form-control" placeholder="Rechercher facture..." value="<?php echo htmlspecialchars($filters['invoice_number'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Date de début</label>
                    <input type="date" name="date_from" class="form-control" value="<?php echo htmlspecialchars($filters['date_from'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Date de fin</label>
                    <input type="date" name="date_to" class="form-control" value="<?php echo htmlspecialchars($filters['date_to'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Statut</label>
                    <select name="status" class="form-control">
                        <option value="completed" <?php echo ($filters['status'] ?? 'completed') == 'completed' ? 'selected' : ''; ?>>Terminée</option>
                        <option value="cancelled" <?php echo ($filters['status'] ?? '') == 'cancelled' ? 'selected' : ''; ?>>Annulée</option>
                    </select>
                </div>
                <div class="form-group" style="display: flex; align-items: flex-end;">
                    <button type="submit" class="btn btn-primary">Filtrer</button>
                    <a href="index.php?page=sales" class="btn btn-outline" style="margin-left: 10px;">Effacer</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Sales Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Historique des ventes</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Facture</th>
                        <th>Date</th>
                        <th>Caissier</th>
                        <th>Client</th>
                        <th>Paiement</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sales)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center;">Aucune vente trouvée</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($sales as $sale): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($sale['invoice_number']); ?></td>
                                <td><?php echo format_date($sale['created_at'], 'M d, Y H:i'); ?></td>
                                <td><?php echo htmlspecialchars($sale['cashier_name']); ?></td>
                                <td><?php echo htmlspecialchars($sale['customer_name'] ?? 'Client anonyme'); ?></td>
                                <td><?php echo ucfirst($sale['payment_method']); ?></td>
                                <td><?php echo format_currency($sale['total'], $currency); ?></td>
                                <td>
                                    <?php if ($sale['status'] == 'completed'): ?>
                                        <span class="badge badge-success">Terminée</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Annulée</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="index.php?page=sales&action=view&id=<?php echo $sale['id']; ?>" class="btn btn-sm btn-outline">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($sale['status'] == 'completed'): ?>
                                        <a href="index.php?page=sales&action=cancel&id=<?php echo $sale['id']; ?>" class="btn btn-sm btn-danger">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php echo pagination_links($pagination, 'index.php?page=sales' . (!empty($filters) ? '&' . http_build_query($filters) : '')); ?>
    </div>
</div>
