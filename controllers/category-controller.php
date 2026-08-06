<?php

/**
 * Handles category requests between the model and views.
 * Ref: Fowler, M. (2002) Patterns of Enterprise Application Architecture - https://sar.ac.id/stmik_ebook/prog_file_file/EFCofwzsj0.pdf
 * Ref: Fowler, M. (2004) Inversion of Control Containers and the Dependency Injection pattern - https://martinfowler.com/articles/injection.html
 */

class CategoryController
{
    private Category $categoryManager;

    // Receive the Category model.
    public function __construct(Category $categoryManager)
    {
        $this->categoryManager = $categoryManager;
    }

    public function index(): void
    {
        $message = '';
        $error = '';

        // Process the add category request.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if ($name === '') {
                $error = 'Category name is required.';
            } else {
                try {
                    $this->categoryManager->create($name, $description);
                    $message = 'Category added successfully.';
                } catch (PDOException $e) {
                    if ($e->getCode() === '23000') {
                        $error = 'This category already exists.';
                    } else {
                        $error = 'Unable to add the category.';
                    }
                }
            }
        }

        // Get category data.
        $categories = $this->categoryManager->getAll();

        $pageTitle = 'Categories';
        $currentPage = 'Categories';

        // Load the view.
        require __DIR__ . '/../views/categories/index.php';
    }
}
