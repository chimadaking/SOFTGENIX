<?php
// Load configuration
require_once __DIR__ . '/config/constants.php';

// bootstrap.php 
/**
 * Generate a URL for the application
 * 
 * @param string $path The path to generate a URL for
 * @return string The generated URL
 */
if (!function_exists('site_url')) {
    function site_url(string $path = ''): string {
        $path = trim($path, '/');
        $base = rtrim(APP_URL, '/');
        
        if ($path === '') {
            return $base . '/';
        }
        
        if (defined('USE_REWRITE') && USE_REWRITE) {
            return $base . '/' . $path;
        }
        
        return $base . '/index.php?url=' . $path;
    }
}

// Start session
session_start();

// Load Config
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/helpers.php';

// Autoload Core Libraries
spl_autoload_register(function ($className) {
    $className = ltrim($className, '\\');

    // Only autoload App namespace
    if (strpos($className, 'App\\') !== 0) {
        return;
    }

    // Remove "App\"
    $relative = substr($className, 4);

    // Convert namespace to path
    $path = str_replace('\\', '/', $relative);

    // DO NOT lowercase folders — preserve real structure
    $file = __DIR__ . '/app/' . $path . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});
