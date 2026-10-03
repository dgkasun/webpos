# WebPOS

## Browser-Based Point of Sale and Inventory Management System for Small Textile Retailers in Sri Lanka

WebPOS is a browser-based POS and inventory management system developed for my MSc Computer Science final project at the University of London.

The project focuses on small textile retailers in Sri Lanka that still use manual methods to record sales and manage their stock. WebPOS was developed as a simple alternative that can be accessed directly through a web browser. This also makes it easier to deploy and maintain the system without installing dedicated POS software on each shop computer.

## Features

- Product and category management
- Barcode generation and printing
- Supports unit and length based products such as fabric sold by the metre/yard
- POS screen with barcode scanning or name search, cart, and cash/card payments
- Automatic stock updates and low-stock alerts
- Printable receipts, sales history and sales reports
- Dashboard and user authentication
- Touchscreen friendly user interface

## Tech Stack

PHP (8.2.4) with PDO, MySQL, HTML, CSS, JavaScript and PHPUnit (11.5.56), using a Model-View-Controller (MVC) architecture with dependency injection.

## Hardware

The system was tested with:

- PM-BSD234 barcode scanner
- XP-Q838L printer
- Windows computer with touchscreen monitor

The barcode scanner does not need any special setup because it works like a normal keyboard. The receipt printer needs its driver installed on the computer. Once installed, the receipts can be printed using the browser.

## Getting Started

1. Clone the repository.
2. Create a MySQL database and import the supplied SQL file.
3. Create `config/config.php` with your database details:

```php
<?php

return [
    'database' => [
        'host' => '',
        'name' => '',
        'username' => '',
        'password' => ''
    ]
];
```

4. Create the administrator account:
   - Rename `create-admin.php_bk` to `create-admin.php`.
   - Set the admin username and password in the file.
   - Run it once in the browser.
   - Remove the credentials from the file and rename it back to `create-admin.php_bk`, or delete it.
5. Open the site in your browser.
6. Set up shop details in the shop settings before making the first sale.

## Running Tests

Automated tests can be run using PHPUnit:

```bash
composer install
./vendor/bin/phpunit
```

## Author

**D.G. Kasun Chamara**  
MSc Computer Science  
University of London  
2026
