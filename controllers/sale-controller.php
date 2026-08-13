<?php

/**
 * Handles sale and report requests.
 */
class SaleController
{
    private Sale $saleManager;

    // Receive the Sale model.
    public function __construct(Sale $saleManager)
    {
        $this->saleManager = $saleManager;
    }

    public function history(): void
    {
        $fromDate = trim($_GET['from_date'] ?? '');
        $toDate = trim($_GET['to_date'] ?? '');
        $saleId = (int) ($_GET['sale_id'] ?? 0);
        $page = (int) ($_GET['page'] ?? 1);

        if ($page < 1) {
            $page = 1;
        }

        $perPage = 10;

        $totalSales = $this->saleManager->getFilteredCount($fromDate, $toDate, $saleId);

        $totalPages = max(1, ceil($totalSales / $perPage));

        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $offset = ($page - 1) * $perPage;

        // Get sales for the current page.
        $sales = $this->saleManager->getFiltered($fromDate, $toDate, $saleId, $perPage, $offset);

        $pageTitle = 'Sales History';
        $currentPage = 'Sales History';

        // Load the view.
        require __DIR__ . '/../views/sales/history.php';
    }

    public function receipt(int $saleId): void
    {
        // Get sale and sale item data.
        $sale = $this->saleManager->find($saleId);
        $saleItems = $this->saleManager->getItems($saleId);

        if (!$sale) {
            exit('Sale not found.');
        }

        $pageTitle = 'Receipt';
        $currentPage = 'Sales History';

        // Load the receipt view.
        require __DIR__ . '/../views/sales/receipt.php';
    }

    public function report(): void
    {
        $fromDate = $_GET['from_date'] ?? date('Y-m-01');
        $toDate = $_GET['to_date'] ?? date('Y-m-d');

        $error = '';
        $sales = [];
        $summary = [
            'sale_count' => 0,
            'sales_total' => 0,
            'items_sold' => 0,
        ];
        $bestSellingProducts = [];

        if ($fromDate > $toDate) {
            $error = 'From date cannot be later than To date.';
        } else {
            // Get sales and summary data for the selected date range.
            $sales = $this->saleManager->getByDateRange($fromDate, $toDate);
            $summary = $this->saleManager->getReportSummary($fromDate, $toDate);

            // Get the five best-selling products.
            $bestSellingProducts = $this->saleManager->getBestSellingProducts($fromDate, $toDate);
        }

        $pageTitle = 'Sales Report';
        $currentPage = 'Reports';

        // Load the report view.
        require __DIR__ . '/../views/sales/report.php';
    }


    public function success(int $saleId): void
    {
        // Get completed sale data.
        $sale = $this->saleManager->find($saleId);
        $saleItems = $this->saleManager->getItems($saleId);

        if (!$sale) {
            exit('Sale not found.');
        }

        $numberOfItems = count($saleItems);
        $totalQuantity = 0;

        foreach ($saleItems as $item) {
            $totalQuantity += $item['quantity'];
        }

        $pageTitle = 'Sale Complete';

        // Load the sale success view.
        require __DIR__ . '/../views/sales/success.php';
    }
}
