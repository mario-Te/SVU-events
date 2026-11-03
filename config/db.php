<?php
// =====================================
// Global Website Configuration
// =====================================

// Local project URL
$base_url = '/SVU-Events-Guide-master/';

// =====================================
// TiDB Cloud Connection - PDO
// =====================================

$dbHost = 'gateway01.ap-northeast-1.prod.aws.tidbcloud.com';
$dbPort = 4000;
$dbName = 'svu_events';
$dbUser = '3hhCjrT3aEC1aw3.root';

// Replace with your NEW TiDB password after rotating credentials
$dbPassword = 'kqXx55qSEnA9S7Ym';
$caCertificate = dirname(__DIR__) . '/isrgrootx1.pem';

try {

    if (!extension_loaded('pdo_mysql')) {
        throw new RuntimeException('PHP PDO MySQL extension is not enabled.');
    }

    if (!file_exists($caCertificate)) {
        throw new RuntimeException('SSL certificate not found: ' . $caCertificate);
    }

    $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_SSL_CA => $caCertificate,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true,
    ];

    $pdo = new PDO($dsn, $dbUser, $dbPassword, $options);

} catch (Throwable $e) {

    error_log('TiDB connection error: ' . $e->getMessage());

    http_response_code(500);

    die(
        'Database connection failed: ' .
        htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')
    );
}