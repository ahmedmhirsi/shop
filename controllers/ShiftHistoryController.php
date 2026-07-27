<?php
/**
 * Shift History Controller
 * Historique du chiffre d'affaires par shift (07h-16h / 16h-22h / 22h-07h)
 */

require_once __DIR__ . '/../models/Sale.php';
require_once __DIR__ . '/../models/Settings.php';

class ShiftHistoryController {
    private $saleModel;
    private $settingsModel;

    public function __construct() {
        $this->saleModel = new Sale();
        $this->settingsModel = new Settings();
    }

    public function index() {
        $filter = $_GET['filter'] ?? 'today';

        [$date_from, $date_to] = $this->resolveDateRange($filter);

        $shift_history = $this->saleModel->getShiftHistory($date_from, $date_to);

        // Statistiques globales de la période
        $total_revenue = 0.0;
        $total_sales = 0;
        $total_profit = 0.0;

        // Totaux par shift pour le graphique de comparaison
        $chart_totals = [
            1 => 0.0,
            2 => 0.0,
            3 => 0.0,
        ];

        foreach ($shift_history as $row) {
            $total_revenue += $row['revenue'];
            $total_sales += $row['sales_count'];
            $total_profit += $row['profit'];

            $sid = $row['shift_id'];
            if (isset($chart_totals[$sid])) {
                $chart_totals[$sid] += $row['revenue'];
            }
        }

        $total_average = $total_sales > 0 ? ($total_revenue / $total_sales) : 0.0;

        $settings = $this->settingsModel->getSettings();

        $page_title = 'Historique des Shifts';
        $content = __DIR__ . '/../views/shift_history/index.php';
        include __DIR__ . '/../views/layout.php';
    }

    /**
     * Résout le filtre en date_from / date_to (format Y-m-d)
     * @param string $filter
     * @return array [date_from, date_to]
     */
    private function resolveDateRange($filter) {
        switch ($filter) {
            case 'yesterday':
                $d = date('Y-m-d', strtotime('-1 day'));
                return [$d, $d];

            case 'this_week':
                $date_from = date('Y-m-d', strtotime('monday this week'));
                $date_to = date('Y-m-d');
                return [$date_from, $date_to];

            case 'this_month':
                $date_from = date('Y-m-01');
                $date_to = date('Y-m-d');
                return [$date_from, $date_to];

            case 'custom':
                $date_from = $_GET['date_from'] ?? date('Y-m-d');
                $date_to = $_GET['date_to'] ?? date('Y-m-d');
                // Sécurité : si l'utilisateur inverse les dates, on les remet dans l'ordre
                if (strtotime($date_from) > strtotime($date_to)) {
                    [$date_from, $date_to] = [$date_to, $date_from];
                }
                return [$date_from, $date_to];

            case 'today':
            default:
                $d = date('Y-m-d');
                return [$d, $d];
        }
    }
}
