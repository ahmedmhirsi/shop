<?php
/**
 * Stock History View
 * Shows stock movement history
 */

$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Historique des mouvements de stock</h3>
    </div>
    <div class="card-body">
        <!-- Date Filter Form -->
        <form method="GET" action="index.php" class="form-row" style="margin-bottom: 20px;">
            <input type="hidden" name="page" value="stock">
            <div class="form-group">
                <label>From Date</label>
                <input type="date" name="date_from" class="form-control" value="<?php echo htmlspecialchars($date_from); ?>">
            </div>
            <div class="form-group">
                <label>To Date</label>
                <input type="date" name="date_to" class="form-control" value="<?php echo htmlspecialchars($date_to); ?>">
            </div>
            <div class="form-group" style="display: flex; align-items: flex-end;">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="index.php?page=stock" class="btn btn-outline" style="margin-left: 10px;">Clear</a>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Product</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>User</th>
                        <th>Reference</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($movements)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">Aucun mouvement de stock trouvé</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($movements as $movement): ?>
                            <tr>
                                <td><?php echo format_date($movement['created_at'], 'M d, Y H:i'); ?></td>
                                <td><?php echo htmlspecialchars($movement['product_name']); ?></td>
                                <td>
                                    <?php if ($movement['type'] == 'in'): ?>
                                        <span class="badge badge-success">ENTRÉE</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">SORTIE</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $movement['quantity']; ?></td>
                                <td><?php echo htmlspecialchars($movement['user_name']); ?></td>
                                <td><?php echo htmlspecialchars($movement['reference_type'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($movement['notes'] ?? '-'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
