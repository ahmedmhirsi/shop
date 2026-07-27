<?php $page_title = isset($isEdit) && $isEdit ? 'Edit Product' : 'Add Product'; ob_start(); ?>

<div class="card" style="max-width: 600px;">
    <div class="card-header">
        <h3 class="card-title"><?php echo $page_title; ?></h3>
    </div>
    <div class="card-body">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo SecurityHelper::escapeHtml($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <div class="form-group">
                <label class="form-label">Product Name *</label>
                <input type="text" name="name" class="form-control" value="<?php echo SecurityHelper::escapeHtml($product['name'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Barcode</label>
                <div class="barcode-generator-widget">
                    <div class="barcode-input-wrapper">
                        <input type="text" name="barcode" id="barcodeInput" class="form-control" value="<?php echo SecurityHelper::escapeHtml($product['barcode'] ?? ''); ?>" placeholder="Entrez ou générez un code EAN-12">
                        <button type="button" class="btn btn-sm btn-primary" id="generateBarcodeBtn" title="Générer un code-barres automatique">
                            <i class="fas fa-sync-alt"></i> Générer
                        </button>
                    </div>
                    <small class="form-text text-muted">Format EAN-12 (12 chiffres)</small>
                    
                    <!-- Barcode Preview -->
                    <div class="barcode-preview-container" id="barcodePreviewContainer" style="display: none; margin-top: 15px;">
                        <div class="barcode-preview-header">
                            <h6>Aperçu du Code-barres</h6>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="downloadBarcodeBtn" title="Télécharger">
                                <i class="fas fa-download"></i> Télécharger
                            </button>
                        </div>
                        <div class="barcode-preview-display">
                            <svg id="barcodeCanvas" style="max-width: 100%; height: auto;"></svg>
                        </div>
                        <p class="barcode-preview-info">Code généré: <strong id="barcodeValueDisplay">---</strong></p>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-control">
                        <option value="">-- Select --</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo (isset($product['category_id']) && $product['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo SecurityHelper::escapeHtml($cat['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Supplier</label>
                    <select name="supplier_id" class="form-control">
                        <option value="">-- Select --</option>
                        <?php foreach ($suppliers as $sup): ?>
                        <option value="<?php echo $sup['id']; ?>" <?php echo (isset($product['supplier_id']) && $product['supplier_id'] == $sup['id']) ? 'selected' : ''; ?>>
                            <?php echo SecurityHelper::escapeHtml($sup['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Buying Price</label>
                    <input type="number" name="buying_price" class="form-control" step="0.01" value="<?php echo $product['buying_price'] ?? '0'; ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Selling Price *</label>
                    <input type="number" name="selling_price" class="form-control" step="0.01" value="<?php echo $product['selling_price'] ?? '0'; ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Cigarette Price (per unit)</label>
                    <input type="number" name="cigarette_price" class="form-control" step="0.001" value="<?php echo $product['cigarette_price'] ?? '0'; ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Cigarettes per Pack</label>
                    <input type="number" name="cigarettes_per_pack" class="form-control" value="<?php echo $product['cigarettes_per_pack'] ?? '20'; ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="quantity" class="form-control" step="0.001" value="<?php echo $product['quantity'] ?? '0'; ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Minimum Stock Level</label>
                    <input type="number" name="minimum_stock" class="form-control" value="<?php echo $product['minimum_stock'] ?? '5'; ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control"><?php echo SecurityHelper::escapeHtml($product['description'] ?? ''); ?></textarea>
            </div>

            <?php if (isset($isEdit) && $isEdit): ?>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="active" <?php echo ($product['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo ($product['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            <?php endif; ?>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="/shop_v2/index.php?url=products" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<!-- Styles for Barcode Generator -->
<style>
.barcode-generator-widget {
    margin-top: 5px;
}

.barcode-input-wrapper {
    display: flex;
    gap: 8px;
    margin-bottom: 8px;
}

.barcode-input-wrapper input {
    flex: 1;
}

.barcode-input-wrapper .btn {
    min-width: 110px;
}

.barcode-preview-container {
    border: 1px solid #E2E8F0;
    border-radius: 6px;
    padding: 15px;
    background-color: #F8FAFC;
}

.barcode-preview-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    padding-bottom: 12px;
    border-bottom: 1px solid #E2E8F0;
}

.barcode-preview-header h6 {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
    color: #0F172A;
}

.barcode-preview-display {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
    background: white;
    border: 1px solid #CBD5E1;
    border-radius: 4px;
    min-height: 80px;
}

.barcode-preview-display svg {
    max-width: 100%;
    height: auto;
}

.barcode-preview-info {
    margin-top: 12px;
    margin-bottom: 0;
    font-size: 13px;
    color: #475569;
    text-align: center;
}

.barcode-preview-info strong {
    color: #0F172A;
    font-weight: 600;
}

.btn-outline-secondary {
    border: 1px solid #CBD5E1;
    color: #475569;
    background: white;
    padding: 6px 12px;
    font-size: 12px;
    border-radius: 4px;
    cursor: pointer;
    transition: all 200ms;
}

.btn-outline-secondary:hover {
    border-color: #94A3B8;
    color: #334155;
    background: #F1F5F9;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
    border: 1px solid #CBD5E1;
    border-radius: 4px;
    cursor: pointer;
    transition: all 200ms;
}

.btn-primary {
    background: #4F46E5;
    color: white;
    border: none;
}

.btn-primary:hover {
    background: #4338CA;
}

.form-text {
    display: block;
    margin-top: 4px;
    font-size: 12px;
    color: #64748B;
}

.text-muted {
    color: #94A3B8 !important;
}
</style>

<!-- JsBarcode Library -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

<!-- Barcode Generator Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const barcodeInput = document.getElementById('barcodeInput');
    const generateBtn = document.getElementById('generateBarcodeBtn');
    const barcodeCanvas = document.getElementById('barcodeCanvas');
    const barcodeValueDisplay = document.getElementById('barcodeValueDisplay');
    const previewContainer = document.getElementById('barcodePreviewContainer');
    const downloadBtn = document.getElementById('downloadBarcodeBtn');

    // Generate random EAN-12 barcode
    function generateRandomEAN12() {
        // Generate 11 random digits
        let code = '';
        for (let i = 0; i < 11; i++) {
            code += Math.floor(Math.random() * 10);
        }
        
        // Calculate checksum for EAN-12
        let sum = 0;
        for (let i = 0; i < 11; i++) {
            sum += parseInt(code[i]) * (i % 2 === 0 ? 1 : 3);
        }
        let checksum = (10 - (sum % 10)) % 10;
        return code + checksum;
    }

    // Generate barcode when button is clicked
    if (generateBtn) {
        generateBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const ean12 = generateRandomEAN12();
            barcodeInput.value = ean12;
            displayBarcode(ean12);
        });
    }

    // Display barcode on input change
    if (barcodeInput) {
        barcodeInput.addEventListener('change', function() {
            if (this.value.length === 12 && /^\d{12}$/.test(this.value)) {
                displayBarcode(this.value);
            }
        });

        // If barcode already has a value on page load, display it
        if (barcodeInput.value && barcodeInput.value.length === 12) {
            setTimeout(() => {
                displayBarcode(barcodeInput.value);
            }, 500);
        }
    }

    // Display barcode
    function displayBarcode(code) {
        if (!/^\d{12}$/.test(code)) {
            alert('Le code-barres doit contenir exactement 12 chiffres (format EAN-12)');
            return;
        }

        try {
            JsBarcode('#barcodeCanvas', code, {
                format: 'EAN13',
                width: 2,
                height: 100,
                displayValue: true,
                fontSize: 14,
                margin: 10
            });
            
            barcodeValueDisplay.textContent = code;
            previewContainer.style.display = 'block';
        } catch (error) {
            console.error('Erreur lors de la génération du code-barres:', error);
            alert('Erreur lors de la génération du code-barres');
        }
    }

    // Download barcode as SVG
    if (downloadBtn) {
        downloadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const code = barcodeInput.value;
            if (!code || !/^\d{12}$/.test(code)) {
                alert('Veuillez d\'abord générer un code-barres valide');
                return;
            }

            const svg = document.getElementById('barcodeCanvas').cloneNode(true);
            const serializer = new XMLSerializer();
            const svgString = serializer.serializeToString(svg);
            const blob = new Blob([svgString], { type: 'image/svg+xml' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `barcode_${code}.svg`;
            link.click();
            URL.revokeObjectURL(url);
        });
    }
});
</script>

<?php $content = ob_get_clean(); include APP_DIR . '/Views/layouts/main.php'; ?>
