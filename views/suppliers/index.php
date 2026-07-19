<?php
/**
 * Suppliers Index View
 * Lists all suppliers
 */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Fournisseurs</h3>
        <a href="index.php?page=suppliers&action=create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Ajouter un fournisseur
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Contact</th>
                        <th>Téléphone</th>
                        <th>Email</th>
                        <th>Produits</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($suppliers)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center;">Aucun fournisseur trouvé</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($suppliers as $supplier): ?>
                            <tr>
                                <td><?php echo $supplier['id']; ?></td>
                                <td><?php echo htmlspecialchars($supplier['name']); ?></td>
                                <td><?php echo htmlspecialchars($supplier['contact_person'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($supplier['phone'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($supplier['email'] ?? '-'); ?></td>
                                <td><?php echo $supplier['product_count']; ?></td>
                                <td>
                                    <?php if ($supplier['status'] == 'active'): ?>
                                        <span class="badge badge-success">Actif</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Inactif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="index.php?page=suppliers&action=edit&id=<?php echo $supplier['id']; ?>" class="btn btn-sm btn-outline">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="index.php?page=suppliers&action=delete&id=<?php echo $supplier['id']; ?>" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
