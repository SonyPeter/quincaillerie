<?php

/**
 * transactionController — Historique des transactions
 * (vendeur : ses propres ventes ; admin : toutes les transactions, avec l'auteur)
 */

class transactionController extends DashboardPage
{
    public function index(): void
    {
        Auth::requireLogin();

        $model = new transactionModel();

        $periode = $_GET['periode'] ?? '7j';
        $mois = $_GET['mois'] ?? date('Y-m');
        $annee = (int) ($_GET['annee'] ?? date('Y'));

        [$start, $end] = $this->plagePourPeriode($periode, $mois, $annee);

        $sellerId = Auth::isAdmin() ? null : $_SESSION['user_id'];

        $this->render(BASE_PATH . '/apps/views/dashboard/transactions.php', [
            'transactions' => $model->getTransactions($start, $end, $sellerId),
            'annees' => $model->anneesDisponibles(),
            'periode' => $periode,
            'mois' => $mois,
            'annee' => $annee,
        ]);
    }

    private function plagePourPeriode(string $periode, string $mois, int $annee): array
    {
        switch ($periode) {
            case 'mois_courant':
                $start = date('Y-m-01 00:00:00');
                $end = date('Y-m-d 23:59:59');
                break;

            case 'mois':
                $timestamp = strtotime($mois . '-01');
                $timestamp = $timestamp !== false ? $timestamp : time();
                $start = date('Y-m-01 00:00:00', $timestamp);
                $end = date('Y-m-t 23:59:59', $timestamp);
                break;

            case 'annee':
                $annee = $annee > 0 ? $annee : (int) date('Y');
                $start = "$annee-01-01 00:00:00";
                $end = "$annee-12-31 23:59:59";
                break;

            case '7j':
            default:
                $start = date('Y-m-d 00:00:00', strtotime('-6 days'));
                $end = date('Y-m-d 23:59:59');
                break;
        }

        return [$start, $end];
    }
}
