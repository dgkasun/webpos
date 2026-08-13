<?php

require_once __DIR__ . '/config/load.php';

session_unset();
session_destroy();

header('Location: login.php');
exit;
