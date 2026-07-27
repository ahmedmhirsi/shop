<?php
/**
 * Custom Report View
 */

$date_from = $_GET['date_from'] ?? date('Y-m-01');
$date_to = $_GET['date_to'] ?? date('Y-m-d');
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Rapport personnalisé</h3>
        <div>
            <a href="index.php?page=reports" class="btn btn-outline">Retour</a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Imprimer
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Date Filter Form -->
        <form method="GET" action="index.php" class="form-row" style="margin-bottom: 20px;">
            <input type="hidden" name="page" value="reports">
            <input type="hidden" name="action" value="custom">
            <div class="form-group">
                <label>Date de début</label>
                <input type="date" name="date_from" class="form-control" value="<?php echo $date_from; ?>">
            </div>
            <div class="form-group">
                <label>Date de fin</label>
                <input type="date" name="date_to" class="form-control" value="<?php echo $date_to; ?>">
            </div>
            <div class="form-group" style="display: flex; align-items: flex-end;">
                <button type="submit" class="btn btn-primary">Générer le rapport</button>
            </div>
        </form>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo format_currency($report['total_revenue'], $currency); ?></h3>
                    <p>Revenu total</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo format_currency($report['total_profit'], $currency); ?></h3>
                    <p>Bénéfice total</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon warning">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $report['total_products']; ?></h3>
                    <p>Produits vendus</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon info">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $report['total_transactions']; ?></h3>
                    <p>Transactions</p>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Détails des ventes (<?php echo $report['date_range']; ?>)</h3>
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
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($report['sales'])): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center;">Aucune vente pour cette période</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($report['sales'] as $sale): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($sale['invoice_number']); ?></td>
                                        <td><?php echo format_date($sale['created_at'], 'M d, Y H:i'); ?></td>
                                        <td><?php echo htmlspecialchars($sale['cashier_name']); ?></td>
                                        <td><?php echo htmlspecialchars($sale['customer_name'] ?? 'Client anonyme'); ?></td>
                                        <td><?php echo ucfirst($sale['payment_method']); ?></td>
                                        <td><?php echo format_currency($sale['total'], $currency); ?></td>
                                    </tr>
    <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
