<?php
require_once __DIR__ . '/../config/database.php';
require_once 'session.php';

function loginUser($user){
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['full_name'];
    $_SESSION['user_role'] = $user['role'];
}

function logoutUser(){
    session_unset();
    session_destroy();
}

function isLoggedIn(){
    return isset($_SESSION['user_id']);
}

function isAdmin(){
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}
