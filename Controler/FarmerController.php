<?php
session_start();
require_once __DIR__ . "/../config/db.php";

if (!isset($_GET['action'])) {
    die("No action defined");
}

switch ($_GET['action']) {
    case 'addFarm':
        require_once __DIR__ . "/../View/add_farm.php";
        break;

    default:
        echo "Invalid action";
}
