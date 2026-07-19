<?php
/**
 * Products Index View
 * Lists all products with filters
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
            <input type="hidden" name="page" value="products">
            <div class="form-row">
                <div class="form-group">
                    <label>Recherche</label>
                    <input type="text" name="search" class="form-control" placeholder="Code-barres ou nom..." value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Catégorie</label>
                    <select name="category_id" class="form-control">
                        <option value="">Toutes les catégories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo ($filters['category_id'] ?? '') == $category['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Fournisseur</label>
                    <select name="supplier_id" class="form-control">
                        <option value="">Tous les fournisseurs</option>
                        <?php foreach ($suppliers as $supplier): ?>
                            <option value="<?php echo $supplier['id']; ?>" <?php echo ($filters['supplier_id'] ?? '') == $supplier['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($supplier['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Statut</label>
                    <select name="status" class="form-control">
                        <option value="active" <?php echo ($filters['status'] ?? 'active') == 'active' ? 'selected' : ''; ?>>Actif</option>
                        <option value="inactive" <?php echo ($filters['status'] ?? '') == 'inactive' ? 'selected' : ''; ?>>Inactif</option>
                    </select>
                </div>
                <div class="form-group" style="display: flex; align-items: flex-end;">
                    <button type="submit" class="btn btn-primary">Filtrer</button>
                    <a href="index.php?page=products" class="btn btn-outline" style="margin-left: 10px;">Effacer</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Products Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Produits</h3>
        <a href="index.php?page=products&action=create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Ajouter un produit
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Code-barres</th>
                        <th>Nom</th>
                        <th>Catégorie</th>
                        <th>Prix d'achat</th>
                        <th>Prix de vente</th>
                        <th>Stock</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center;">Aucun produit trouvé</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td>
                                    <?php if ($product['image']): ?>
                                        <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                    <?php else: ?>
                                        <div style="width: 50px; height: 50px; background: #e2e8f0; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-image" style="color: #94a3b8;"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($product['barcode']); ?></td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars($product['category_name'] ?? '-'); ?></td>
                                <td><?php echo format_currency($product['buying_price'], $currency); ?></td>
                                <td><?php echo format_currency($product['selling_price'], $currency); ?></td>
                                <td>
                                    <?php if ($product['quantity'] == 0): ?>
                                        <span class="badge badge-danger"><?php echo $product['quantity']; ?> ❌</span>
                                    <?php elseif ($product['quantity'] <= $product['minimum_stock']): ?>
                                        <span class="badge badge-warning"><?php echo $product['quantity']; ?> ⚠</span>
                                    <?php else: ?>
                                        <span class="badge badge-success"><?php echo $product['quantity']; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($product['status'] == 'active'): ?>
                                        <span class="badge badge-success">Actif</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Inactif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="index.php?page=products&action=view&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="index.php?page=products&action=edit&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="index.php?page=products&action=delete&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (!empty($pagination)): ?>
            <?php echo pagination_links($pagination, 'index.php?page=products' . (!empty($filters) ? '&' . http_build_query($filters) : '')); ?>
        <?php endif; ?>
    </div>
</div>
