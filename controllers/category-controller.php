<?php

/**
 * Handles category requests between the model and views.
 * 
 * Ref: Fowler, M. (2003) - https://sar.ac.id/stmik_ebook/prog_file_file/EFCofwzsj0.pdf
 */

class CategoryController
{
    private Category $categoryManager;

    public function __construct(Category $categoryManager)
    {
        $this->categoryManager = $categoryManager;
    }

    public function index(): void
    {
        $message = '';
        $error = '';

        // Process the add category request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');

            if ($name === '') {
                $error = 'Category name is required.';
            } else {
                try {
                    $this->categoryManager->create($name);
                    $message = 'Category added successfully.';
                } catch (PDOException $e) {
                    // Check for a duplicate category
                    if ($e->getCode() === '23000') {
                        $error = 'This category already exists.';
                    } else {
                        $error = 'Unable to add the category.';
                    }
                }
            }
        }

        // Get all categories
        $categories = $this->categoryManager->getAll();

        $pageTitle = 'Categories';
        $currentPage = 'Categories';

        // Load the view
        require __DIR__ . '/../views/categories/index.php';
    }

    public function edit(int $categoryId): void
    {
        $message = '';
        $error = '';

        // Get the selected category
        $category = $this->categoryManager->find($categoryId);

        if (!$category) {
            exit('Category not found.');
        }

        // Process the update category request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $isActive = isset($_POST['is_active']) ? 1 : 0;

            if ($name === '') {
                $error = 'Category name is required.';
            } else {
                try {
                    $this->categoryManager->update($categoryId, $name, $isActive);
                    $message = 'Category updated successfully.';

                    // Get the updated category
                    $category = $this->categoryManager->find($categoryId);
                } catch (PDOException $e) {
                    // Check for a duplicate category
                    if ($e->getCode() === '23000') {
                        $error = 'This category already exists.';
                    } else {
                        $error = 'Unable to update the category.';
                    }
                }
            }
        }

        $pageTitle = 'Edit Category';
        $currentPage = 'Categories';

        // Load the edit view
        require __DIR__ . '/../views/categories/edit.php';
    }
}
