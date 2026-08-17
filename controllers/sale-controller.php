<?php

/**
 * Handles sale and report requests between the model and views.
 * 
 * Ref: Fowler, M. (2003) - https://sar.ac.id/stmik_ebook/prog_file_file/EFCofwzsj0.pdf
 */

class SaleController
{
    private Sale $saleManager;

    public function __construct(Sale $saleManager)
    {
        $this->saleManager = $saleManager;
    }

    public function history(): void
    {
        // Get filter and pagination values
        $fromDate = trim($_GET['from_date'] ?? '');
        $toDate = trim($_GET['to_date'] ?? '');
        $saleId = (int) ($_GET['sale_id'] ?? 0);
        $page = (int) ($_GET['page'] ?? 1);

        if ($page < 1) {
            $page = 1;
        }

        $perPage = 10;

        // Calculate pagination
        $totalSales = $this->saleManager->getFilteredCount($fromDate, $toDate, $saleId);
        $totalPages = max(1, ceil($totalSales / $perPage));

        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $offset = ($page - 1) * $perPage;

        // Get sales for the current page
        $sales = $this->saleManager->getFiltered($fromDate, $toDate, $saleId, $perPage, $offset);

        $pageTitle = 'Sales History';
        $currentPage = 'Sales History';

        // Load the view
        require __DIR__ . '/../views/sales/history.php';
    }


    public function report(): void
    {
        // Get the selected date range
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
            // Get sales and summary data
            $sales = $this->saleManager->getByDateRange($fromDate, $toDate);
            $summary = $this->saleManager->getReportSummary($fromDate, $toDate);

            // Get the five best-selling products
            $bestSellingProducts = $this->saleManager->getBestSellingProducts($fromDate, $toDate);
        }

        $pageTitle = 'Sales Report';
        $currentPage = 'Reports';

        // Load the view
        require __DIR__ . '/../views/sales/report.php';
    }


    public function success(int $saleId): void
    {
        // Get the completed sale
        $sale = $this->saleManager->find($saleId);

        if (!$sale) {
            exit('Sale not found.');
        }

        // Get the items in the sale
        $saleItems = $this->saleManager->getItems($saleId);

        $numberOfItems = count($saleItems);
        $totalQuantity = 0;

        // Calculate the total quantity sold
        foreach ($saleItems as $item) {
            $totalQuantity += $item['quantity'];
        }

        // Get the previous page
        $from = $_GET['from'] ?? '';

        $pageTitle = 'Sale Complete';

        // Load the view
        require __DIR__ . '/../views/sales/success.php';
    }
}
