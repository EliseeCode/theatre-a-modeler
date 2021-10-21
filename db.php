<?php
// Autoloader
if (file_exists('vendor/autoload.php')) {
    require_once('vendor/autoload.php');
}

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
//Database connection settings

$host = $_ENV["DB_HOST"];
$user = $_ENV["DB_USER"];
$pass = $_ENV["DB_PASSWORD"];
$db = $_ENV["DB_NAME"];

mysqli_report(MYSQLI_REPORT_ERROR);
$mysqli = new mysqli($host,$user,$pass,$db) or die($mysqli->error);
$mysqli->set_charset("utf8");

