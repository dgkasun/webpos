<?php

/**
 * Handles dashboard requests.
 */
class DashboardController
{
    private Sale $saleManager;
    private Product $productManager;
    private Category $categoryManager;

    // Receive the required models.
    public function __construct(
        Sale $saleManager,
        Product $productManager,
        Category $categoryManager
    ) {
        $this->saleManager = $saleManager;
        $this->productManager = $productManager;
        $this->categoryManager = $categoryManager;
    }

    public function index(): void
    {
        // Get dashboard data.
        $todaySales = $this->saleManager->getTodaySummary();
        $productCount = $this->productManager->getCount();
        $categoryCount = $this->categoryManager->getCount();
        $lowStockCount = $this->productManager->getLowStockCount();

        $pageTitle = 'Dashboard';
        $currentPage = 'Dashboard';

        // Load the view.
        require __DIR__ . '/../views/dashboard/index.php';
    }
}
