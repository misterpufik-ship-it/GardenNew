<?php
if ( !defined('K_COUCH_DIR') ) die();

function garden_clear_couch_cache(){
    global $FUNCS;

    $cacheDir = K_COUCH_DIR . 'cache';
    $removed = 0;

    if ( is_dir($cacheDir) ){
        $items = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($cacheDir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ( $items as $item ){
            $path = $item->getPathname();

            if ( $item->isDir() ){
                if ( $path !== $cacheDir && basename($path) !== 'booking-throttle' ){
                    @rmdir($path);
                }
                continue;
            }

            if ( basename($path) === '.htaccess' ){
                continue;
            }

            if ( @unlink($path) ){
                $removed++;
            }
        }
    }

    if ( isset($FUNCS) && method_exists($FUNCS, 'invalidate_cache') ){
        $FUNCS->invalidate_cache();
    }

    return $removed;
}
