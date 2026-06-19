<?php
$config = '/home/m/mrpuffch/garden-lounge.pro/public_html/admiralteyskaya/couch/config.php';
define('K_COUCH_DIR', dirname($config) . '/');
require_once $config;
require_once '/home/m/mrpuffch/garden-lounge.pro/public_html/admiralteyskaya/couch/addons/garden-booking/booking-lib.php';

$options = getopt('', array(
    'adm-token:',
    'udel-token:',
    'test-send-admiral',
    'test-send-udelnaya',
));

if (!empty($options['adm-token'])) echo "adm-token-opt\n";
if (isset($options['test-send-admiral'])) echo "test-admiral-flag\n";
