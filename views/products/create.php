<?php
/**
 * Create Product View
 * Form to add new product
 */

$currency = $settings['currency'] ?? 'TND ';
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Ajouter un nouveau produit</h3>
        <a href="index.php?page=products" class="btn btn-outline">Retour</a>
    </div>
    <div class="card-body">
        <form method="POST" action="index.php?page=products&action=create" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="barcode">Code-barres *</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" id="barcode" name="barcode" class="form-control" required>
                        <button type="button" class="btn btn-outline" onclick="generateBarcode()">
                            <i class="fas fa-barcode"></i> Générer
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="name">Nom du produit *</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="category_id">Catégorie *</label>
                    <select id="category_id" name="category_id" class="form-control" required>
                        <option value="">Choisir une catégorie</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="supplier_id">Fournisseur</label>
                    <select id="supplier_id" name="supplier_id" class="form-control">
                        <option value="">Choisir un fournisseur</option>
                        <?php foreach ($suppliers as $supplier): ?>
                            <option value="<?php echo $supplier['id']; ?>">
                                <?php echo htmlspecialchars($supplier['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="buying_price">Prix d'achat (<?php echo $currency; ?>) *</label>
                    <input type="number" id="buying_price" name="buying_price" class="form-control" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label for="selling_price">Prix de vente (<?php echo $currency; ?>) *</label>
                    <input type="number" id="selling_price" name="selling_price" class="form-control" step="0.01" min="0" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="cigarette_price">Prix par cigarette (<?php echo $currency; ?>)</label>
                    <input type="number" id="cigarette_price" name="cigarette_price" class="form-control" step="0.001" min="0" placeholder="Laisser vide si non applicable">
                </div>
                <div class="form-group">
                    <label for="cigarettes_per_pack">Cigarettes par paquet</label>
                    <input type="number" id="cigarettes_per_pack" name="cigarettes_per_pack" class="form-control" min="1" placeholder="Par ex. 20">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="quantity">Quantité initiale *</label>
                    <input type="number" id="quantity" name="quantity" class="form-control" step="0.001" min="0" required>
                </div>
                <div class="form-group">
                    <label for="minimum_stock">Alerte stock minimum *</label>
                    <input type="number" id="minimum_stock" name="minimum_stock" class="form-control" min="0" value="5" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="image">Image du produit</label>
                <input type="file" id="image" name="image" class="form-control" accept="image/*">
                <small>Formats autorisés : JPG, PNG, GIF (Max 2 Mo)</small>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="3"></textarea>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer le produit
                </button>
                <a href="index.php?page=products" class="btn btn-outline">Annuler</a>
            </div>
        </form>
    </div>
</div>

<script>
function generateBarcode() {
    document.getElementById('barcode').value = Math.floor(1000000000 + Math.random() * 9000000000);
}
</script>
