<?php

/**
 * dashboardController — Tableau de bord (admin ou vendeur)
 */

class dashboardController extends DashboardPage
{
    public function index(): void
    {
        Auth::requireLogin();

        if (Auth::isAdmin()) {
            $model = new dashboardModel();

            $this->render(BASE_PATH . '/apps/views/dashboard/admin_content.php', [
                'userName' => $_SESSION['user_name'],
                'finance' => $model->financeSummary(),
                'lowStockCount' => $model->countLowStock(),
                'pendingWithdrawalsCount' => $model->countPendingWithdrawals(),
                'salesLast7Days' => $model->salesLast7Days(),
                'salesByCategory' => $model->salesByCategory(),
                'lowStockProducts' => $model->lowStockProducts(),
                'paidSales' => $model->paidSales(),
            ]);
        } else {
            $saleModel = new saleModel();

            $this->render(BASE_PATH . '/apps/views/dashboard/vendeur_content.php', [
                'userName' => $_SESSION['user_name'],
                'totalVentes' => $saleModel->totalPaidBySeller($_SESSION['user_id']),
                'totalVentesAujourdhui' => $saleModel->totalPaidTodayBySeller($_SESSION['user_id']),
                'ventesEnAttente' => $saleModel->countPendingBySeller($_SESSION['user_id']),
                'salesLast7Days' => $saleModel->salesLast7DaysBySeller($_SESSION['user_id']),
            ]);
        }
    }
}
