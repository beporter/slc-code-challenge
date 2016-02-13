<?php
/**
 * Shim for loading composer's autload file when available.
 */

$composer = dirname(__FILE__) . '/vendor/autoload.php';
if ( file_exists($composer) ) {
    require_once $composer;
}
