<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("CLI only\n");
}

chdir('/home/m/mrpuffch/garden-lounge.pro/public_html/admiralteyskaya');
$_SERVER['HTTP_HOST'] = 'garden-lounge.pro';
$_SERVER['REQUEST_URI'] = '/admiralteyskaya/booking-settings.php';
$_SERVER['SCRIPT_NAME'] = '/admiralteyskaya/booking-settings.php';
$_SERVER['SCRIPT_FILENAME'] = '/home/m/mrpuffch/garden-lounge.pro/public_html/admiralteyskaya/booking-settings.php';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

require 'couch/cms.php';
COUCH::invoke();
echo "registered\n";
