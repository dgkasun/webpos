<?php

/**
 * Handles sale requests.
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
        // Get sales history data.
        $sales = $this->saleManager->getAll();

        $pageTitle = 'Sales History';
        $currentPage = 'Sales History';

        // Load the view.
        require __DIR__ . '/../views/sales/history.php';
    }
}
