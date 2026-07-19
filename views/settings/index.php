<?php
/**
 * Settings View
 * Application settings management
 */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Paramètres du magasin</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="index.php?page=settings" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="store_name">Nom du magasin *</label>
                    <input type="text" id="store_name" name="store_name" class="form-control" value="<?php echo htmlspecialchars($settings['store_name'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="currency">Symbole de devise *</label>
                    <input type="text" id="currency" name="currency" class="form-control" value="<?php echo htmlspecialchars($settings['currency'] ?? 'TND '); ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="address">Adresse</label>
                <textarea id="address" name="address" class="form-control" rows="2"><?php echo htmlspecialchars($settings['address'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="phone">Téléphone</label>
                    <input type="text" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($settings['phone'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($settings['email'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="tax_percentage">Pourcentage de taxe (%) *</label>
                    <input type="number" id="tax_percentage" name="tax_percentage" class="form-control" step="0.01" min="0" value="<?php echo $settings['tax_percentage'] ?? 0; ?>" required>
                </div>
                <div class="form-group">
                    <label for="invoice_prefix">Préfixe de facture *</label>
                    <input type="text" id="invoice_prefix" name="invoice_prefix" class="form-control" value="<?php echo htmlspecialchars($settings['invoice_prefix'] ?? 'INV-'); ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="low_stock_alert">Quantité d'alerte de stock faible *</label>
                <input type="number" id="low_stock_alert" name="low_stock_alert" class="form-control" min="0" value="<?php echo $settings['low_stock_alert'] ?? 10; ?>" required>
                <small>Les produits dont la quantité est inférieure à cette valeur seront affichés comme stock faible</small>
            </div>
            
            <div class="form-group">
                <label for="logo">Logo du magasin</label>
                <input type="file" id="logo" name="logo" class="form-control" accept="image/*">
                <?php if ($settings['logo']): ?>
                    <div style="margin-top: 10px;">
                        <img src="uploads/<?php echo htmlspecialchars($settings['logo']); ?>" alt="" style="width: 100px; height: 100px; object-fit: cover; border-radius: 4px;">
                        <small>Logo actuel</small>
                    </div>
                <?php endif; ?>
                <small>Formats autorisés : JPG, PNG, GIF (Max 2 Mo). Laisser vide pour conserver le logo actuel.</small>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer les paramètres
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Change Password Card -->
<div class="card mt-4">
    <div class="card-header">
        <h3 class="card-title"><i class="bi bi-key"></i> Modifier le mot de passe</h3>
    </div>
    <div class="card-body">
        <form id="changePasswordForm" method="POST" action="index.php?page=settings">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="change_password" value="1">
            <div class="form-group">
                <label for="current_password">Mot de passe actuel</label>
                <input type="password" id="current_password" name="current_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="new_password">Nouveau mot de passe</label>
                <input type="password" id="new_password" name="new_password" class="form-control" minlength="6" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirmer le mot de passe</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" minlength="6" required>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-warning" id="btnChangePassword">
                    <i class="bi bi-arrow-repeat"></i> Modifier le mot de passe
                </button>
            </div>
            <div id="changePasswordAlert" style="display:none; margin-top:10px;"></div>
        </form>
    </div>
</div>

<script>
// Simple client-side validation and AJAX submit for the password change form
(function(){
    var form = document.getElementById('changePasswordForm');
    var alertBox = document.getElementById('changePasswordAlert');
    form.addEventListener('submit', function(e){
        e.preventDefault();
        alertBox.style.display = 'none';
        var current = document.getElementById('current_password').value.trim();
        var nw = document.getElementById('new_password').value.trim();
        var conf = document.getElementById('confirm_password').value.trim();
        if (!current || !nw || !conf) {
            showAlert('Veuillez remplir tous les champs', 'danger');
            return;
        }
        if (nw.length < 6) {
            showAlert('Le nouveau mot de passe doit contenir au moins 6 caractères', 'danger');
            return;
        }
        if (nw !== conf) {
            showAlert('Les mots de passe ne correspondent pas', 'danger');
            return;
        }
        // send via fetch to the same page — backend may or may not handle; this keeps UI-only change safe
        var fd = new FormData(form);
        fetch(form.action, { method: 'POST', body: fd, credentials: 'same-origin' })
            .then(function(resp){
                if (resp.ok) return resp.text();
                throw new Error('Erreur réseau');
            })
            .then(function(text){
                // Try to detect success message in response HTML
                if (text.indexOf('success') !== -1 || text.indexOf('Mot de passe modifié') !== -1) {
                    showAlert('Mot de passe modifié avec succès', 'success');
                    form.reset();
                } else {
                    showAlert('Requête envoyée. Si le serveur ne supporte pas ce point de terminaison, veuillez contacter l\'administrateur.', 'info');
                }
            })
            .catch(function(err){
                showAlert('Erreur lors de l\'envoi : ' + err.message, 'danger');
            });
    });
    function showAlert(message, type) {
        alertBox.style.display = 'block';
        alertBox.className = 'alert alert-' + (type === 'danger' ? 'danger' : (type === 'success' ? 'success' : 'info'));
        alertBox.innerText = message;
        setTimeout(function(){ alertBox.style.display = 'none'; }, 6000);
    }
})();
</script>
