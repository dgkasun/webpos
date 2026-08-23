<?php
/*
ref https://github.com/lindell/JsBarcode */

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/product.php';

// Get the product ID from the URL
$productId = (int) ($_GET['id'] ?? 0);

// Find the selected product
$product = new Product($conn);
$productData = $product->find($productId);

// Stop if the product cannot be found
if (!$productData) {
    die('Product not found.');
}

$copies = (int) ($_GET['copies'] ?? 1);

if ($copies < 1) {
    $copies = 1;
}

if ($copies > 50) {
    $copies = 50;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Print Barcode</title>

    <!-- JsBarcode library -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .print-options {
            margin-bottom: 20px;
        }

        .barcode-labels {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .barcode-label {
            padding: 2mm;
            box-sizing: border-box;
            text-align: center;
            border: 1px solid #ccc;
            width: 80mm;
            height: 27mm;
        }

        .product-name {
            font-size: 13px;
            font-weight: bold;
            margin: 0;
        }

        .barcode {
            display: block;
            margin: 1mm auto 0;
        }

        .barcode-number {
            font-size: 11px;
            margin: 5px;
        }

        .product-price {
            font-size: 12px;
            margin: 0;
        }

        @media print {

            @page {
                margin: 0;
            }

            html,
            body {

                margin: 0;
                padding: 0;
            }

            .print-options {
                display: none;
            }

            .barcode-labels {
                margin: 0;
                padding: 0;
                display: block;
            }

            .barcode-label {
                padding: 2mm;
                margin: 0;
                box-sizing: border-box;
                border: none;
                text-align: center;
            }
        }
    </style>
</head>

<body>

    <div class="print-options">
        <form method="GET">
            <input type="hidden" name="id" value="<?= (int) $productData['id']; ?>">
            <label for="copies">Number of labels:</label>
            <input type="number" id="copies" name="copies" min="1" max="50" value="<?= $copies; ?>">
            <button type="submit">Update</button>
            <button type="button" onclick="window.print()">Print Barcode</button>
        </form>
    </div>

    <div class="barcode-labels">
        <?php for ($i = 0; $i < $copies; $i++): ?>
            <div class="barcode-label">
                <div class="product-name">
                    <?= htmlspecialchars($productData['name']); ?>
                </div>
                <svg class="barcode" data-barcode="<?= htmlspecialchars($productData['barcode']); ?>"></svg>
                <div class="barcode-number">
                    <?= htmlspecialchars($productData['barcode']); ?>
                </div>
                <div class="product-price">
                    <?= htmlspecialchars($productData['selling_price']); ?>
                </div>
            </div>
        <?php endfor; ?>
    </div>

    <script>
        // Convert each stored barcode value into a CODE128 barcode
        document.querySelectorAll('.barcode').forEach(function(barcode) {
            JsBarcode(barcode, barcode.dataset.barcode, {
                format: 'CODE128',
                width: 1.5,
                height: 35,
                displayValue: false,
                margin: 0
            });
        });
    </script>

</body>

</html>