<?php

/**
 * Handles dashboard requests.
 * 
 * Ref: Fowler, M. (2003) - https://sar.ac.id/stmik_ebook/prog_file_file/EFCofwzsj0.pdf
 */
class DashboardController
{
    private Sale $saleManager;
    private Product $productManager;
    private Category $categoryManager;

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
        // Get dashboard data
        $todaySales = $this->saleManager->getTodaySummary();
        $productCount = $this->productManager->getCount();
        $categoryCount = $this->categoryManager->getCount();
        $lowStockCount = $this->productManager->getLowStockCount();

        $pageTitle = 'Dashboard';
        $currentPage = 'Dashboard';

        // Load the view
        require __DIR__ . '/../views/dashboard/index.php';
    }
}
