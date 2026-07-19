# TODO - Performance par Shift (Dashboard)

- [ ] Ajouter dans `models/Sale.php` une méthode SQL agrégée pour calculer les stats par shift (CA, ventes, bénéfice, ticket moyen) en gérant le shift 22:00-07:00.
- [ ] Mettre à jour `controllers/DashboardController.php` pour injecter `shift_stats` vers `views/dashboard.php` sans casser le `$stats` existant.
- [ ] Mettre à jour `views/dashboard.php` pour afficher une nouvelle section “Performance par Shift” avec 3 cartes (Shift 1/2/3) en gardant le design CSS existant.
- [ ] Vérifier les garde-fous PHP (division par 0, champs manquants) et valider que `format_currency()` est utilisée.

