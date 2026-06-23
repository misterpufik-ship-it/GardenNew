<?php
$f = file_get_contents( dirname( __DIR__ ) . '/couch/addons/kfunctions.php' );
preg_match( "/'home\.php' => array\('field'=>'label_home', 'title'=>'([^']+)'/", $f, $m );
echo "home: " . ( isset( $m[1] ) ? $m[1] : '?' ) . PHP_EOL;
preg_match( "/_garden_admiral_', '([^']+)'/", $f, $m2 );
echo "admiral: " . ( isset( $m2[1] ) ? $m2[1] : '?' ) . PHP_EOL;
