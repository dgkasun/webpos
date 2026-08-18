<?php

/**
 * Handles settings requests between the model and views.
 *
 * Ref: Fowler, M. (2003) - https://sar.ac.id/stmik_ebook/prog_file_file/EFCofwzsj0.pdf
 */

class SettingController
{
    private Setting $settingManager;

    public function __construct(Setting $settingManager)
    {
        $this->settingManager = $settingManager;
    }

    public function index()
    {
        $message = '';
        $error = '';

        $settings = $this->settingManager->get();

        if (!$settings) {
            $settings = [
                'shop_name' => '',
                'address' => '',
                'phone' => '',
                'print_footer' => '',
            ];
        }

        // Process the settings request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $shopName = trim($_POST['shop_name'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $printFooter = trim($_POST['print_footer'] ?? '');

            if ($shopName === '') {
                $error = 'Shop name is required.';
            } else {
                try {
                    $this->settingManager->save($shopName, $address, $phone, $printFooter);

                    $message = 'Settings saved successfully.';

                    // Get the saved settings
                    $settings = $this->settingManager->get();
                } catch (PDOException $e) {
                    $error = 'Unable to save settings.';
                }
            }
        }

        $pageTitle = 'Settings';
        $currentPage = 'Settings';

        require __DIR__ . '/../views/settings/index.php';
    }
}
