<?php

$caPath = getenv('MYSQL_ATTR_SSL_CA');
if (empty($caPath)) {
    echo "MYSQL_ATTR_SSL_CA is not set. Skipping SSL check.\n";
    exit(0);
}

if (!file_exists($caPath) || !is_readable($caPath)) {
    fwrite(STDERR, "Error: MySQL CA certificate cannot be read at {$caPath}\n");
    exit(1);
}

$content = file_get_contents($caPath);
if (strpos($content, 'BEGIN CERTIFICATE') === false) {
    fwrite(STDERR, "Error: Invalid CA certificate format at {$caPath}\n");
    exit(1);
}

echo "MySQL CA certificate verified and readable.\n";
exit(0);
