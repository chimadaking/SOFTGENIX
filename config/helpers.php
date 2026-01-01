<?php
// config/helpers.php

/**
 * Generate a URL using BASE_DIR
 * @param string $path The path to append to BASE_DIR (e.g., '/auth/login')
 * @return string The full URL
 */
function url(string $path = ''): string {
    return BASE_DIR . '/' . ltrim($path, '/');
}

/**
 * Redirect to a page using site_url (respects USE_REWRITE setting)
 * @param string $page The page to redirect to (e.g., 'auth/login')
 */
function redirect($page) {
    $url = site_url($page);
    
    // Debug output
    error_log("=== REDIRECT DEBUG ===");
    error_log("Page: " . $page);
    error_log("BASE_DIR: " . BASE_DIR);
    error_log("APP_URL: " . APP_URL);
    error_log("USE_REWRITE: " . (defined('USE_REWRITE') ? (USE_REWRITE ? 'true' : 'false') : 'NOT DEFINED'));
    error_log("Generated URL: " . $url);
    error_log("=== END DEBUG ===");
    
    header('Location: ' . $url);
    exit;
}

function flash($name = '', $message = '', $class = 'alert alert-success') {
    if (!empty($name)) {
        if (!empty($message) && empty($_SESSION[$name])) {
            if (!empty($_SESSION[$name])) {
                unset($_SESSION[$name]);
            }
            if (!empty($_SESSION[$name . '_class'])) {
                unset($_SESSION[$name . '_class']);
            }
            $_SESSION[$name] = $message;
            $_SESSION[$name . '_class'] = $class;
        } elseif (empty($message) && !empty($_SESSION[$name])) {
            $class = !empty($_SESSION[$name . '_class']) ? $_SESSION[$name . '_class'] : '';
            echo '<div class="' . $class . '" id="msg-flash">' . $_SESSION[$name] . '</div>';
            unset($_SESSION[$name]);
            unset($_SESSION[$name . '_class']);
        }
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUser() {
    if (!isLoggedIn()) {
        return null;
    }
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'email' => $_SESSION['user_email'] ?? null,
        'name' => $_SESSION['user_name'] ?? null,
        'role' => $_SESSION['user_role'] ?? null
    ];
}

function getUserName() {
    return $_SESSION['user_name'] ?? '';
}

function redirectIfNotAuthenticated($redirectTo = 'auth/login') {
    if (!isLoggedIn()) {
        redirect($redirectTo);
    }
}

function isAdmin() {
    return isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 1);
}

function h($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
