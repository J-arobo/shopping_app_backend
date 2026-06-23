<?php
/**
 * Patches PayPal REST SDK for PHP 8 compatibility.
 * count()/sizeof() on non-array values throws TypeError in PHP 8.
 * This script is invoked automatically via composer post-install-cmd.
 */
$file = __DIR__ . '/../vendor/paypal/rest-api-sdk-php/lib/PayPal/Common/PayPalModel.php';

if (!file_exists($file)) {
    echo "PayPalModel.php not found, skipping patch.\n";
    exit(0);
}

$content = file_get_contents($file);

$patched = str_replace(
    'is_array($v) && count($v) <= 0',
    'is_array($v) && empty($v)',
    $content
);
$patched = str_replace(
    'is_array($v) && sizeof($v) <= 0',
    'is_array($v) && empty($v)',
    $patched
);

if ($patched === $content) {
    echo "PayPalModel.php already patched or pattern not found.\n";
    exit(0);
}

file_put_contents($file, $patched);
echo "Patched PayPalModel.php for PHP 8 compatibility.\n";
