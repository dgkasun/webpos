<?php

/**
 * Handles checkout requests.
 */
class CheckoutController
{
    private Cart $cart;
    private Sale $saleManager;

    // Receive the Cart and Sale model.
    public function __construct(
        Cart $cart,
        Sale $saleManager
    ) {
        $this->cart = $cart;
        $this->saleManager = $saleManager;
    }

    public function index(): void
    {
        if ($this->cart->isEmpty()) {
            header('Location: pos.php');
            exit;
        }

        $error = '';

        $cartItems = $this->cart->getItems();
        $cartTotal = $this->cart->getTotal();
        $totalItems = $this->cart->getItemCount();
        $totalQuantity = $this->cart->getTotalQuantity();

        // Process the payment request.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $paymentMethod = $_POST['payment_method'] ?? 'cash';
            $cashReceived = $_POST['cash_received'] ?? '';

            if (!in_array($paymentMethod, ['cash', 'card'], true)) {
                $error = 'Please select a valid payment method.';
            } elseif (
                $paymentMethod === 'cash' &&
                (
                    $cashReceived === '' ||
                    !is_numeric($cashReceived) ||
                    $cashReceived < $cartTotal
                )
            ) {
                $error = 'Cash received must be equal to or greater than the total.';
            } else {
                $cashReceivedAmount = null;
                $changeAmount = null;

                // Calculate change for cash payments.
                if ($paymentMethod === 'cash') {
                    $cashReceivedAmount = $cashReceived;
                    $changeAmount = $cashReceivedAmount - $cartTotal;
                }

                try {
                    // Create the sale and update stock.
                    $saleId = $this->saleManager->create(
                        $_SESSION['user_id'],
                        $cartItems,
                        $cartTotal,
                        $paymentMethod,
                        $cashReceivedAmount,
                        $changeAmount
                    );
                    $this->cart->clear();
                    header('Location: sale-success.php?id=' . $saleId);
                    exit;
                } catch (Exception $e) {
                    $error = $e->getMessage();
                }
            }
        }

        $pageTitle = 'Checkout';

        // Load the checkout view.
        require __DIR__ . '/../views/checkout/index.php';
    }
}
