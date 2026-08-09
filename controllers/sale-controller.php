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
}
