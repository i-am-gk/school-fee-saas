<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return $_SESSION['role'] === 'admin';
}

function isParent() {
    return $_SESSION['role'] === 'parent';
}
?>
