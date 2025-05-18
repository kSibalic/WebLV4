<?php
session_start();

function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /lv2/public/auth/login.php");
        exit;
    }
}

function require_admin() {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header("Location: /lv2/public/auth/login.php");
        exit;
    }
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function get_current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

function get_current_username() {
    return $_SESSION['username'] ?? null;
} 