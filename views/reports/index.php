<?php
/**
 * Reports Index View
 * Main reports page with report type selection
 */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Sélectionner le type de rapport</h3>
    </div>
    <div class="card-body">
        <div class="stats-grid">
            <a href="index.php?page=reports&action=daily" class="stat-card" style="text-decoration: none; color: inherit;">
                <div class="stat-icon primary">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-info">
                    <h3>Journalier</h3>
                    <p>Rapport de ventes du jour</p>
                </div>
            </a>
            
            <a href="index.php?page=reports&action=weekly" class="stat-card" style="text-decoration: none; color: inherit;">
                <div class="stat-icon success">
                    <i class="fas fa-calendar-week"></i>
                </div>
                <div class="stat-info">
                    <h3>Hebdomadaire</h3>
                    <p>Rapport de ventes de la semaine</p>
                </div>
            </a>
            
            <a href="index.php?page=reports&action=monthly" class="stat-card" style="text-decoration: none; color: inherit;">
                <div class="stat-icon warning">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-info">
                    <h3>Mensuel</h3>
                    <p>Rapport de ventes du mois</p>
                </div>
            </a>
            
            <a href="index.php?page=reports&action=yearly" class="stat-card" style="text-decoration: none; color: inherit;">
                <div class="stat-icon info">
                    <i class="fas fa-calendar"></i>
                </div>
                <div class="stat-info">
                    <h3>Annuel</h3>
                    <p>Rapport de ventes de l'année</p>
                </div>
            </a>
            
            <a href="index.php?page=reports&action=custom" class="stat-card" style="text-decoration: none; color: inherit;">
                <div class="stat-icon danger">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <h3>Personnalisé</h3>
                    <p>Rapport sur une période personnalisée</p>
                </div>
            </a>
        </div>
    </div>
</div>
